<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Faq;
use App\Models\HomePartner;
use App\Models\HomeServiceTile;
use App\Models\HomeTestimonial;
use App\Models\Slider;
use Illuminate\View\View;

class HomeController
{
    public function index(): View
    {
        $heroSliders = Slider::query()->activeOrdered()->get();
        $featuredCars = Car::query()
            ->where('is_active', true)
            ->where('home_featured', true)
            ->orderByRaw('COALESCE(home_sort_order, 65535) asc')
            ->orderBy('id')
            ->with(['priceMatrices' => fn ($q) => $q->where('is_active', true)->orderBy('id')->with('duration')])
            ->limit(12)
            ->get();

        $serviceTiles = HomeServiceTile::query()->activeOrdered()->get();
        $partners = HomePartner::query()->activeOrdered()->get();
        $testimonials = HomeTestimonial::query()->activeOrdered()->get();
        $faqs = Faq::query()->activeOrdered()->get();

        return view('theme.v1.index', compact(
            'heroSliders',
            'featuredCars',
            'serviceTiles',
            'partners',
            'testimonials',
            'faqs',
        ));
    }
}
