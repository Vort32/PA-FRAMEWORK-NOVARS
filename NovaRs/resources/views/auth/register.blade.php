@extends('layouts.app', [
    'bodyClass' => 'relative min-h-screen flex items-center justify-center overflow-y-auto'
])

@section('content')

<!-- BACKGROUND -->
<div class="absolute inset-0">
    <div class="w-full h-full bg-[url('/public/images/bg.jpg')] bg-cover bg-center opacity-60"></div>
    <div class="absolute inset-0 bg-emerald-600/20 backdrop-blur-sm"></div>
</div>

<!-- MAIN CONTENT -->
<div class="relative z-10 w-full max-w-6xl mx-auto px-6 py-16"> <!-- widened from max-w-6xl to max-w-6xl -->

    <div class="text-center text-white space-y-2 drop-shadow-md mb-10">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.32em]">
            <i data-lucide="heart-pulse" class="h-4 w-4"></i>
            Registrasi
        </span>

        <h1 class="text-4xl font-bold">Daftar sebagai Pasien</h1>

        <p class="text-white/90">
            Lengkapi data berikut untuk membuat akun pasien Anda.
        </p>
    </div>

    <div class="w-full rounded-3xl bg-white/85 backdrop-blur-md p-12 shadow-2xl border border-white/40 w-full"> <!-- widened padding -->
        <form action="{{ route('register.store', [], false) }}" method="POST" class="grid gap-6 md:grid-cols-2">
            @csrf

            @php
                $inputClasses = 'w-full rounded-xl border border-emerald-300 bg-white/70 px-4 py-3 text-sm text-emerald-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-300';
                $labelClasses = 'flex items-center gap-2 text-sm font-medium text-emerald-800';
            @endphp

            <!-- NAME -->
            <div class="space-y-2 md:col-span-2">
                <label for="name" class="{{ $labelClasses }}">
                    <i data-lucide="user" class="h-4 w-4"></i> Nama lengkap
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="{{ $inputClasses }}">
            </div>

            <!-- EMAIL -->
            <div class="space-y-2">
                <label for="email" class="{{ $labelClasses }}">
                    <i data-lucide="mail" class="h-4 w-4"></i> Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="{{ $inputClasses }}">
            </div>

            <!-- PHONE -->
            <div class="space-y-2">
                <label for="phone" class="{{ $labelClasses }}">
                    <i data-lucide="phone" class="h-4 w-4"></i> Nomor telepon
                </label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="{{ $inputClasses }}">
            </div>

            <!-- GENDER -->
            <div class="space-y-2">
                <label class="{{ $labelClasses }}">
                    <i data-lucide="venus" class="h-4 w-4"></i> Jenis kelamin
                </label>

                <select name="gender" class="{{ $inputClasses }}">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <!-- DOB -->
            <div class="space-y-2">
                <label for="date_of_birth" class="{{ $labelClasses }}">
                    <i data-lucide="calendar" class="h-4 w-4"></i> Tanggal lahir
                </label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" class="{{ $inputClasses }}">
            </div>

            <!-- ADDRESS -->
            <div class="space-y-2 md:col-span-2">
                <label for="address" class="{{ $labelClasses }}">
                    <i data-lucide="map-pin" class="h-4 w-4"></i> Alamat lengkap
                </label>
                <textarea id="address" name="address" rows="2" class="{{ $inputClasses }}">{{ old('address') }}</textarea>
            </div>

            <!-- PASSWORD -->
            <div class="space-y-2">
                <label for="password" class="{{ $labelClasses }}">
                    <i data-lucide="lock" class="h-4 w-4"></i> Password
                </label>
                <input type="password" id="password" name="password" required class="{{ $inputClasses }}">
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="space-y-2">
                <label for="password_confirmation" class="{{ $labelClasses }}">
                    <i data-lucide="lock-keyhole" class="h-4 w-4"></i> Konfirmasi password
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="{{ $inputClasses }}">
            </div>

            <!-- EMERGENCY CONTACT -->
            <div class="space-y-2">
                <label for="emergency_contact_name" class="{{ $labelClasses }}">
                    <i data-lucide="users" class="h-4 w-4"></i> Kontak darurat
                </label>
                <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="{{ $inputClasses }}">
            </div>

            <div class="space-y-2">
                <label for="emergency_contact_phone" class="{{ $labelClasses }}">
                    <i data-lucide="phone-call" class="h-4 w-4"></i> Telepon kontak darurat
                </label>
                <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="{{ $inputClasses }}">
            </div>

            <!-- BLOOD -->
            <div class="space-y-2">
                <label for="blood_type" class="{{ $labelClasses }}">
                    <i data-lucide="droplet" class="h-4 w-4"></i> Golongan darah
                </label>
                <input type="text" id="blood_type" name="blood_type" value="{{ old('blood_type') }}" class="{{ $inputClasses }}">
            </div>

            <!-- ALLERGIES -->
            <div class="space-y-2">
                <label for="allergies" class="{{ $labelClasses }}">
                    <i data-lucide="pill" class="h-4 w-4"></i> Alergi
                </label>
                <textarea id="allergies" name="allergies" rows="2" class="{{ $inputClasses }}">{{ old('allergies') }}</textarea>
            </div>

            <!-- MEDICAL HISTORY -->
            <div class="space-y-2 md:col-span-2">
                <label for="medical_history" class="{{ $labelClasses }}">
                    <i data-lucide="file-text" class="h-4 w-4"></i> Riwayat medis
                </label>
                <textarea id="medical_history" name="medical_history" rows="3" class="{{ $inputClasses }}">{{ old('medical_history') }}</textarea>
            </div>

            <!-- SUBMIT -->
            <div class="md:col-span-2">
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-emerald-700 transition">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    Kirim pendaftaran
                </button>
            </div>

            <p class="md:col-span-2 text-center text-sm text-emerald-900">
                Sudah punya akun?
                <a href="{{ route('login', [], false) }}" class="font-semibold underline">Masuk di sini</a>
            </p>
        </form>
    </div>

</div>

@endsection
