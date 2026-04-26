@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-8">
        <div>
            <a href="{{ route('sliders.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Slayt listesi
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Slayt düzenle</h2>
            <p class="text-slate-500 text-sm mt-1">Slayt bilgilerini buradan güncelleyebilirsiniz.</p>
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            @can('update', $slider)
                <form method="POST" action="{{ route('sliders.update', $slider) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')
                    @include('admin.site.sliders._form', ['slider' => $slider, 'edit' => true])
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                            <i class="ri-save-3-line"></i> Kaydet
                        </button>
                    </div>
                </form>
            @else
                <p class="text-sm text-slate-500">Bu kaydı düzenleyemezsiniz.</p>
            @endcan
        </div>
    </div>
@endsection
