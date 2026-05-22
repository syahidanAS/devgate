@extends('layouts.cms')

@section('title', 'Tambah Pengguna Baru — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.users.index') }}" class="hover:text-white transition-colors">Pengguna</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Tambah Baru</span>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-user-plus text-indigo-500"></i> Tambah Pengguna Baru
            </h1>
            <p class="text-slate-400 text-xs mt-1">Daftarkan akun pengguna CMS baru secara instan dan tentukan hak akses peran.</p>
        </div>
        <a 
            href="{{ route('cms.users.index') }}" 
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-800 bg-slate-900/50 text-xs font-bold text-slate-400 hover:bg-slate-900 hover:text-white transition-all"
        >
            <i class="fa-solid fa-arrow-left-long"></i> Kembali
        </a>
    </div>

    <!-- Create Card Form -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl p-6 sm:p-8">

        <form action="{{ route('cms.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Identity Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="space-y-2">
                    <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        placeholder="Misal: Ahmad Dani" 
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                        required
                    >
                    @error('name')
                        <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div class="space-y-2">
                    <label for="username" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        value="{{ old('username') }}" 
                        placeholder="Misal: ahmad_dani" 
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                        required
                    >
                    @error('username')
                        <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    placeholder="Misal: dani@devgate.id" 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                    required
                >
                @error('email')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Minimal 8 karakter" 
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                        required
                    >
                    @error('password')
                        <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        placeholder="Ulangi password" 
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                        required
                    >
                </div>
            </div>

            <!-- Account Status Select -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Status Akun Awal</label>
                <div class="grid grid-cols-2 gap-4" x-data="{ active: true }">
                    <!-- Aktif -->
                    <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer select-none transition-all" :class="active ? 'bg-indigo-600/10 border-indigo-500/30' : 'bg-slate-900/20 border-slate-800/80'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-white">Langsung Aktif</span>
                            <input type="radio" name="is_active" value="1" @click="active = true" class="text-indigo-500 focus:ring-indigo-500 h-4 w-4 bg-slate-955 border-slate-800" checked>
                        </div>
                        <span class="text-[10px] text-slate-500">User dapat langsung masuk ke dashboard setelah didaftarkan.</span>
                    </label>

                    <!-- Ditangguhkan -->
                    <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer select-none transition-all" :class="!active ? 'bg-rose-550/10 border-rose-500/30' : 'bg-slate-900/20 border-slate-800/80'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-white">Ditangguhkan</span>
                            <input type="radio" name="is_active" value="0" @click="active = false" class="text-indigo-500 focus:ring-indigo-500 h-4 w-4 bg-slate-955 border-slate-800">
                        </div>
                        <span class="text-[10px] text-slate-500">Membekukan akun segera setelah didaftarkan hingga diaktifkan manual.</span>
                    </label>
                </div>
            </div>

            <!-- Role Assignment -->
            <div class="space-y-3 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Peran & Hak Akses (Roles)</label>
                    <p class="text-[10px] text-slate-500 mt-0.5">Tentukan minimal satu hak akses untuk pengguna baru ini.</p>
                </div>
                
                <div class="space-y-3">
                    @foreach($roles as $role)
                        @php
                            $roleDescriptions = [
                                'superadmin'        => 'Akses penuh ke seluruh sistem, manajemen pengguna, keuangan, produk, dan konten.',
                                'author'            => 'Akses menulis, mengedit, mengunggah media, dan menerbitkan artikel teknologi di platform.',
                                'admin-marketplace' => 'Akses penuh pengelolaan stok barang elektronik/IoT, konfirmasi order, dan resi kurir.',
                            ];
                            $roleTitle = [
                                'superadmin'        => 'Super Administrator',
                                'author'            => 'Writer / Author (Content Creator)',
                                'admin-marketplace' => 'Marketplace Shop Admin',
                            ];
                            $roleColors = [
                                'superadmin'        => 'text-rose-400',
                                'author'            => 'text-emerald-400',
                                'admin-marketplace' => 'text-amber-400',
                            ];
                        @endphp
                        <label class="flex items-start gap-3.5 p-4 rounded-2xl border border-slate-800/85 bg-slate-900/20 hover:bg-slate-900/40 cursor-pointer select-none transition-all">
                            <input 
                                type="checkbox" 
                                name="roles[]" 
                                value="{{ $role->name }}" 
                                class="rounded border-slate-800 bg-slate-950 text-indigo-500 focus:ring-indigo-500 mt-1 h-4.5 w-4.5"
                                {{ old('roles') && in_array($role->name, old('roles')) ? 'checked' : '' }}
                            >
                            <div class="leading-tight">
                                <span class="text-xs font-bold {{ $roleColors[$role->name] ?? 'text-white' }}">
                                    {{ $roleTitle[$role->name] ?? $role->name }}
                                </span>
                                <p class="text-[10px] text-slate-500 mt-1">
                                    {{ $roleDescriptions[$role->name] ?? 'Hak akses reguler sistem.' }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Submission Action Controls -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-800/60">
                <a 
                    href="{{ route('cms.users.index') }}" 
                    class="px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-900/30 text-xs font-bold text-slate-400 hover:bg-slate-900 hover:text-white transition-all"
                >
                    Batalkan
                </a>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all"
                >
                    Daftarkan Pengguna
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
