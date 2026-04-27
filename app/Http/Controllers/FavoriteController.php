<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController
{
    public function index(Request $request): View
    {
        $favorites = Favorite::query()
            ->with(['car' => fn ($q) => $q->with(['priceMatrices' => fn ($pm) => $pm->where('is_active', true)->orderBy('id')->with('duration')])])
            ->when(
                $request->user(),
                fn ($q) => $q->where('user_id', $request->user()->id),
                fn ($q) => $q->whereNull('user_id')->where('session_id', $request->session()->getId())
            )
            ->latest()
            ->get();

        return view('theme.v1.favorites.index', compact('favorites'));
    }

    public function store(Request $request, Car $car): RedirectResponse
    {
        if ($request->user()) {
            Favorite::query()->firstOrCreate([
                'car_id' => $car->id,
                'user_id' => $request->user()->id,
            ]);
        } else {
            Favorite::query()->firstOrCreate([
                'car_id' => $car->id,
                'session_id' => $request->session()->getId(),
                'user_id' => null,
            ]);
        }

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Favorilere eklendi',
            'message' => 'Arac favori listenize eklendi.',
        ]);
    }

    public function destroy(Request $request, Car $car): RedirectResponse
    {
        Favorite::query()
            ->where('car_id', $car->id)
            ->when(
                $request->user(),
                fn ($q) => $q->where('user_id', $request->user()->id),
                fn ($q) => $q->whereNull('user_id')->where('session_id', $request->session()->getId())
            )
            ->delete();

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Favorilerden kaldirildi',
            'message' => 'Arac favori listenizden kaldirildi.',
        ]);
    }
}
