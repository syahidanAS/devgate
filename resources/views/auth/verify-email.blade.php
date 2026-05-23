@extends('layouts.guest')

@section('content')
{{-- Heading --}}
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-500/10 text-indigo-400 mb-4 ring-1 ring-indigo-500/20">
        <i class="fa-regular fa-envelope-open text-2xl"></i>
    </div>
    <p class="auth-heading text-xl">Verifikasi Email Anda</p>
</div>

<div class="text-sm text-slate-400 text-center mb-6 leading-relaxed">
    Terima kasih telah mendaftar! Sebelum memulai, harap verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan. Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkan tautan yang baru.
</div>

{{-- Session Status --}}
@if (session('status') == 'verification-link-sent')
    <div class="flash-success text-center justify-center mb-6">
        <i class="fa-solid fa-circle-check"></i> Tautan verifikasi baru telah dikirim!
    </div>
@endif

{{-- Form Grid --}}
<div class="form-grid">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="submit-btn w-full">
            <i class="fa-solid fa-paper-plane"></i> Kirim Ulang Tautan Verifikasi
        </button>
    </form>

    <div class="divider mt-2 mb-2"><div class="divider-inner"><span>ATAU</span></div></div>

    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <button type="submit" class="text-sm font-semibold text-slate-400 hover:text-white transition-colors duration-200">
            <i class="fa-solid fa-right-from-bracket mr-1"></i> Keluar
        </button>
    </form>
</div>
@endsection
