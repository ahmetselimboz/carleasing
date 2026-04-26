@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-8">
        <div>
            <a href="{{ route('sliders.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Slayt listesi
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Yeni anasayfa slaytı</h2>
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            @can('create', App\Models\Slider::class)
                <form method="POST" action="{{ route('sliders.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @include('admin.site.sliders._form', ['slider' => null, 'edit' => false])
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                            <i class="ri-save-3-line"></i> Kaydet
                        </button>
                    </div>
                </form>
            @endcan
        </div>
    </div>
@endsection
