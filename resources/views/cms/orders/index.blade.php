@extends('layouts.cms')

@section('title', 'Kelola Pesanan — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Marketplace</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Pesanan</span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Page Header & Action Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-800/60 pb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-receipt text-indigo-500"></i> Kelola Pesanan Pembeli
            </h1>
            <p class="text-slate-450 text-xs mt-1">Pantau status pembayaran, proses pengiriman paket, dan input nomor resi kurir belanja.</p>
        </div>
    </div>

    <!-- Interactive Status Filters -->
    <div class="flex flex-wrap gap-2 items-center">
        @php
            $currentStatus = request('status');
            $statuses = [
                ''                  => 'Semua Pesanan',
                'pending'           => 'Menunggu Konfirmasi',
                'awaiting_payment'  => 'Menunggu Bayar',
                'paid'              => 'Sudah Dibayar',
                'processing'        => 'Sedang Diproses',
                'shipped'           => 'Dikirim',
                'delivered'         => 'Sampai Tujuan',
                'completed'         => 'Selesai',
                'refunding'         => 'Pengajuan Refund',
                'refunded'          => 'Refund Selesai',
                'cancelled'         => 'Dibatalkan',
            ];
            
            $statusColors = [
                ''                  => 'hover:bg-slate-800/80 text-slate-300',
                'pending'           => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                'awaiting_payment'  => 'bg-yellow-500/10 border-yellow-500/20 text-yellow-400',
                'paid'              => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
                'processing'        => 'bg-cyan-500/10 border-cyan-500/20 text-cyan-400',
                'shipped'           => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
                'delivered'         => 'bg-teal-500/10 border-teal-500/20 text-teal-400',
                'completed'         => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                'refunding'         => 'bg-amber-550/10 border-amber-550/20 text-amber-400',
                'refunded'          => 'bg-purple-500/10 border-purple-500/20 text-purple-400',
                'cancelled'         => 'bg-rose-500/10 border-rose-500/20 text-rose-455',
            ];
        @endphp

        @foreach($statuses as $key => $label)
            <a 
                href="{{ route('cms.orders.index', $key ? ['status' => $key] : []) }}"
                class="inline-flex items-center px-3 py-1.5 rounded-xl border text-xs font-bold transition-all {{ $currentStatus === $key ? 'bg-indigo-600 border-indigo-550 text-white shadow-md shadow-indigo-600/20' : 'bg-slate-950/60 border-slate-850 text-slate-400 hover:text-white' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Orders Table Card -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60 bg-slate-950/60">
                        <th class="py-4 px-6">No. Pesanan</th>
                        <th class="py-4 px-6">Pelanggan & Tanggal</th>
                        <th class="py-4 px-6">Total Belanja</th>
                        <th class="py-4 px-6">Pengiriman</th>
                        <th class="py-4 px-6">Status Pesanan</th>
                        <th class="py-4 px-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($orders as $order)
                        <tr class="group hover:bg-slate-900/20 transition-all">
                            <!-- Order Number -->
                            <td class="py-4 px-6">
                                <div class="text-xs font-mono font-bold text-indigo-400 group-hover:underline">
                                    <a href="{{ route('cms.orders.show', $order->id) }}">
                                        #{{ $order->order_number }}
                                    </a>
                                </div>
                                <div class="text-[9px] text-slate-550 mt-1 flex items-center gap-1 font-semibold uppercase">
                                    <i class="fa-regular fa-clock"></i> {{ $order->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Customer & Date -->
                            <td class="py-4 px-6">
                                <div class="text-xs font-bold text-white leading-tight">
                                    {{ $order->user ? $order->user->name : 'Akun Terhapus' }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1 font-mono">
                                    {{ $order->created_at->format('d M Y, H:i') }} WIB
                                </div>
                            </td>

                            <!-- Total Price -->
                            <td class="py-4 px-6">
                                <div class="text-xs font-extrabold text-white">
                                    {{ $order->formatted_total }}
                                </div>
                                <div class="text-[9px] text-slate-500 mt-0.5 font-semibold">
                                    Ongkir: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                </div>
                            </td>

                            <!-- Courier & Tracking -->
                            <td class="py-4 px-6 text-slate-350">
                                <div class="text-xs font-bold flex items-center gap-1.5 uppercase">
                                    <i class="fa-solid fa-truck-ramp-box text-slate-600 text-[10px]"></i> 
                                    {{ $order->courier }} ({{ $order->courier_service }})
                                </div>
                                <div class="text-[10px] text-slate-450 mt-1 font-mono">
                                    @if($order->tracking_number)
                                        Resi: <span class="text-cyan-400 font-semibold">{{ $order->tracking_number }}</span>
                                    @else
                                        <span class="text-slate-600 italic">Resi Belum Diinput</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-6">
                                @php
                                    $badgeStyle = match ($order->status) {
                                        'pending'           => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                                        'awaiting_payment'  => 'bg-yellow-500/10 border-yellow-500/20 text-yellow-400',
                                        'paid'              => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
                                        'processing'        => 'bg-cyan-500/10 border-cyan-500/20 text-cyan-400',
                                        'shipped'           => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
                                        'delivered'         => 'bg-teal-500/10 border-teal-500/20 text-teal-400',
                                        'completed'         => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                        'refunding'         => 'bg-amber-550/10 border-amber-550/20 text-amber-400',
                                        'refunded'          => 'bg-purple-500/10 border-purple-500/20 text-purple-400',
                                        'cancelled'         => 'bg-rose-500/10 border-rose-500/20 text-rose-455',
                                        default             => 'bg-slate-800 border-slate-700 text-slate-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider {{ $badgeStyle }}">
                                    @if(in_array($order->status, ['paid', 'processing', 'shipped']))
                                        <span class="h-1.5 w-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                                    @endif
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <!-- Actions edit/delete buttons -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center">
                                    <a 
                                        href="{{ route('cms.orders.show', $order->id) }}" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-350 hover:bg-indigo-600 hover:border-indigo-500 hover:text-white transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-file-invoice-dollar text-[10px]"></i> Detail Invoice
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="h-16 w-16 rounded-full bg-slate-900/60 border border-slate-850 flex items-center justify-center text-slate-500 text-xl">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <p class="text-sm">Tidak ada pesanan masuk ditemukan.</p>
                                    @if(request('status'))
                                        <a href="{{ route('cms.orders.index') }}" class="text-xs text-indigo-400 hover:underline">Hapus Filter Status</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination controls -->
        @if($orders->hasPages())
            <div class="border-t border-slate-800/60 px-6 py-4 bg-slate-950/30">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
