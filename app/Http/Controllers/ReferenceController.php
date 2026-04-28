<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\View\View;

class ReferenceController
{
    public function index(): View
    {
        $references = Reference::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('theme.v1.references', compact('references'));
    }
}
