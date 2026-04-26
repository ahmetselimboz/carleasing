<div id="admin-toast-container" class="fixed top-4 right-4 z-[9999] flex max-w-sm w-full flex-col gap-2 pointer-events-none"></div>
<script>
(function () {
    var palettes = {
        success: { box: 'bg-emerald-50 border border-emerald-200', icon: 'bg-emerald-500 text-white', sym: 'ri-check-line' },
        error: { box: 'bg-red-50 border border-red-200', icon: 'bg-red-500 text-white', sym: 'ri-close-line' },
        warning: { box: 'bg-amber-50 border border-amber-200', icon: 'bg-amber-500 text-white', sym: 'ri-alert-line' },
        info: { box: 'bg-[#37008a]/10 border border-[#37008a]/20', icon: 'bg-[#37008a] text-white', sym: 'ri-information-line' }
    };

    window.adminShowToast = function (type, title, message) {
        type = type || 'info';
        var c = document.getElementById('admin-toast-container');
        if (!c) return;
        var p = palettes[type] || palettes.info;
        var wrap = document.createElement('div');
        wrap.className = 'pointer-events-auto rounded-xl shadow-lg p-4 flex items-start gap-3 toast-enter ' + p.box;
        wrap.innerHTML =
            '<span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold ' + p.icon + '">' +
            '<i class="' + p.sym + '"></i></span>' +
            '<div class="flex-1 min-w-0">' +
            '<p class="font-medium text-slate-900">' + (title || '') + '</p>' +
            '<p class="text-sm text-slate-600 mt-0.5">' + (message || '') + '</p></div>' +
            '<button type="button" class="toast-close text-slate-400 hover:text-slate-600"><i class="ri-close-line"></i></button>';
        var close = function () {
            if (wrap.parentNode) wrap.parentNode.removeChild(wrap);
        };
        wrap.querySelector('.toast-close').addEventListener('click', close);
        c.appendChild(wrap);
        setTimeout(close, 5000);
    };

    document.addEventListener('DOMContentLoaded', function () {
        var f = @json(session('toast'));
        if (f && window.adminShowToast) {
            adminShowToast(f.type, f.title, f.message);
        }
    });
})();
</script>
