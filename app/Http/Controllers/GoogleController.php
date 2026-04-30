<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleController
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $propertyId;
    private $accessToken;
    private $refreshToken;
    private $tokenExpiresAt;

    public function __construct()
    {
        try {
            $setting = Setting::singleton();
            $mb = $setting->magicbox ?? [];

            $this->clientId = data_get($mb, 'google.oauth_client_id');
            $this->clientSecret = data_get($mb, 'google.oauth_client_secret');
            $this->redirectUri = data_get($mb, 'google.oauth_redirect_url');
            $this->propertyId = data_get($mb, 'google.property_id');
            $this->accessToken = data_get($mb, 'google.access_token');
            $this->refreshToken = data_get($mb, 'google.refresh_token');
            $this->tokenExpiresAt = data_get($mb, 'google.token_expires_at');

            // Token'ı otomatik yenile (gerekirse) - sadece token varsa
            if ($this->accessToken) {
                $this->ensureValidToken();
            }
        } catch (\Exception $e) {
            Log::error('GoogleController constructor hatası: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // OAuth: Connect / Callback / Logout
    // ─────────────────────────────────────────────

    private function googleProvider()
    {
        return Socialite::buildProvider(
            GoogleProvider::class,
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect' => $this->redirectUri,
            ]
        );
    }

    public function googleConnect()
    {
        session(['redirect_url' => url()->previous()]);

        return $this->googleProvider()
            ->scopes([
                'https://www.googleapis.com/auth/analytics.readonly',
                'https://www.googleapis.com/auth/analytics',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
            ])
            ->redirect();
    }

    public function googleCallback()
    {
        try {
            $googleUser = $this->googleProvider()
                ->stateless()
                ->user();

            $this->accessToken = $googleUser->token ?? null;
            $this->refreshToken = $googleUser->refreshToken ?? null;
            $this->tokenExpiresAt = isset($googleUser->expiresIn)
                ? now()->addSeconds($googleUser->expiresIn)->toDateTimeString()
                : now()->addHour()->toDateTimeString();

            session([
                'google_access_token' => $this->accessToken,
                'google_user_email' => $googleUser->email ?? null,
                'google_user_name' => $googleUser->name ?? null,
            ]);

            $this->saveTokensToDatabase();

            return redirect(session('redirect_url', route('dashboard')));
        } catch (\Exception $e) {
            Log::error('Google Callback hatası: ' . $e->getMessage());
            return redirect(route('dashboard'));
        }
    }

    public function googleLogout()
    {
        session()->forget('google_access_token');
        session()->forget('google_user_email');
        session()->forget('google_user_name');

        try {
            $setting = Setting::singleton();
            $mb = $setting->magicbox ?? [];

            data_set($mb, 'google.access_token', null);
            data_set($mb, 'google.refresh_token', null);
            data_set($mb, 'google.token_expires_at', null);

            $setting->magicbox = $mb;
            $setting->save();
        } catch (\Exception $e) {
            Log::error('Google Logout hatası: ' . $e->getMessage());
        }

        return redirect()->back()->with('toast', [
            'type' => 'success',
            'title' => 'Google bağlantısı kaldırıldı',
            'message' => 'Google bağlantınız başarıyla kaldırıldı.',
        ]);
    }

    // ─────────────────────────────────────────────
    // Token Yönetimi
    // ─────────────────────────────────────────────

    private function ensureValidToken()
    {
        if (!$this->accessToken || $this->isTokenExpired()) {
            $this->reloadTokensFromSettings();

            if (!$this->accessToken || $this->isTokenExpired()) {
                $this->refreshAccessToken();
            }
        }

        if ($this->accessToken) {
            session(['google_access_token' => $this->accessToken]);
        }
    }

    private function reloadTokensFromSettings()
    {
        try {
            $setting = Setting::singleton()->fresh();
            $mb = $setting->magicbox ?? [];

            $this->accessToken = data_get($mb, 'google.access_token');
            $this->refreshToken = data_get($mb, 'google.refresh_token');
            $this->tokenExpiresAt = data_get($mb, 'google.token_expires_at');
        } catch (\Exception $e) {
            Log::error('Token\'lar okunamadı: ' . $e->getMessage());
        }
    }

    private function isTokenExpired()
    {
        if (!$this->tokenExpiresAt) {
            return true;
        }

        try {
            return Carbon::parse($this->tokenExpiresAt)->isPast();
        } catch (\Exception $e) {
            return true;
        }
    }

    private function refreshAccessToken()
    {
        try {
            if (!$this->refreshToken) {
                Log::warning('Refresh token bulunamadı. Yeniden Google ile giriş yapılması gerekiyor.');
                return false;
            }

            $client = new Client();
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $this->refreshToken,
                    'grant_type' => 'refresh_token',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['access_token'])) {
                $this->accessToken = $data['access_token'];
                $this->tokenExpiresAt = now()->addSeconds($data['expires_in'])->toDateTimeString();
                $this->saveTokensToDatabase();
                Log::info('Google Access Token başarıyla yenilendi.');
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Token yenileme hatası: ' . $e->getMessage());
            return false;
        }
    }

    private function saveTokensToDatabase()
    {
        try {
            $setting = Setting::singleton();
            $mb = $setting->magicbox ?? [];

            data_set($mb, 'google.access_token', $this->accessToken);
            data_set($mb, 'google.refresh_token', $this->refreshToken);
            data_set($mb, 'google.token_expires_at', $this->tokenExpiresAt);

            $setting->magicbox = $mb;
            $setting->save();

            Log::info('Google token\'lar başarıyla kaydedildi.');
        } catch (\Exception $e) {
            Log::error('Token veritabanına kaydedilemedi: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // Ana İstatistik Endpoint'i (Dashboard)
    // ─────────────────────────────────────────────

    public function getHomepageStats(Request $request)
    {
        try {
            $client = new Client([
                'base_uri' => 'https://analyticsdata.googleapis.com/v1beta/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                ],
            ]);

            $fallback = [
                [
                    'pageTitle' => 'Veri Yok!',
                    'activeUsers' => 0,
                    'screenPageViews' => 0,
                    'date' => now()->format('Y-m-d'),
                ],
            ];

            $countryData = $this->fetchCountryStats($client, $this->propertyId);
            $dailyData = $this->fetchDailyTrendData($client, $this->propertyId, 30);
            $last1Day = $this->fetchVisitorsAndPageViews($client, $this->propertyId, 1);
            $last7Day = $this->fetchVisitorsAndPageViews($client, $this->propertyId, 7);
            $last30Day = $this->fetchVisitorsAndPageViews($client, $this->propertyId, 30);
            $performanceMetrics = $this->fetchPerformanceMetrics($client, $this->propertyId, 30);
            $deviceData = $this->fetchDeviceStats($client, $this->propertyId, 30);
            $browserData = $this->fetchBrowserStats($client, $this->propertyId, 30);
            $topPages = $this->fetchTopPages($client, $this->propertyId, 30);

            $result = (object) [
                'countries' => $countryData,
                'last1Day' => $last1Day ?: $fallback,
                'last7Day' => $last7Day ?: $fallback,
                'last30Day' => $topPages ?: $fallback,
                'dailyTrend' => $dailyData,
                'performance' => $performanceMetrics,
                'devices' => $deviceData,
                'browsers' => $browserData,
                'topPages' => $topPages,
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Homepage Stats Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Veri çekme hatası',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // Trend Data (Özel tarih aralığı)
    // ─────────────────────────────────────────────

    public function getTrendData(Request $request)
    {
        try {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $metric = $request->input('metric', 'views');

            if (!$startDate || !$endDate) {
                return response()->json(['error' => 'Başlangıç ve bitiş tarihleri gerekli'], 400);
            }

            if (strtotime($startDate) > strtotime($endDate)) {
                return response()->json(['error' => 'Başlangıç tarihi, bitiş tarihinden büyük olamaz'], 400);
            }

            $client = new Client([
                'base_uri' => 'https://analyticsdata.googleapis.com/v1beta/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                ],
            ]);

            $daysDiff = ceil((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24));
            $dailyData = $this->fetchDailyTrendDataCustom($client, $this->propertyId, $startDate, $endDate);

            return response()->json([
                'dailyTrend' => $dailyData,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'days' => $daysDiff + 1,
                'metric' => $metric,
            ]);
        } catch (\Exception $e) {
            Log::error('Trend Data Error: ' . $e->getMessage());
            return response()->json(['error' => 'Veri çekme hatası', 'message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // Realtime Active Users
    // ─────────────────────────────────────────────

    public function fetchRealtimeActiveUsers()
    {
        try {
            $this->ensureValidToken();

            if (!$this->accessToken) {
                return response()->json([
                    'auth_error' => true,
                    'message' => 'Google erişim tokenı bulunamadı. Lütfen yeniden bağlanın.',
                ], 401);
            }

            if (!$this->propertyId) {
                return response()->json(['error' => 'Google Analytics property ID tanımlı değil.'], 400);
            }

            $client = new Client([
                'base_uri' => 'https://analyticsdata.googleapis.com/v1beta/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'timeout' => 15,
            ]);

            $totalJson = json_decode(
                $client->post("properties/{$this->propertyId}:runRealtimeReport", [
                    'json' => ['metrics' => [['name' => 'activeUsers']]],
                ])->getBody()->getContents(),
                true
            ) ?: [];

            $last30MinutesTotal = 0;
            if (!empty($totalJson['rows'][0]['metricValues'][0]['value'])) {
                $last30MinutesTotal = (int) $totalJson['rows'][0]['metricValues'][0]['value'];
            }

            $dimJson = json_decode(
                $client->post("properties/{$this->propertyId}:runRealtimeReport", [
                    'json' => [
                        'dimensions' => [['name' => 'minutesAgo']],
                        'metrics' => [['name' => 'activeUsers']],
                    ],
                ])->getBody()->getContents(),
                true
            ) ?: [];

            $last5MinutesTotal = 0;
            $chartData = array_fill(0, 30, 0);

            if (!empty($dimJson['rows']) && is_array($dimJson['rows'])) {
                foreach ($dimJson['rows'] as $row) {
                    $minutesAgo = (int) ($row['dimensionValues'][0]['value'] ?? 0);
                    $activeUsers = (int) ($row['metricValues'][0]['value'] ?? 0);

                    if ($minutesAgo < 5) {
                        $last5MinutesTotal += $activeUsers;
                    }
                    if ($minutesAgo >= 0 && $minutesAgo < 30) {
                        $chartData[$minutesAgo] = $activeUsers;
                    }
                }
            }

            return response()->json([
                'last_30_minutes' => $last30MinutesTotal,
                'last_5_minutes' => $last5MinutesTotal,
                'chart_data' => $chartData,
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $status = $e->getCode();
            $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';

            if ($status === 401 || str_contains($body, 'invalid_grant')) {
                return response()->json(['auth_error' => true, 'message' => 'Oturum süresi dolmuş.'], 401);
            }

            return response()->json(['error' => 'Analytics gerçek zamanlı API hatası', 'details' => $body], $status >= 400 && $status < 600 ? $status : 500);
        } catch (\Exception $e) {
            Log::error('fetchRealtimeActiveUsers: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // GA4 Data Fetch Helpers
    // ─────────────────────────────────────────────

    public function fetchCountryStats($client, $propertyId)
    {
        $body = [
            'dateRanges' => [['startDate' => now()->subDays(30)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d')]],
            'dimensions' => [['name' => 'country'], ['name' => 'countryId']],
            'metrics' => [['name' => 'screenPageViews']],
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);
        $countries = [];

        if (!empty($result['rows'])) {
            foreach ($result['rows'] as $row) {
                $countries[] = [
                    'country' => $row['dimensionValues'][0]['value'],
                    'countryCode' => $row['dimensionValues'][1]['value'],
                    'screenPageViews' => $row['metricValues'][0]['value'],
                ];
            }
        }

        return $countries;
    }

    public function fetchVisitorsAndPageViews($client, $propertyId, $days)
    {
        $body = [
            'dateRanges' => [['startDate' => now()->subDays($days)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d')]],
            'dimensions' => [['name' => 'pageTitle'], ['name' => 'pagePath']],
            'metrics' => [['name' => 'activeUsers'], ['name' => 'screenPageViews']],
            'orderBys' => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
            'limit' => 10,
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);
        $pageStats = [];

        if (!empty($result['rows'])) {
            foreach ($result['rows'] as $row) {
                $pageStats[] = [
                    'pageTitle' => $row['dimensionValues'][0]['value'],
                    'pagePath' => $row['dimensionValues'][1]['value'],
                    'activeUsers' => (int) $row['metricValues'][0]['value'],
                    'screenPageViews' => (int) $row['metricValues'][1]['value'],
                ];
            }
        }

        return $pageStats;
    }

    public function fetchDailyTrendData($client, $propertyId, $days)
    {
        $body = [
            'dateRanges' => [['startDate' => now()->subDays($days)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d')]],
            'dimensions' => [['name' => 'date']],
            'metrics' => [
                ['name' => 'activeUsers'],
                ['name' => 'screenPageViews'],
                ['name' => 'sessions'],
                ['name' => 'newUsers'],
            ],
            'orderBys' => [['dimension' => ['dimensionName' => 'date'], 'desc' => false]],
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);
        $dailyData = [];

        if (!empty($result['rows'])) {
            foreach ($result['rows'] as $row) {
                $date = $row['dimensionValues'][0]['value'];
                $formattedDate = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
                $dailyData[] = [
                    'date' => $formattedDate,
                    'activeUsers' => (int) $row['metricValues'][0]['value'],
                    'screenPageViews' => (int) $row['metricValues'][1]['value'],
                    'sessions' => (int) $row['metricValues'][2]['value'],
                    'newUsers' => (int) $row['metricValues'][3]['value'],
                ];
            }
        }

        return $dailyData;
    }

    public function fetchDailyTrendDataCustom($client, $propertyId, $startDate, $endDate)
    {
        $body = [
            'dateRanges' => [['startDate' => $startDate, 'endDate' => $endDate]],
            'dimensions' => [['name' => 'date']],
            'metrics' => [
                ['name' => 'activeUsers'],
                ['name' => 'screenPageViews'],
                ['name' => 'sessions'],
                ['name' => 'newUsers'],
            ],
            'orderBys' => [['dimension' => ['dimensionName' => 'date'], 'desc' => false]],
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);
        $dailyData = [];

        if (!empty($result['rows'])) {
            foreach ($result['rows'] as $row) {
                $date = $row['dimensionValues'][0]['value'];
                $formattedDate = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
                $dailyData[] = [
                    'date' => $formattedDate,
                    'activeUsers' => (int) $row['metricValues'][0]['value'],
                    'screenPageViews' => (int) $row['metricValues'][1]['value'],
                    'sessions' => (int) $row['metricValues'][2]['value'],
                    'newUsers' => (int) $row['metricValues'][3]['value'],
                ];
            }
        }

        return $dailyData;
    }

    public function fetchPerformanceMetrics($client, $propertyId, $days)
    {
        $body = [
            'dateRanges' => [['startDate' => now()->subDays($days)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d')]],
            'metrics' => [
                ['name' => 'screenPageViewsPerSession'],
                ['name' => 'averageSessionDuration'],
                ['name' => 'bounceRate'],
                ['name' => 'activeUsers'],
                ['name' => 'screenPageViews'],
                ['name' => 'sessions'],
            ],
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);

        if (!empty($result['rows'][0])) {
            $row = $result['rows'][0];
            return [
                'avgPageViewsPerSession' => round((float) $row['metricValues'][0]['value'], 2),
                'avgSessionDuration' => round((float) $row['metricValues'][1]['value'], 2),
                'bounceRate' => round((float) $row['metricValues'][2]['value'] * 100, 2),
                'totalUsers' => (int) $row['metricValues'][3]['value'],
                'totalPageViews' => (int) $row['metricValues'][4]['value'],
                'totalSessions' => (int) $row['metricValues'][5]['value'],
            ];
        }

        return [
            'avgPageViewsPerSession' => 0,
            'avgSessionDuration' => 0,
            'bounceRate' => 0,
            'totalUsers' => 0,
            'totalPageViews' => 0,
            'totalSessions' => 0,
        ];
    }

    public function fetchDeviceStats($client, $propertyId, $days)
    {
        $body = [
            'dateRanges' => [['startDate' => now()->subDays($days)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d')]],
            'dimensions' => [['name' => 'deviceCategory']],
            'metrics' => [['name' => 'activeUsers'], ['name' => 'screenPageViews']],
            'orderBys' => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);
        $deviceStats = [];

        if (!empty($result['rows'])) {
            $deviceTR = ['mobile' => 'Mobil', 'desktop' => 'Masaüstü', 'tablet' => 'Tablet'];
            foreach ($result['rows'] as $row) {
                $device = $row['dimensionValues'][0]['value'];
                $deviceStats[] = [
                    'device' => $deviceTR[$device] ?? ucfirst($device),
                    'users' => (int) $row['metricValues'][0]['value'],
                    'pageViews' => (int) $row['metricValues'][1]['value'],
                ];
            }
        }

        return $deviceStats;
    }

    public function fetchBrowserStats($client, $propertyId, $days)
    {
        $body = [
            'dateRanges' => [['startDate' => now()->subDays($days)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d')]],
            'dimensions' => [['name' => 'browser']],
            'metrics' => [['name' => 'activeUsers'], ['name' => 'screenPageViews']],
            'orderBys' => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
            'limit' => 10,
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);
        $browserStats = [];

        if (!empty($result['rows'])) {
            foreach ($result['rows'] as $row) {
                $browserStats[] = [
                    'browser' => $row['dimensionValues'][0]['value'],
                    'users' => (int) $row['metricValues'][0]['value'],
                    'pageViews' => (int) $row['metricValues'][1]['value'],
                ];
            }
        }

        return $browserStats;
    }

    public function fetchTopPages($client, $propertyId, $days)
    {
        $body = [
            'dateRanges' => [['startDate' => now()->subDays($days)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d')]],
            'dimensions' => [['name' => 'pageTitle'], ['name' => 'pagePath']],
            'metrics' => [
                ['name' => 'screenPageViews'],
                ['name' => 'activeUsers'],
                ['name' => 'averageSessionDuration'],
            ],
            'orderBys' => [['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]],
            'limit' => 20,
        ];

        $response = $client->post("properties/{$propertyId}:runReport", ['json' => $body]);
        $result = json_decode($response->getBody(), true);
        $topPages = [];

        if (!empty($result['rows'])) {
            foreach ($result['rows'] as $row) {
                $topPages[] = [
                    'pageTitle' => $row['dimensionValues'][0]['value'],
                    'pagePath' => $row['dimensionValues'][1]['value'],
                    'screenPageViews' => (int) $row['metricValues'][0]['value'],
                    'activeUsers' => (int) $row['metricValues'][1]['value'],
                    'avgDuration' => round((float) $row['metricValues'][2]['value'], 2),
                ];
            }
        }

        return $topPages;
    }

    /**
     * Ürün sayfası için görüntülenme sayısını getir
     * Cache ile performans optimizasyonu yapıldı
     */
    public function getProductPageViews($url, $startDate, $endDate)
    {
        try {
            $cacheKey = 'product_views_' . md5($url);

            $this->ensureValidToken();

            if (!$this->accessToken || !$this->propertyId) {
                return 0;
            }

            $client = new Client([
                'base_uri' => 'https://analyticsdata.googleapis.com/v1beta/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                ],
            ]);

            $body = [
                'dateRanges' => [['startDate' => $startDate->format('Y-m-d'), 'endDate' => $endDate->format('Y-m-d')]],
                'dimensions' => [['name' => 'pagePath']],
                'metrics' => [['name' => 'screenPageViews'], ['name' => 'activeUsers'], ['name' => 'eventCount']],
                'dimensionFilter' => [
                    'filter' => [
                        'fieldName' => 'pagePath',
                        'stringFilter' => ['matchType' => 'CONTAINS', 'value' => $url],
                    ],
                ],
            ];

            $response = $client->post("properties/{$this->propertyId}:runReport", [
                'json' => $body,
                'timeout' => 5,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $views = 0;

            if (!empty($result['rows'][0]['metricValues'][0]['value'])) {
                $views = (int) $result['rows'][0]['metricValues'][0]['value'];
            }

            cache()->put($cacheKey, $views, 3600);

            return $views;
        } catch (\Exception $e) {
            Log::error('Ürün görüntülenme sayısı alınamadı: ' . $e->getMessage());
            return 0;
        }
    }
}
