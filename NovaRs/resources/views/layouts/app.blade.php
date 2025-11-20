<!DOCTYPE html>
<html lang="en">
@php
    $bodyClasses = trim(($bodyClass ?? 'bg-gray-100 text-gray-900') . ' font-sans');
    $outerWrapperClass = $outerWrapperClass ?? 'min-h-screen flex';
    $contentWrapperClass = $contentWrapperClass ?? 'flex-1 flex flex-col min-h-screen';
    $mainClasses = trim('flex-1 ' . ($mainClass ?? 'p-6 space-y-6 bg-gray-50'));
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Hospital Operation Management' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $bodyClasses }}">
    <div class="{{ $outerWrapperClass }}">
        @hasSection('sidebar')
            @yield('sidebar')
        @endif

        <div class="{{ $contentWrapperClass }}">
            @hasSection('navbar')
                @yield('navbar')
            @endif

            <main class="{{ $mainClasses }}">
                @if (session('status'))
                    <div class="rounded-md bg-[#38B2AC]/10 border border-[#38B2AC]/40 text-[#2B6CB0] px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
