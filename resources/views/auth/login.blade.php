<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteTitle = $site['title'] ?? config('app.name');
    @endphp
    <title>{{ $siteTitle }} — Giriş</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ $site['favicon_url'] ?? asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#f5f0ff', 100: '#ede5ff', 200: '#ddd0ff', 300: '#c4adff', 400: '#8b5cf6', 500: '#6d28d9', 600: '#37008a', 700: '#2e0071', 800: '#26005d', 900: '#1a0040' }
                    }
                }
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="{{ asset('admin/css/shared.css') }}" rel="stylesheet">
    <style>
        .login-root {
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(109, 40, 217, 0.12), transparent 60%),
                radial-gradient(900px 500px at 110% 10%, rgba(55, 0, 138, 0.10), transparent 60%),
                var(--bg-body);
            overflow: hidden;
        }
        body.dark-mode .login-root {
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(139, 92, 246, 0.18), transparent 60%),
                radial-gradient(900px 500px at 110% 10%, rgba(109, 40, 217, 0.14), transparent 60%),
                var(--bg-body);
        }

        .login-bg { position: absolute; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
        .login-blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(70px);
            opacity: .55;
            animation: blobFloat 14s ease-in-out infinite;
        }
        .login-blob--a { width: 420px; height: 420px; background: #8b5cf6; top: -120px; left: -120px; }
        .login-blob--b { width: 360px; height: 360px; background: #37008a; bottom: -140px; right: -120px; animation-delay: -4s; }
        .login-blob--c { width: 260px; height: 260px; background: #c4adff; top: 40%; left: 45%; opacity: .35; animation-delay: -8s; }
        body.dark-mode .login-blob { opacity: .35; }
        body.dark-mode .login-blob--c { opacity: .2; }
        @keyframes blobFloat {
            0%,100% { transform: translate(0,0) scale(1); }
            50%     { transform: translate(20px,-30px) scale(1.05); }
        }
        @media (prefers-reduced-motion: reduce) { .login-blob { animation: none; } }

        .login-card {
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            background-color: rgba(255,255,255,0.85);
            box-shadow: 0 25px 60px -20px rgba(55, 0, 138, 0.28), 0 10px 30px -15px rgba(15, 23, 42, 0.15);
            animation: cardIn .45s cubic-bezier(.22,.61,.36,1) both;
        }
        body.dark-mode .login-card {
            background-color: rgba(30, 41, 59, 0.75);
            box-shadow: 0 25px 60px -20px rgba(0,0,0,0.6), 0 10px 30px -15px rgba(0,0,0,0.45);
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(12px) scale(.985); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-hero {
            position: relative;
            background: linear-gradient(135deg, #1a0040 0%, #37008a 55%, #6d28d9 100%);
            overflow: hidden;
        }
        .login-hero::before {
            content: "";
            position: absolute; inset: 0;
            background:
                radial-gradient(600px 200px at 20% 0%, rgba(255,255,255,0.18), transparent 60%),
                radial-gradient(400px 200px at 100% 100%, rgba(139, 92, 246, 0.35), transparent 60%);
            pointer-events: none;
        }
        .login-hero::after {
            content: "";
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse at center, #000 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 40%, transparent 80%);
            pointer-events: none;
            opacity: .6;
        }
        .login-hero-badge {
            width: 60px; height: 60px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.25), 0 10px 25px -10px rgba(0,0,0,0.35);
            backdrop-filter: blur(4px);
        }

        .login-field {
            position: relative;
        }
        .login-field .login-field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            pointer-events: none;
            transition: color .2s ease;
        }
        body.dark-mode .login-field .login-field-icon { color: var(--text-muted); }
        .login-field input {
            background-color: rgba(248, 250, 252, 0.7);
        }
        body.dark-mode .login-field input {
            background-color: rgba(15, 23, 42, 0.5);
            border-color: var(--border-default) !important;
            color: var(--text-body);
        }
        .login-field input:focus {
            background-color: #fff;
        }
        body.dark-mode .login-field input:focus {
            background-color: rgba(15, 23, 42, 0.85);
        }
        .login-field input:focus + .login-field-icon,
        .login-field:focus-within .login-field-icon {
            color: #37008a;
        }
        body.dark-mode .login-field:focus-within .login-field-icon { color: #c4adff; }

        .login-submit {
            background: linear-gradient(135deg, #37008a 0%, #6d28d9 100%);
            box-shadow: 0 10px 25px -10px rgba(55, 0, 138, 0.6);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .login-submit:hover {
            filter: brightness(1.08);
            box-shadow: 0 14px 30px -10px rgba(55, 0, 138, 0.7);
            transform: translateY(-1px);
        }
        .login-submit:active { transform: translateY(0); }
        .login-submit .ri-arrow-right-line { transition: transform .2s ease; }
        .login-submit:hover .ri-arrow-right-line { transform: translateX(3px); }

        .login-forgot {
            position: relative;
            transition: color .2s ease;
        }
        .login-forgot::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: -2px;
            height: 1px;
            background: currentColor;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform .25s ease;
        }
        .login-forgot:hover::after { transform: scaleX(1); transform-origin: left; }

        .login-check {
            appearance: none;
            -webkit-appearance: none;
            width: 18px; height: 18px;
            border: 1.5px solid #cbd5e1;
            border-radius: 5px;
            background: #fff;
            display: inline-grid;
            place-content: center;
            cursor: pointer;
            transition: all .15s ease;
        }
        .login-check::before {
            content: "";
            width: 10px; height: 10px;
            transform: scale(0);
            transition: transform .15s ease;
            background: #fff;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        }
        .login-check:checked {
            background: linear-gradient(135deg, #37008a 0%, #6d28d9 100%);
            border-color: #37008a;
        }
        .login-check:checked::before { transform: scale(1); }
        .login-check:focus-visible { outline: 2px solid #c4adff; outline-offset: 2px; }
        body.dark-mode .login-check { background: rgba(15, 23, 42, 0.5); border-color: var(--border-default); }

        .login-alert {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #b91c1c;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.4;
        }
        body.dark-mode .login-alert {
            background: rgba(239, 68, 68, 0.14);
            color: #fca5a5;
        }
    </style>
</head>
<body class="login-root min-h-screen flex items-center justify-center p-4 relative">
    <div class="login-bg" aria-hidden="true">
        <span class="login-blob login-blob--a"></span>
        <span class="login-blob login-blob--b"></span>
        <span class="login-blob login-blob--c"></span>
    </div>

    <button type="button"
        class="dark-mode-toggle fixed top-4 right-4 z-20 p-2.5 rounded-xl border border-transparent text-slate-600 hover:bg-slate-100 transition-soft"
        title="Tema değiştir"
        aria-label="Tema değiştir">
        <i class="ri-moon-line text-xl"></i>
    </button>

    <div class="w-full max-w-md relative z-10">
        <div class="login-card card rounded-2xl border border-slate-100 overflow-hidden">
            <div class="login-hero px-8 py-10 text-center">
                <div class="relative">
                    @if (! empty($site['logo_url']))
                        <div class="flex justify-center mb-3">
                            <img src="{{ $site['logo_url'] }}" alt="{{ $siteTitle }}" class="login-hero-logo max-w-[min(100%,220px)] drop-shadow-sm">
                        </div>
                    @else
                        <div class="login-hero-badge mx-auto mb-4 rounded-2xl flex items-center justify-center">
                            <i class="ri-dashboard-3-line text-3xl text-white"></i>
                        </div>
                    @endif
                    <h1 class="text-xl font-semibold text-white leading-snug tracking-tight">{{ $siteTitle }}</h1>
                    <p class="text-white/75 text-sm mt-1.5">Yönetim paneline hoş geldiniz</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="p-8 space-y-5" novalidate>
                @csrf

                @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
                    <div class="login-alert" role="alert">
                        <i class="ri-error-warning-line text-base mt-0.5"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div>
                    <label for="login-email" class="login-label block text-xs font-semibold uppercase tracking-wider mb-2">E-posta</label>
                    <div class="login-field">
                        <input id="login-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-11 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-[#37008a]/30 focus:border-[#37008a] transition outline-none @error('email') border-red-500 @else border-slate-200 @enderror"
                            placeholder="ornek@email.com">
                        <i class="ri-mail-line login-field-icon"></i>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="ri-error-warning-line"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="login-password" class="login-label block text-xs font-semibold uppercase tracking-wider mb-2">Şifre</label>
                    <div class="login-field">
                        <input id="login-password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-11 pr-12 py-3 border rounded-xl focus:ring-2 focus:ring-[#37008a]/30 focus:border-[#37008a] transition outline-none @error('password') border-red-500 @else border-slate-200 @enderror"
                            placeholder="••••••••">
                        <i class="ri-lock-2-line login-field-icon"></i>
                        <button type="button" class="js-password-toggle password-toggle absolute right-1.5 top-1/2 -translate-y-1/2 p-2 rounded-lg"
                            aria-label="Şifreyi göster" aria-pressed="false" data-target="#login-password">
                            <i class="ri-eye-line text-xl"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="ri-error-warning-line"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between gap-3 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="login-check">
                        <span class="text-sm login-muted">Beni hatırla</span>
                    </label>
                    {{-- <a href="#" class="login-forgot login-muted text-sm shrink-0">Şifremi unuttum</a> --}}
                </div>

                <button type="submit"
                    class="login-submit w-full py-3 text-white font-medium rounded-xl flex items-center justify-center gap-2">
                    <span>Giriş Yap</span>
                    <i class="ri-arrow-right-line text-lg"></i>
                </button>
            </form>
        </div>

        <p class="text-center mt-6">
            <a href="{{ route('home') }}" class="login-muted text-sm inline-flex items-center gap-1.5 hover:underline">
                <i class="ri-arrow-left-line"></i> Siteye dön
            </a>
        </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin/js/app.js') }}"></script>
    <script>
        (function ($) {
            $(document).on('click', '.js-password-toggle', function () {
                var $btn = $(this);
                var sel = $btn.data('target');
                var $in = sel ? $(sel) : null;
                if (!$in || !$in.length) {
                    $in = $btn.closest('.login-field, .relative').find('input').first();
                }
                if (!$in.length) {
                    return;
                }
                var show = $in.attr('type') === 'password';
                $in.attr('type', show ? 'text' : 'password');
                $btn.attr('aria-pressed', show ? 'true' : 'false');
                $btn.attr('aria-label', show ? 'Şifreyi gizle' : 'Şifreyi göster');
                $btn.find('i').toggleClass('ri-eye-line ri-eye-off-line');
            });
        })(jQuery);
    </script>
</body>
</html>
