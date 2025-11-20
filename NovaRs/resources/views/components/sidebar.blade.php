@props([
    'items' => [],
    'brand' => 'Hospital',
])

@php
    $routeName = request()->route()?->getName();
    $context = 'default';

    if ($routeName) {
        if (\Illuminate\Support\Str::startsWith($routeName, 'admin.')) $context = 'admin';
        elseif (\Illuminate\Support\Str::startsWith($routeName, 'doctor.')) $context = 'doctor';
        elseif (\Illuminate\Support\Str::startsWith($routeName, 'patient.')) $context = 'patient';
        elseif (\Illuminate\Support\Str::startsWith($routeName, 'staff.')) $context = 'staff';
    }

    /** BASE COLORS */
    $asideBase = [
        'patient' => 'border-emerald-100/80 bg-white text-emerald-700',
        'staff' => 'border-emerald-100/70 bg-white text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-100',
        'doctor' => 'border-slate-700 bg-slate-800 text-slate-200',
        'admin' => 'border-teal-100/70 bg-white/60 text-slate-900 backdrop-blur-xl dark:bg-slate-800/60 dark:text-slate-100',
        'default' => 'bg-[#2B6CB0] text-white',
    ][$context];

    /** ICON MAP */
    $iconMap = [
        'Dashboard' => 'layout-dashboard',
        'My Operations' => 'activity',
        'My Reports' => 'file-heart',
        'Patients' => 'users',
        'Doctors' => 'stethoscope',
        'Staff' => 'user-circle-2',
        'Rooms' => 'building-2',
        'Equipments' => 'package',
        'Diseases' => 'virus',
        'Operations' => 'scalpel',
        'Reports' => 'file-text',
        'Operation Requests' => 'inbox',
    ];

    /** ACTIVE STYLING */
    function linkState($context, $isActive) {
        return match ($context) {
            'patient' =>
                $isActive ? 'bg-emerald-100 text-emerald-700 shadow-sm'
                          : 'text-emerald-600/80 hover:bg-emerald-50 hover:text-emerald-700',

            'staff' =>
                $isActive ? 'bg-emerald-100 text-emerald-700 shadow-sm dark:bg-emerald-800/60 dark:text-emerald-100'
                          : 'text-emerald-600/80 hover:bg-emerald-50 hover:text-emerald-700 dark:text-emerald-200/80 dark:hover:bg-emerald-800/40 dark:hover:text-emerald-100',

            'doctor' =>
                $isActive ? 'bg-slate-700 text-white shadow-inner'
                          : 'text-slate-300/80 hover:bg-slate-700/70 hover:text-white',

            'admin' =>
                $isActive ? 'bg-teal-600 text-white shadow-lg'
                          : 'text-slate-600/80 hover:bg-teal-50 hover:text-teal-700 dark:text-slate-300/80 dark:hover:bg-slate-700/60 dark:hover:text-white',

            default =>
                $isActive ? 'bg-white/20 text-white'
                          : 'text-white/80 hover:bg-white/10 hover:text-white',
        };
    }
@endphp


<!-- =============== HAMBURGER MOBILE =============== -->
<button onclick="toggleSidebar()"
    class="lg:hidden p-3 text-slate-700 fixed top-4 left-4 z-50 bg-white/90 rounded-xl shadow-md">
    <i data-lucide="menu" class="w-6 h-6"></i>
</button>


<!-- ================================================= -->
<!-- =============== SIDEBAR ========================= -->
<!-- ================================================= -->

<aside id="sidebar"
    class="group fixed lg:sticky top-0 left-0 h-full lg:h-screen z-40
           transform -translate-x-full lg:translate-x-0
           transition-all duration-300 ease-in-out
           border-r {{ $asideBase }}
           w-72 lg:w-20 hover:lg:w-72 overflow-hidden">

    <!-- BRAND -->
    <div class="flex items-center gap-3 px-5 py-6 transition-all duration-300">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl
                     bg-gradient-to-br from-teal-500 via-blue-500 to-purple-500
                     text-white text-lg font-bold uppercase shadow-lg">
            {{ strtoupper(substr($brand, 0, 2)) }}
        </span>

        <!-- Hidden in compact, shown on hover -->
        <span class="text-xl font-semibold whitespace-nowrap
                     hidden lg:group-hover:block">
            {{ $brand }}
        </span>
    </div>

    <!-- MENU -->
    <nav class="flex-1 space-y-1 px-3 pb-6 overflow-y-auto">
        @foreach ($items as $item)
            @php
                $routeName = $item['route'] ?? null;
                $href = $routeName ? route($routeName) : ($item['href'] ?? '#');

                $activePatterns = $item['active'] ?? $routeName;
                $activePatterns = is_array($activePatterns)
                    ? array_filter($activePatterns)
                    : array_filter([$activePatterns]);

                $isActive = false;
                foreach ($activePatterns as $pattern) {
                    if (! $pattern) {
                        continue;
                    }

                    if (request()->routeIs($pattern)) {
                        $isActive = true;
                        break;
                    }

                    if (! $routeName && \Illuminate\Support\Str::contains($pattern, '/')) {
                        if (request()->is(ltrim($pattern, '/'))) {
                            $isActive = true;
                            break;
                        }
                    }
                }

                if (! $isActive && ! $routeName && isset($item['href'])) {
                    $hrefPath = trim(parse_url($item['href'], PHP_URL_PATH) ?? '', '/');
                    if ($hrefPath && request()->is($hrefPath)) {
                        $isActive = true;
                    }
                }

                $iconName = $iconMap[$item['label']] ?? 'dot';
            @endphp

<a href="{{ $href }}" onclick="closeSidebar()"
   class="flex items-center gap-4 px-4 py-3 lg:py-4 min-h-[12px] rounded-xl
          text-sm font-medium transition-all duration-200
          {{ linkState($context, $isActive) }}">


                <!-- ICON -->
                <i data-lucide="{{ $iconName }}" class="h-5 w-5"></i>

                <!-- TEXT (hidden saat compact) -->
                <span class="whitespace-nowrap hidden lg:group-hover:block">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
    </nav>
</aside>


<!-- ================================================= -->
<!-- =============== SCRIPT ========================== -->
<!-- ================================================= -->

<script>
    const sidebar = document.getElementById('sidebar');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
    }

    function closeSidebar() {
        if (window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
        }
    }
</script>
