@extends('layouts.app', [
    'bodyClass' => 'relative min-h-screen flex items-center justify-center overflow-y-auto'
])

@section('content')

<!-- BACKGROUND -->
<div class="absolute inset-0">
    <div class="w-full h-full bg-[url('/public/images/bg.jpg')] bg-cover bg-center opacity-60"></div>

    <!-- Overlay hijau lembut -->
    <div class="absolute inset-0 bg-emerald-600/20 backdrop-blur-sm"></div>
</div>

<!-- MAIN CONTENT -->
<div class="relative z-10 w-full max-w-6xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

    <!-- LEFT SIDE (LOGO + TEXT) -->
    <div class="space-y-6 text-white drop-shadow-md">

        <div class="flex items-center gap-4">

            <!-- LOGO CARD — ukuran fix agar sama tinggi dengan card login -->
            <div class="bg-white/80 rounded-2xl backdrop-blur
                        h-28 w-28 flex items-center justify-center overflow-hidden shadow-lg border border-white/50">

                <img src="/images/logo.jpg"
                     alt="Logo"
                     class="h-full w-full object-cover">
            </div>

            <h1 class="text-4xl font-bold">Hospital System</h1>
        </div>

        <p class="text-lg text-white/90 leading-relaxed max-w-md">
            Sistem informasi operasi yang cepat, aman, dan mudah digunakan oleh staf dan pasien.
        </p>
    </div>

    <!-- RIGHT SIDE (LOGIN FORM) -->
    <div class="w-full max-w-md ml-auto">
        <div class="rounded-3xl bg-white/85 backdrop-blur-md p-10 shadow-2xl border border-white/40">

            <h2 class="text-2xl font-semibold text-emerald-800 mb-8">Masuk ke Akun Anda</h2>

            <form action="{{ route('login.attempt', [], false) }}" method="POST" class="space-y-8">
                @csrf

                <!-- EMAIL -->
                <div class="relative pt-3">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="peer w-full border-b-2 border-emerald-300 bg-transparent pb-2 pt-4 text-emerald-900 outline-none transition-all focus:border-emerald-600"
                        required
                    />
                    <label for="email"
                        class="absolute left-0 top-3 text-emerald-700/70 text-sm transition-all peer-focus:-top-1 peer-focus:text-xs peer-focus:text-emerald-600 peer-valid:-top-1 peer-valid:text-xs">
                        Email
                    </label>
                </div>

                <!-- PASSWORD -->
                <div class="relative pt-3">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="peer w-full border-b-2 border-emerald-300 bg-transparent pb-2 pt-4 pr-10 text-emerald-900 outline-none transition-all focus:border-emerald-600"
                        required
                    />
                    <label for="password"
                        class="absolute left-0 top-3 text-emerald-700/70 text-sm transition-all peer-focus:-top-1 peer-focus:text-xs peer-focus:text-emerald-600 peer-valid:-top-1 peer-valid:text-xs">
                        Password
                    </label>

                    <!-- Toggle -->
                    <button type="button" id="togglePassword"
                        class="absolute right-0 bottom-2 text-emerald-500 hover:opacity-80">
                        <i data-lucide="eye" class="h-5 w-5"></i>
                    </button>
                </div>

                <!-- OPTIONS -->
                <div class="flex items-center justify-between text-sm text-emerald-700">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="h-4 w-4 border-emerald-400 text-emerald-600">
                        Ingat saya
                    </label>
                    <a href="#" class="hover:text-emerald-800">Lupa password?</a>
                </div>

                <!-- BUTTON -->
                <button
                    type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl py-3 shadow-lg shadow-emerald-500/20 transition">
                    Masuk
                </button>
            </form>

        </div>

        <p class="text-center mt-6 text-sm text-white/90">
            Belum punya akun?
            <a href="{{ route('register', [], false) }}" class="underline font-semibold">Daftar sebagai pasien</a>
        </p>
    </div>

</div>

<!-- SHOW/HIDE PASSWORD SCRIPT -->
<script>
    const pwd = document.getElementById("password");
    const btn = document.getElementById("togglePassword");

    btn.addEventListener("click", () => {
        const show = pwd.type === "password";
        pwd.type = show ? "text" : "password";
        btn.innerHTML = show
            ? `<i data-lucide="eye-off" class="h-5 w-5"></i>`
            : `<i data-lucide="eye" class="h-5 w-5"></i>`;
        lucide.createIcons();
    });
</script>

@endsection
