<?php

namespace App\Http\Controllers\Manage;

use App\Models\Car;
use App\Models\Message;
use App\Models\RentalRequest;
use App\Models\WeCallYou;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', RentalRequest::class);

        $filters = $this->resolveFilters($request);
        $payload = $this->buildReportPayload($filters['start'], $filters['end'], $filters['group_by']);

        return view('admin.reports.index', [
            'filters' => $filters,
            ...$payload,
        ]);
    }

    public function export(Request $request, string $format)
    {
        $this->authorize('viewAny', RentalRequest::class);

        $filters = $this->resolveFilters($request);
        $payload = $this->buildReportPayload($filters['start'], $filters['end'], $filters['group_by']);

        if ($format === 'csv') {
            return $this->exportCsv($payload['leadRows'], $filters['label']);
        }

        if ($format === 'excel') {
            return $this->exportExcel($payload['leadRows'], $filters['label']);
        }

        return $this->exportPdf($filters, $payload);
    }

    private function resolveFilters(Request $request): array
    {
        $period = (string) $request->input('period', 'last_7_days');
        $allowedPeriods = ['today', 'last_7_days', 'last_30_days', 'this_month', 'this_year', 'custom'];
        if (! in_array($period, $allowedPeriods, true)) {
            $period = 'last_30_days';
        }

        $groupBy = (string) $request->input('group_by', 'day');
        if (! in_array($groupBy, ['day', 'week', 'month'], true)) {
            $groupBy = 'day';
        }

        $now = now();
        $start = null;
        $end = null;

        if ($period === 'today') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
        } elseif ($period === 'last_7_days') {
            $start = $now->copy()->subDays(6)->startOfDay();
            $end = $now->copy()->endOfDay();
        } elseif ($period === 'last_30_days') {
            $start = $now->copy()->subDays(29)->startOfDay();
            $end = $now->copy()->endOfDay();
        } elseif ($period === 'this_month') {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
        } elseif ($period === 'this_year') {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
        } else {
            $startInput = $request->input('start_date');
            $endInput = $request->input('end_date');

            try {
                $start = $startInput ? Carbon::parse((string) $startInput)->startOfDay() : $now->copy()->subDays(6)->startOfDay();
                $end = $endInput ? Carbon::parse((string) $endInput)->endOfDay() : $now->copy()->endOfDay();
            } catch (\Throwable) {
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                $period = 'last_7_days';
            }
        }

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $labels = [
            'today' => 'Bugun',
            'last_7_days' => 'Son 7 Gun',
            'last_30_days' => 'Son 30 Gun',
            'this_month' => 'Bu Ay',
            'this_year' => 'Bu Yil',
            'custom' => 'Ozel Aralik',
        ];

        return [
            'period' => $period,
            'group_by' => $groupBy,
            'start' => $start,
            'end' => $end,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'label' => $labels[$period],
            'range_human' => $start->format('d.m.Y').' - '.$end->format('d.m.Y'),
        ];
    }

    private function buildReportPayload(Carbon $start, Carbon $end, string $groupBy): array
    {
        $rentalBase = RentalRequest::query()->whereBetween('created_at', [$start, $end]);
        $messageBase = Message::query()->whereBetween('created_at', [$start, $end]);
        $callBase = WeCallYou::query()->whereBetween('created_at', [$start, $end]);

        $kpis = [
            'rental_requests' => (int) (clone $rentalBase)->count(),
            'messages' => (int) (clone $messageBase)->count(),
            'callback_requests' => (int) (clone $callBase)->count(),
            'active_cars' => (int) Car::query()->where('is_active', true)->count(),
            'unread_rental' => (int) (clone $rentalBase)->whereNull('read_at')->count(),
            'unread_messages' => (int) (clone $messageBase)->whereNull('read_at')->count(),
            'unread_callbacks' => (int) (clone $callBase)->whereNull('read_at')->count(),
        ];
        $kpis['total_leads'] = $kpis['rental_requests'] + $kpis['messages'] + $kpis['callback_requests'];

        $leadRows = $this->collectLeadRows($start, $end);
        $timeline = $this->buildTimeline($leadRows, $start, $end, $groupBy);
        $cityBreakdown = $this->buildCityBreakdown($leadRows);

        $messageCategoryBreakdown = (clone $messageBase)
            ->selectRaw("COALESCE(category, 'belirsiz') as category, COUNT(*) as total")
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row): array => [
                'label' => Message::categoryLabels()[$row->category] ?? ucfirst((string) $row->category),
                'count' => (int) $row->total,
            ])
            ->values();

        $demandBreakdown = $this->buildCarDemandBreakdown($start, $end);

        return [
            'kpis' => $kpis,
            'timeline' => $timeline,
            'cityBreakdown' => $cityBreakdown,
            'messageCategoryBreakdown' => $messageCategoryBreakdown,
            'demandBreakdown' => $demandBreakdown,
            'leadRows' => $leadRows,
        ];
    }

    private function collectLeadRows(Carbon $start, Carbon $end): Collection
    {
        $rentals = RentalRequest::query()
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (RentalRequest $item): array {
                $name = trim((string) ($item->name ?? '').' '.(string) ($item->surname ?? ''));
                $firstCar = null;

                if (is_array($item->cars) && isset($item->cars[0]) && is_array($item->cars[0])) {
                    $firstCar = $item->cars[0]['slug'] ?? $item->cars[0]['title'] ?? null;
                }

                return [
                    'type' => 'Kiralama Talebi',
                    'date' => optional($item->created_at)->format('Y-m-d H:i:s'),
                    'name' => $name !== '' ? $name : '-',
                    'email' => $item->email ?? '-',
                    'phone' => $item->phone_number ?? '-',
                    'city' => $item->city ?? '-',
                    'detail' => $firstCar ? 'Arac: '.$firstCar : '-',
                    'read_status' => $item->read_at ? 'Okundu' : 'Yeni',
                ];
            });

        $messages = Message::query()
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Message $item): array {
                $name = trim((string) ($item->name ?? '').' '.(string) ($item->surname ?? ''));

                return [
                    'type' => 'Mesaj',
                    'date' => optional($item->created_at)->format('Y-m-d H:i:s'),
                    'name' => $name !== '' ? $name : '-',
                    'email' => $item->email ?? '-',
                    'phone' => $item->phone_number ?? '-',
                    'city' => '-',
                    'detail' => 'Kategori: '.($item->categoryLabel()),
                    'read_status' => $item->read_at ? 'Okundu' : 'Yeni',
                ];
            });

        $callbacks = WeCallYou::query()
            ->with('car:id,title')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (WeCallYou $item): array {
                $name = trim((string) ($item->name ?? '').' '.(string) ($item->surname ?? ''));

                return [
                    'type' => 'Geri Arama',
                    'date' => optional($item->created_at)->format('Y-m-d H:i:s'),
                    'name' => $name !== '' ? $name : '-',
                    'email' => $item->email ?? '-',
                    'phone' => $item->phone_number ?? '-',
                    'city' => $item->city ?? '-',
                    'detail' => $item->car?->title ? 'Arac: '.$item->car->title : ($item->preferred_time ? 'Tercih: '.$item->preferred_time : '-'),
                    'read_status' => $item->read_at ? 'Okundu' : 'Yeni',
                ];
            });

        return $rentals->concat($messages)->concat($callbacks)
            ->sortByDesc('date')
            ->values();
    }

    private function buildTimeline(Collection $leadRows, Carbon $start, Carbon $end, string $groupBy): Collection
    {
        $bucketFormat = match ($groupBy) {
            'month' => 'Y-m',
            'week' => 'o-\WW',
            default => 'Y-m-d',
        };

        $rows = $leadRows
            ->groupBy(function (array $row) use ($bucketFormat): string {
                return Carbon::parse((string) $row['date'])->format($bucketFormat);
            })
            ->map(function (Collection $group, string $bucket): array {
                return [
                    'bucket' => $bucket,
                    'total' => $group->count(),
                    'rental' => $group->where('type', 'Kiralama Talebi')->count(),
                    'message' => $group->where('type', 'Mesaj')->count(),
                    'callback' => $group->where('type', 'Geri Arama')->count(),
                ];
            })
            ->sortBy('bucket')
            ->values();

        if ($rows->isNotEmpty()) {
            return $rows;
        }

        return collect([
            [
                'bucket' => $start->format($bucketFormat).' - '.$end->format($bucketFormat),
                'total' => 0,
                'rental' => 0,
                'message' => 0,
                'callback' => 0,
            ],
        ]);
    }

    private function buildCityBreakdown(Collection $leadRows): Collection
    {
        return $leadRows
            ->filter(fn (array $row): bool => isset($row['city']) && $row['city'] !== '-' && $row['city'] !== '')
            ->groupBy(fn (array $row): string => mb_strtoupper(trim((string) $row['city'])))
            ->map(fn (Collection $group, string $city): array => ['city' => $city, 'count' => $group->count()])
            ->sortByDesc('count')
            ->take(12)
            ->values();
    }

    private function buildCarDemandBreakdown(Carbon $start, Carbon $end): Collection
    {
        $byTitle = [];

        $rentalRequests = RentalRequest::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('cars')
            ->get(['cars']);

        foreach ($rentalRequests as $request) {
            $cars = is_array($request->cars) ? $request->cars : [];
            foreach ($cars as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $title = (string) ($item['title'] ?? $item['slug'] ?? 'Belirsiz Arac');
                $quantity = (int) ($item['adet'] ?? $item['qty'] ?? $item['quantity'] ?? 1);
                $byTitle[$title] = ($byTitle[$title] ?? 0) + max($quantity, 1);
            }
        }

        $callbackCounts = WeCallYou::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('car_id')
            ->selectRaw('car_id, COUNT(*) as total')
            ->groupBy('car_id')
            ->get();

        foreach ($callbackCounts as $row) {
            $title = Car::query()->whereKey((int) $row->car_id)->value('title') ?? 'Belirsiz Arac';
            $byTitle[$title] = ($byTitle[$title] ?? 0) + (int) $row->total;
        }

        return collect($byTitle)
            ->map(fn (int $count, string $name): array => ['car' => $name, 'count' => $count])
            ->sortByDesc('count')
            ->take(12)
            ->values();
    }

    private function exportCsv(Collection $rows, string $label): StreamedResponse
    {
        $filename = 'rapor-'.$label.'-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'wb');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Tip', 'Tarih', 'Ad Soyad', 'E-posta', 'Telefon', 'Sehir', 'Detay', 'Durum'], ';');
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['type'],
                    $row['date'],
                    $row['name'],
                    $row['email'],
                    $row['phone'],
                    $row['city'],
                    $row['detail'],
                    $row['read_status'],
                ], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function exportExcel(Collection $rows, string $label): StreamedResponse
    {
        $filename = 'rapor-'.$label.'-'.now()->format('Ymd_His').'.xls';

        return response()->streamDownload(function () use ($rows): void {
            echo '<table border="1"><thead><tr>';
            echo '<th>Tip</th><th>Tarih</th><th>Ad Soyad</th><th>E-posta</th><th>Telefon</th><th>Sehir</th><th>Detay</th><th>Durum</th>';
            echo '</tr></thead><tbody>';
            foreach ($rows as $row) {
                echo '<tr>';
                foreach (['type', 'date', 'name', 'email', 'phone', 'city', 'detail', 'read_status'] as $field) {
                    echo '<td>'.e((string) $row[$field]).'</td>';
                }
                echo '</tr>';
            }
            echo '</tbody></table>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    private function exportPdf(array $filters, array $payload)
    {
        return Pdf::loadView('admin.reports.pdf', [
            'filters' => $filters,
            ...$payload,
        ])->setPaper('a4', 'landscape')->download('rapor-'.$filters['label'].'-'.now()->format('Ymd_His').'.pdf');
    }
}
