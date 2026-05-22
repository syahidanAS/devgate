@extends('layouts.guest')

@section('content')
{{-- Heading --}}
<p class="auth-heading">Buat Akun Baru</p>
<p class="auth-sub">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

{{-- Google OAuth --}}
<a href="{{ route('auth.google') }}" class="google-btn">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
    </svg>
    Daftar dengan Google
</a>

{{-- Divider --}}
<div class="divider"><div class="divider-inner"><span>atau daftar dengan email</span></div></div>

{{-- Form --}}
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-grid">

        {{-- Name --}}
        <div>
            <label for="name" class="field-label">Nama Lengkap</label>
            <div class="input-wrap">
                <i class="fa-regular fa-user input-icon"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                       placeholder="Nama lengkap kamu" class="auth-input">
            </div>
            @error('name')<p class="error-msg"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="field-label">Alamat Email</label>
            <div class="input-wrap">
                <i class="fa-regular fa-envelope input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                       placeholder="nama@email.com" class="auth-input">
            </div>
            @error('email')<p class="error-msg"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="field-label">Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock input-icon"></i>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       placeholder="Min. 8 karakter" class="auth-input">
                <button type="button" class="toggle-pw" id="toggle-pw-btn">
                    <i class="fa-solid fa-eye" id="toggle-pw-icon"></i>
                </button>
            </div>
            @error('password')<p class="error-msg"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="field-label">Konfirmasi Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock input-icon"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       placeholder="Ulangi password" class="auth-input">
                <button type="button" class="toggle-pw" id="toggle-pw2-btn">
                    <i class="fa-solid fa-eye" id="toggle-pw2-icon"></i>
                </button>
            </div>
            @error('password_confirmation')<p class="error-msg"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        {{-- CAPTCHA --}}
        <div>
            <label class="field-label"><i class="fa-solid fa-shield-halved" style="color:#6366f1;margin-right:4px"></i>Verifikasi Keamanan</label>
            <div class="captcha-wrap">
                <div class="captcha-img-box" onclick="document.getElementById('captcha-img-reg').src='{{ captcha_src() }}'+Math.random()" title="Klik untuk refresh">
                    <img src="{{ captcha_src() }}" alt="captcha" id="captcha-img-reg">
                </div>
                <input id="captcha" type="text" name="captcha" required placeholder="Ketik teks di gambar" class="auth-input no-icon" style="flex:1">
            </div>
            <p class="captcha-hint"><i class="fa-solid fa-rotate" style="margin-right:4px"></i>Klik gambar untuk refresh</p>
            @error('captcha')<p class="error-msg"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        {{-- Terms --}}
        <p class="terms-note">
            Dengan mendaftar, kamu menyetujui <a href="#">Syarat &amp; Ketentuan</a> dan <a href="#">Kebijakan Privasi</a> DevGate.
        </p>

        {{-- Submit --}}
        <button type="submit" class="submit-btn">
            <i class="fa-solid fa-user-plus"></i> Buat Akun Sekarang
        </button>

    </div>
</form>

<script>
    function pwToggle(btnId, iconId, inputId) {
        document.getElementById(btnId).addEventListener('click', () => {
            const el = document.getElementById(inputId);
            const ic = document.getElementById(iconId);
            const show = el.type === 'password';
            el.type = show ? 'text' : 'password';
            ic.className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        });
    }
    pwToggle('toggle-pw-btn',  'toggle-pw-icon',  'password');
    pwToggle('toggle-pw2-btn', 'toggle-pw2-icon', 'password_confirmation');
</script>
@endsection
