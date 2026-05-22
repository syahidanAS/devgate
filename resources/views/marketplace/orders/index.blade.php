@extends('layouts.app')

@section('title', 'Daftar Pesanan — DevGate')

@section('content')
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl mb-8">
            Daftar Pesanan
        </h1>

        <div class="flex flex-col gap-4">
            @forelse($orders as $order)
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm transition hover:shadow-md">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                        <div>
                            <p class="text-xs text-slate-500 font-semibold mb-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Order #{{ $order->order_number }}</h3>
                        </div>
                        <div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $order->status === 'completed' ? 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' : ($order->status === 'awaiting_payment' ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20' : 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20') }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-4">
                            @if($order->items->count() > 0)
                                <div class="h-16 w-16 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 overflow-hidden flex items-center justify-center">
                                    @if($order->items->first()->product && $order->items->first()->product->getFirstMediaUrl('product-images'))
                                        <img src="{{ $order->items->first()->product->getFirstMediaUrl('product-images', 'thumbnail') }}" alt="{{ $order->items->first()->product_name }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fa-solid fa-box text-slate-400 text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $order->items->first()->product_name }}</p>
                                    @if($order->items->count() > 1)
                                        <p class="text-xs text-slate-500 mt-1">+ {{ $order->items->count() - 1 }} produk lainnya</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 w-full sm:w-auto mt-4 sm:mt-0">
                            <p class="text-xs text-slate-500">Total Belanja</p>
                            <p class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400">{{ $order->formatted_total }}</p>
                            <a href="{{ route('orders.show', $order->order_number) }}" class="w-full sm:w-auto mt-2 text-center rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 p-12 text-center dark:border-slate-700">
                    <i class="fa-solid fa-basket-shopping text-4xl text-slate-300 dark:text-slate-600 mb-4"></i>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Belum ada pesanan</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Mulai belanja komponen hardware dan module IoT sekarang!</p>
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-600/20">
                        Eksplorasi Katalog
                    </a>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
