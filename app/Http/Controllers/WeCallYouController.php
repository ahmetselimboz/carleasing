<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\WeCallYou;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeCallYouController
{
    public function create(Request $request): View
    {
        $car = null;
        $slug = $request->query('car');
        if (is_string($slug) && $slug !== '') {
            $car = Car::query()->where('slug', $slug)->where('is_active', true)->first();
        }

        $config = [
            'package_id' => $request->integer('package') ?: null,
            'duration_id' => $request->integer('duration') ?: null,
            'kilometer_id' => $request->integer('kilometer') ?: null,
            'down_payment_id' => $request->integer('down_payment') ?: null,
        ];

        return view('theme.v1.we-call-you', compact('car', 'config'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'surname' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone_number' => ['required', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:80'],
            'preferred_time' => ['nullable', 'string', 'max:64'],
            'note' => ['nullable', 'string', 'max:2000'],
            'car_slug' => ['nullable', 'string', 'max:160'],
            'kvkk' => ['accepted'],
            'package_id' => ['nullable', 'integer'],
            'duration_id' => ['nullable', 'integer'],
            'kilometer_id' => ['nullable', 'integer'],
            'down_payment_id' => ['nullable', 'integer'],
        ]);

        $car = null;
        if (! empty($data['car_slug'])) {
            $car = Car::query()->where('slug', $data['car_slug'])->where('is_active', true)->first();
        }

        WeCallYou::create([
            'name' => $data['name'],
            'surname' => $data['surname'] ?? null,
            'email' => $data['email'] ?? null,
            'phone_number' => $data['phone_number'],
            'city' => $data['city'] ?? null,
            'preferred_time' => $data['preferred_time'] ?? null,
            'note' => $data['note'] ?? null,
            'car_id' => $car?->id,
            'magicbox' => [
                'config' => array_filter([
                    'package_id' => $data['package_id'] ?? null,
                    'duration_id' => $data['duration_id'] ?? null,
                    'kilometer_id' => $data['kilometer_id'] ?? null,
                    'down_payment_id' => $data['down_payment_id'] ?? null,
                ]),
                'car_slug' => $car?->slug,
                'car_title' => $car?->title,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('we-call-you.create', $car ? ['car' => $car->slug] : [])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Talebiniz alındı',
                'message' => 'En kısa sürede sizi arayacağız. Teşekkürler!',
            ]);
    }
}
