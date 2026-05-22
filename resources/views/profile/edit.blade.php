@extends('layouts.app')

@section('title', 'Profil Saya — DevGate')

@section('content')
<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-600/20">
            <i class="fa-regular fa-user text-lg"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Profil Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola informasi akun dan alamat pengirimanmu</p>
        </div>
    </div>

    {{-- ─────────────────────────────────────────── --}}
    {{-- Card: Profile Information --}}
    {{-- ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- ─────────────────────────────────────────── --}}
    {{-- Card: Shipping Addresses --}}
    {{-- ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
        @include('profile.partials.manage-addresses')
    </div>

    {{-- ─────────────────────────────────────────── --}}
    {{-- Card: Change Password --}}
    {{-- ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
        @include('profile.partials.update-password-form')
    </div>

    {{-- ─────────────────────────────────────────── --}}
    {{-- Card: Delete Account --}}
    {{-- ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-rose-200 dark:border-rose-900/40 bg-white dark:bg-slate-800/50 p-6 shadow-sm">
        @include('profile.partials.delete-user-form')
    </div>

</div>
@endsection
