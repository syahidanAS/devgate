@extends('layouts.cms')

@section('title', 'Kelola Pengguna — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Pengguna</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Daftar</span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-800/60 pb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-users-gear text-indigo-500"></i> Manajemen Pengguna
            </h1>
            <p class="text-slate-450 text-xs mt-1">Administrasi hak akses peran (roles), izin akun, dan pemblokiran akses sistem.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Search input form -->
            <form action="{{ route('cms.users.index') }}" method="GET" class="relative max-w-sm w-full sm:w-64">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama, email, username..." 
                    class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 pl-11 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all backdrop-blur-sm"
                >
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-500 text-xs"></i>
                @if(request('search'))
                    <a href="{{ route('cms.users.index') }}" class="absolute right-4 top-3.5 text-slate-500 hover:text-white transition-colors">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                    </a>
                @endif
            </form>

            <a 
                href="{{ route('cms.users.create') }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all w-full sm:w-auto justify-center"
            >
                <i class="fa-solid fa-user-plus text-[10px]"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60 bg-slate-950/60">
                        <th class="py-4 px-6">Identitas Pengguna</th>
                        <th class="py-4 px-6">Informasi Kontak</th>
                        <th class="py-4 px-6">Hak Akses (Roles)</th>
                        <th class="py-4 px-6">Status Akun</th>
                        <th class="py-4 px-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($users as $user)
                        <tr class="group hover:bg-slate-900/20 transition-all">
                            <!-- Avatar & Name -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 flex items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-500 text-white font-extrabold text-sm shadow-inner uppercase">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors leading-tight">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-1 font-mono">
                                            &#64;{{ $user->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email Contact -->
                            <td class="py-4 px-6 text-slate-300">
                                <div class="text-xs font-semibold flex items-center gap-2">
                                    <i class="fa-regular fa-envelope text-slate-500 text-[10px]"></i> {{ $user->email }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1">
                                    Mendaftar pada {{ $user->created_at->format('d M Y') }}
                                </div>
                            </td>

                            <!-- Role Badges (Spatie roles integration) -->
                            <td class="py-4 px-6">
                                <div class="flex flex-wrap gap-1.5 max-w-[200px]">
                                    @forelse($user->roles as $role)
                                        @php
                                            $roleColors = [
                                                'superadmin'        => 'bg-rose-500/10 border-rose-500/20 text-rose-400',
                                                'author'            => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                                'admin-marketplace' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                                            ];
                                            $badgeColor = $roleColors[$role->name] ?? 'bg-slate-800 border-slate-750 text-slate-400';
                                        @endphp
                                        <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider {{ $badgeColor }}">
                                            {{ str_replace('-', ' ', $role->name) }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center rounded-lg border border-slate-800 bg-slate-900/50 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-500">
                                            customer
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Active Status -->
                            <td class="py-4 px-6">
                                @if($user->is_active)
                                    <span class="inline-flex items-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-rose-500/20 bg-rose-500/10 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-rose-455">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-550 mr-1.5"></span> ditangguhkan
                                    </span>
                                @endif
                            </td>

                            <!-- Actions edit/delete buttons -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a 
                                        href="{{ route('cms.users.edit', $user->id) }}" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-350 hover:bg-indigo-605 hover:border-indigo-550 hover:text-white transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-user-pen text-[10px]"></i> Edit
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('cms.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-rose-400 hover:bg-rose-600 hover:border-rose-500 hover:text-white transition-all shadow-sm"
                                            >
                                                <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 text-xs">
                                <i class="fa-solid fa-user-slash text-4xl text-slate-700 mb-3 block"></i>
                                Tidak ditemukan pengguna yang cocok dengan kata kunci pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination custom footer layout -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-850 bg-slate-950/20">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
