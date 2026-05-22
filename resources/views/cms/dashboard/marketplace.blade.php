@extends('layouts.cms')

@section('title', 'Marketplace Dashboard — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Marketplace Dashboard</span>
</div>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- Hero Banner Welcome -->
    <div class="relative overflow-hidden rounded-3xl border border-indigo-500/20 bg-gradient-to-r from-slate-950 via-slate-900/60 to-indigo-950/40 p-6 sm:p-8 shadow-xl">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-500/10 via-transparent to-transparent pointer-events-none"></div>
        <div class="relative z-10 max-w-xl">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-cyan-500/10 px-3 py-1 text-xs font-bold text-cyan-400 border border-cyan-500/20 mb-4">
                <i class="fa-solid fa-store text-[10px]"></i> Pengelola Toko Aktif
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                Dasbor Marketplace IoT,<br>
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">{{ Auth::user()->name }}</span>
            </h1>
            <p class="mt-2 text-slate-400 text-sm leading-relaxed">
                Portal khusus administrasi penjualan komponen elektronik dan IoT. Anda dapat memantau pesanan masuk, mengunggah produk baru, melacak stok inventaris, serta memperbarui resi pengiriman barang.
            </p>
        </div>
    </div>

    <!-- Stats Summary Cards (Four Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Sales -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-indigo-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-cart-shopping text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['total_sales']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Pesanan Diproses</span>
            </div>
        </div>

        <!-- Total Products -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-cyan-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Katalog Produk</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($stats['total_products']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Varian Komponen</span>
            </div>
        </div>

        <!-- Low Stock warning card -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-rose-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Menipis</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-450 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-extrabold @if($stats['low_stock'] > 0) text-rose-400 @else text-white @endif tracking-tight">{{ number_format($stats['low_stock']) }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Perlu Restock</span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="relative group rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 transition-all hover:border-emerald-500/40 hover:bg-slate-950/60 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Omzet</span>
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 group-hover:scale-105 transition-all">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-400 tracking-tight">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1 block">Dana Diterima</span>
            </div>
        </div>

    </div>

    <!-- Main Splitted Info: Recent Orders and Low Stock alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Recent Orders table list (Col span 2) -->
        <div class="lg:col-span-2 rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800/60 pb-5 mb-5">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-indigo-500"></i> Aktivitas Pembelian Terbaru
                    </h3>
                    <a href="{{ route('cms.orders.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">Kelola Pesanan</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/40 pb-3">
                                <th class="pb-3">No. Order</th>
                                <th class="pb-3">Pembeli</th>
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
                                    <td class="py-3.5 text-right font-bold text-white text-xs font-mono">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 text-xs">Belum ada aktivitas pesanan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Inventori warning stock (Col span 1) -->
        <div class="lg:col-span-1 rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl flex flex-col justify-between">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-white mb-2">Peringatan Inventori</h3>
                    <p class="text-slate-400 text-xs leading-relaxed">Produk di bawah ini memiliki tingkat persediaan stok sangat kritis (&le; 5 item).</p>
                </div>

                <div class="space-y-3">
                    @forelse($lowStockProducts as $prod)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl border border-rose-500/15 bg-rose-500/5 group hover:bg-rose-500/10 hover:border-rose-500/30 transition-all">
                            <div class="overflow-hidden">
                                <h4 class="text-xs font-bold text-white truncate leading-tight group-hover:text-rose-400 transition-colors">
                                    {{ $prod->name }}
                                </h4>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500 mt-1 block">Brand: {{ $prod->brand }}</span>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-300 font-extrabold">
                                Sisa {{ $prod->stock }}
                            </span>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-500 text-xs border border-dashed border-slate-850 rounded-2xl">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-2xl mb-3 block"></i>
                            Stok semua komponen IoT aman dan terpantau dengan baik.
                        </div>
                    @endforelse
                </div>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('cms.products.create') }}" class="flex items-center justify-between p-4 rounded-2xl border border-indigo-500/20 bg-indigo-500/5 hover:bg-indigo-500/10 hover:border-indigo-500/40 text-sm font-bold text-indigo-300 transition-all group">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-plus-circle text-base"></i> Tambah Produk Baru
                        </span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
