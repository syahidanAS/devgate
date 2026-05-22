@extends('layouts.cms')

@section('title', 'Superadmin Dashboard — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Dashboard</span>
</div>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- Hero Banner Welcome -->
    <div class="relative overflow-hidden rounded-3xl border border-indigo-500/20 bg-gradient-to-r from-slate-950 via-slate-900/60 to-indigo-950/40 p-6 sm:p-8 shadow-xl">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-500/10 via-transparent to-transparent pointer-events-none"></div>
        <div class="relative z-10 max-w-xl">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-bold text-indigo-400 border border-indigo-500/20 mb-4">
                <i class="fa-solid fa-shield-halved text-[10px]"></i> Kontrol Superadmin Aktif
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                Selamat Datang Kembali,<br>
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">{{ Auth::user()->name }}</span>
            </h1>
            <p class="mt-2 text-slate-400 text-sm leading-relaxed">
                Ini adalah portal kontrol pusat DevGate. Anda memiliki akses penuh ke manajemen pengguna, moderasi artikel, inventaris barang marketplace, serta monitoring transaksi.
            </p>
        </div>
    </div>

    <!-- Stats Summary Cards (Four Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Users -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-indigo-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['total_users']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Akun Terdaftar</span>
            </div>
        </div>

        <!-- Total Articles -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-cyan-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Artikel</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-450 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-newspaper text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['total_articles']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Wawasan & Panduan</span>
            </div>
        </div>

        <!-- Total Products -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-violet-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Produk IoT</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-500/10 border border-violet-500/20 text-violet-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-microchip text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['total_products']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Komponen Tersedia</span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-emerald-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-450 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-400 tracking-tight">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Transaksi Sukses</span>
            </div>
        </div>

    </div>

    <!-- Revenue Graph / Chart Section -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-800/60 pb-5 mb-6 gap-4">
            <div>
                <h3 class="text-lg font-bold text-white">Tren Pendapatan Penjualan</h3>
                <p class="text-slate-450 text-xs mt-1">Grafik pendapatan terkumpul selama 7 hari terakhir.</p>
            </div>
            <span class="text-xs px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-slate-400 font-semibold flex items-center gap-2 self-start sm:self-auto">
                <i class="fa-solid fa-calendar-days text-indigo-400"></i> Past 7 Days
            </span>
        </div>

        @if($chartData->count() > 0)
            <!-- Premium CSS/Blade Scaling Bar Chart -->
            <div class="h-64 flex items-end gap-3 sm:gap-6 pt-6 overflow-x-auto">
                @php 
                    $maxVal = $chartData->max('revenue') ?: 1; 
                @endphp
                @foreach($chartData as $data)
                    @php 
                        $percentage = ($data->revenue / $maxVal) * 100;
                    @endphp
                    <div class="flex-grow flex flex-col items-center min-w-[55px] h-full justify-end group">
                        <div class="text-[10px] font-bold text-indigo-450 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            Rp{{ number_format($data->revenue / 1000, 0) }}k
                        </div>
                        <div 
                            class="w-full bg-gradient-to-t from-indigo-600 via-indigo-500 to-cyan-400 rounded-t-xl group-hover:from-indigo-500 group-hover:to-cyan-300 transition-all duration-500 shadow-lg shadow-indigo-600/10 hover:shadow-cyan-400/20"
                            style="height: {{ max($percentage, 8) }}%;"
                        ></div>
                        <span class="text-[10px] font-semibold text-slate-500 mt-3 whitespace-nowrap">
                            {{ date('d M', strtotime($data->date)) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty state chart -->
            <div class="flex flex-col items-center justify-center py-16 text-slate-500 border border-dashed border-slate-850 rounded-2xl">
                <i class="fa-solid fa-chart-line text-4xl mb-3 text-slate-700"></i>
                <p class="text-sm">Belum ada data transaksi penjualan pada 7 hari terakhir.</p>
            </div>
        @endif
    </div>

    <!-- Lower Split: Recent Orders and Articles -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Orders table card -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800/60 pb-5 mb-5">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-cart-flatbed text-indigo-500"></i> Transaksi Terbaru
                    </h3>
                    <a href="{{ route('cms.orders.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">Lihat Semua</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/40 pb-3">
                                <th class="pb-3">No. Order</th>
                                <th class="pb-3">Pelanggan</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @forelse($recentOrders as $order)
                                <tr class="group">
                                    <td class="py-3.5 font-mono text-xs font-bold text-slate-300 group-hover:text-indigo-400 transition-colors">
                                        #{{ $order->order_number }}
                                    </td>
                                    <td class="py-3.5">
                                        <div class="text-xs font-semibold text-white">{{ $order->user->name }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $order->user->email }}</div>
                                    </td>
                                    <td class="py-3.5">
                                        @php
                                            $statusColors = [
                                                'unpaid'            => 'bg-rose-500/10 border-rose-500/20 text-rose-450',
                                                'awaiting_payment'  => 'bg-amber-500/10 border-amber-500/20 text-amber-450',
                                                'paid'              => 'bg-cyan-500/10 border-cyan-500/20 text-cyan-400',
                                                'processing'        => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
                                                'shipped'           => 'bg-violet-500/10 border-violet-500/20 text-violet-400',
                                                'delivered'         => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                                'completed'         => 'bg-emerald-600/20 border-emerald-500/40 text-emerald-400',
                                                'cancelled'         => 'bg-slate-700/10 border-slate-700/20 text-slate-500',
                                            ];
                                            $color = $statusColors[$order->status] ?? 'bg-slate-850 border-slate-850 text-slate-400';
                                        @endphp
                                        <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider {{ $color }}">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 text-right font-bold text-white text-xs">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 text-xs">Belum ada order masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Articles Catalog card -->
        <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800/60 pb-5 mb-5">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-feather text-indigo-500"></i> Publikasi Artikel Baru
                    </h3>
                    <a href="{{ route('cms.articles.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">Lihat Semua</a>
                </div>

                <div class="space-y-4">
                    @forelse($recentArticles as $article)
                        <div class="flex items-center justify-between p-3 rounded-2xl border border-slate-850 bg-slate-900/30 hover:border-slate-800 hover:bg-slate-900/60 transition-all group">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="relative h-12 w-12 shrink-0 rounded-xl overflow-hidden bg-slate-800">
                                    <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="text-xs font-bold text-white truncate group-hover:text-indigo-400 transition-colors leading-tight">
                                        {{ $article->title }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-500">
                                        <span class="font-medium text-slate-400">{{ $article->author->name }}</span>
                                        <span>&bull;</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-800 text-indigo-300 text-[8px] font-bold uppercase tracking-wider">{{ $article->category->name }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="p-2 text-slate-400 hover:text-indigo-400 rounded-lg hover:bg-slate-800 transition-all shrink-0">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-500 text-xs">Belum ada artikel terbit.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
