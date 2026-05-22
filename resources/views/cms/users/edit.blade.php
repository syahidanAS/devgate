@extends('layouts.cms')

@section('title', 'Edit Pengguna — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.users.index') }}" class="hover:text-white transition-colors">Pengguna</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Edit</span>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-user-pen text-indigo-500"></i> Edit Pengguna
            </h1>
            <p class="text-slate-400 text-xs mt-1">Ubah peran (roles) dan status keaktifan akun user secara langsung.</p>
        </div>
        <a 
            href="{{ route('cms.users.index') }}" 
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-800 bg-slate-900/50 text-xs font-bold text-slate-400 hover:bg-slate-900 hover:text-white transition-all"
        >
            <i class="fa-solid fa-arrow-left-long"></i> Kembali
        </a>
    </div>

    <!-- Edit Card Form -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl p-6 sm:p-8">
        
        <!-- User Metadata Header Display -->
        <div class="flex items-center gap-4 pb-6 border-b border-slate-800/60 mb-6">
            <div class="h-14 w-14 shrink-0 flex items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-500 text-white font-extrabold text-lg shadow-inner uppercase">
                {{ substr($user->name, 0, 2) }}
            </div>
            <div>
                <h3 class="text-lg font-bold text-white leading-snug">{{ $user->name }}</h3>
                <p class="text-xs text-slate-400 font-mono">&#64;{{ $user->username }} &bull; {{ $user->email }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Mendaftar pada {{ $user->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <form action="{{ route('cms.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Account Status Toggle -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Status Akun</label>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Aktif -->
                    <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer select-none transition-all {{ $user->is_active ? 'bg-indigo-600/10 border-indigo-500/30' : 'bg-slate-900/20 border-slate-800/80' }} hover:border-slate-750">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-white">Aktif</span>
                            <input type="radio" name="is_active" value="1" class="text-indigo-500 focus:ring-indigo-500 h-4 w-4 bg-slate-955 border-slate-800" {{ $user->is_active ? 'checked' : '' }}>
                        </div>
                        <span class="text-[10px] text-slate-500">User dapat login dan mengakses dashboard/fitur sesuai peran mereka.</span>
                    </label>

                    <!-- Ditangguhkan (Suspended) -->
                    <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer select-none transition-all {{ !$user->is_active ? 'bg-rose-550/10 border-rose-500/30' : 'bg-slate-900/20 border-slate-800/80' }} hover:border-slate-750">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-white">Ditangguhkan</span>
                            <input type="radio" name="is_active" value="0" class="text-indigo-500 focus:ring-indigo-500 h-4 w-4 bg-slate-955 border-slate-800" {{ !$user->is_active ? 'checked' : '' }}>
                        </div>
                        <span class="text-[10px] text-slate-500">Membekukan akses login user secara langsung ke seluruh platform.</span>
                    </label>
                </div>
                @error('is_active')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Assignment (Spatie Permission Integration) -->
            <div class="space-y-3 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Peran & Hak Akses (Roles)</label>
                    <p class="text-[10px] text-slate-500 mt-0.5">Pilih minimal satu peran. Pilihan peran menentukan akses ke menu CMS.</p>
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
                                {{ $user->hasRole($role->name) ? 'checked' : '' }}
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
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition-all"
                >
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
