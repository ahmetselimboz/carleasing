<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#37008a">
    <link rel="icon" href="{{ asset('/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        (function() {
            try {
                if (localStorage.getItem('admin-ui-dark-mode') === 'true') {
                    document.documentElement.classList.add('dark-mode');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="bg-slate-50 min-h-screen">
    <script>
        if (document.documentElement.classList.contains('dark-mode')) {
            document.body.classList.add('dark-mode');
        }
    </script>
    <div class="flex min-h-screen">
        <!-- Sidebar - Açık renk -->
        @include('admin.components.aside')
        <div class="sidebar-overlay fixed inset-0 bg-black/30 z-30 lg:hidden hidden"></div>

        <main class="flex-1 overflow-x-hidden min-w-0">
            @include('admin.components.header')

            <div class="p-4 lg:p-6 space-y-6">
                @yield('content')
            </div>
        </main>
    </div>
    @include('admin.components.toast')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
