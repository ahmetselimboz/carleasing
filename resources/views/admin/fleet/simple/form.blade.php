@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ $indexRoute }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Listeye dön
            </a>
            <h2 class="text-2xl font-bold text-slate-800">{{ $title }}</h2>
            @if (!empty($description))
                <p class="text-slate-500 text-sm mt-1">{{ $description }}</p>
            @endif
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ $action }}" class="space-y-5">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                @foreach ($fields as $field)
                    @php
                        $name = $field['name'];
                        $type = $field['type'];
                        $label = $field['label'];
                        $required = $field['required'] ?? false;
                        $val = old($name, $model?->{$name});
                    @endphp

                    @if ($type === 'checkbox')
                        <div>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="{{ $name }}" value="1"
                                    class="rounded border-slate-300 text-brand focus:ring-brand"
                                    @checked(old($name, $model?->{$name} ?? true))>
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </label>
                            @error($name)
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @elseif ($type === 'textarea')
                        <div>
                            <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-2">{{ $label }}
                                @if (!$required)
                                    <span class="text-slate-400 font-normal">(isteğe bağlı)</span>
                                @endif
                            </label>
                            <textarea name="{{ $name }}" id="{{ $name }}" rows="3"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error($name) border-red-500 @enderror">{{ $val }}</textarea>
                            @error($name)
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @elseif ($type === 'select')
                        <div>
                            <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-2">{{ $label }}</label>
                            <select name="{{ $name }}" id="{{ $name }}" @if ($required) required @endif
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft bg-white @error($name) border-red-500 @enderror">
                                @foreach ($field['options'] as $optVal => $optLabel)
                                    <option value="{{ $optVal }}" @selected((string) old($name, $model?->{$name}) === (string) $optVal)>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error($name)
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <div>
                            <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-2">{{ $label }}
                                @if (!$required)
                                    <span class="text-slate-400 font-normal">(isteğe bağlı)</span>
                                @endif
                            </label>
                            <input type="text" name="{{ $name }}" id="{{ $name }}" value="{{ $val }}"
                                @if ($required) required @endif
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error($name) border-red-500 @enderror">
                            @error($name)
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                @endforeach

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ $indexRoute }}"
                        class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-soft">Vazgeç</a>
                    @if ($model)
                        @can('update', $model)
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                                <i class="ri-save-3-line"></i> Kaydet
                            </button>
                        @endcan
                    @else
                        @can('create', $modelFqcn)
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                                <i class="ri-save-3-line"></i> Kaydet
                            </button>
                        @endcan
                    @endif
                </div>
            </form>
        </div>

        @if ($model && !empty($routePrefix))
            @can('delete', $model)
                <div
                    class="card bg-white rounded-2xl border border-red-100 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-sm text-slate-600">Bu kaydı kalıcı olarak siler.</p>
                    <form method="POST" action="{{ route($routePrefix . '.destroy', $model) }}"
                        onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-soft w-full sm:w-auto">
                            <i class="ri-delete-bin-line"></i> Sil
                        </button>
                    </form>
                </div>
            @endcan
        @endif
    </div>
@endsection
