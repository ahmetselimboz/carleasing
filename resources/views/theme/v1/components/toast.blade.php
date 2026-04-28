@php
    $toast = session('toast');
@endphp

<div id="toast-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none w-[min(92vw,380px)]">
</div>

<script>
    (function() {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const TYPES = {
            success: {
                bg: 'bg-emerald-50',
                border: 'border-emerald-200',
                text: 'text-emerald-900',
                icon: 'ri-checkbox-circle-fill',
                accent: 'text-emerald-600'
            },
            error: {
                bg: 'bg-rose-50',
                border: 'border-rose-200',
                text: 'text-rose-900',
                icon: 'ri-close-circle-fill',
                accent: 'text-rose-600'
            },
            warning: {
                bg: 'bg-amber-50',
                border: 'border-amber-200',
                text: 'text-amber-900',
                icon: 'ri-alert-fill',
                accent: 'text-amber-600'
            },
            info: {
                bg: 'bg-sky-50',
                border: 'border-sky-200',
                text: 'text-sky-900',
                icon: 'ri-information-fill',
                accent: 'text-sky-600'
            },
        };

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const show = (payload) => {
            if (!payload || (!payload.title && !payload.message)) return;
            const type = TYPES[payload.type] || TYPES.info;
            const duration = Number.isFinite(payload.duration) ? payload.duration : 5000;

            const el = document.createElement('div');
            el.className =
                `pointer-events-auto rounded-2xl border ${type.border} ${type.bg} ${type.text} shadow-lg shadow-slate-900/5 px-4 py-3.5 flex items-start gap-3 transform translate-x-[120%] opacity-0 transition-all duration-300`;
            el.setAttribute('role', 'status');
            el.innerHTML = `
                <i class="${type.icon} ${type.accent} text-2xl shrink-0 leading-none"></i>
                <div class="min-w-0 flex-1">
                    ${payload.title ? `<p class="font-bold text-sm leading-snug">${escapeHtml(payload.title)}</p>` : ''}
                    ${payload.message ? `<p class="text-xs mt-0.5 leading-relaxed opacity-90">${escapeHtml(payload.message)}</p>` : ''}
                </div>
                <button type="button" class="shrink-0 -mr-1 -mt-1 p-1 rounded-lg hover:bg-black/5 transition-colors text-current/70" aria-label="Kapat">
                    <i class="ri-close-line text-base"></i>
                </button>
            `;

            container.appendChild(el);
            requestAnimationFrame(() => {
                el.classList.remove('translate-x-[120%]', 'opacity-0');
            });

            const dismiss = () => {
                el.classList.add('translate-x-[120%]', 'opacity-0');
                setTimeout(() => el.remove(), 300);
            };

            el.querySelector('button')?.addEventListener('click', dismiss);
            if (duration > 0) setTimeout(dismiss, duration);
        };

        window.toast = show;

        @if ($toast)
            show(@json($toast));
        @endif
    })();
</script>
