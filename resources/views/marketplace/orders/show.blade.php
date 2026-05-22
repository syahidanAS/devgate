@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' — DevGate')

@section('content')
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-2 mb-4">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pesanan
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    Detail Pesanan <span class="text-indigo-600 dark:text-indigo-400">#{{ $order->order_number }}</span>
                </h1>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold ring-1 ring-inset {{ $order->status === 'completed' ? 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' : ($order->status === 'awaiting_payment' ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20' : 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20') }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>

        @if($order->refundRequest)
            <div class="mb-8 p-6 rounded-3xl border border-slate-200/60 bg-white/70 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-rose-500 animate-pulse"></i> Status Pengembalian Dana (Refund)
                </h3>
                
                <!-- Info Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/60 text-sm">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Metode Refund</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200">
                            {{ $order->refundRequest->refund_method === 'midtrans_api' ? 'Otomatis via Midtrans (QRIS/E-Wallet)' : 'Transfer Bank Manual (Virtual Account)' }}
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/60 text-sm">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Alasan Pembatalan</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $order->refundRequest->reason }}</div>
                    </div>
                    
                    @if($order->refundRequest->refund_method === 'manual_bank_transfer')
                        <div class="md:col-span-2 p-4 rounded-2xl bg-indigo-50/40 dark:bg-indigo-950/10 border border-indigo-100/60 dark:border-indigo-900/40 text-sm">
                            <div class="text-xs text-indigo-500 font-bold uppercase tracking-wider mb-2">Rekening Penerima Transfer:</div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-slate-700 dark:text-slate-300">
                                <div><span class="text-slate-400">Bank:</span> <strong class="text-slate-800 dark:text-slate-200">{{ strtoupper($order->refundRequest->bank_name) }}</strong></div>
                                <div><span class="text-slate-400">No. Rekening:</span> <strong class="text-slate-800 dark:text-slate-200">{{ $order->refundRequest->account_number }}</strong></div>
                                <div><span class="text-slate-400">Atas Nama:</span> <strong class="text-slate-800 dark:text-slate-200">{{ $order->refundRequest->account_holder }}</strong></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Visual Timeline -->
                <div class="relative">
                    <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-slate-200 dark:bg-slate-800 md:left-1/2 md:-ml-0.25"></div>
                    <div class="space-y-6">
                        <!-- Step 1: Diajukan -->
                        <div class="relative flex flex-col md:flex-row items-start md:items-center">
                            <div class="absolute left-2.5 md:left-1/2 md:-translate-x-1/2 flex items-center justify-center w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-emerald-100 dark:ring-emerald-950"></div>
                            <div class="ml-10 md:ml-0 md:w-1/2 md:pr-8 md:text-right">
                                <h4 class="font-bold text-slate-800 dark:text-slate-200">Refund Diajukan</h4>
                                <p class="text-xs text-slate-500 mt-1">Pengajuan pembatalan & pengembalian dana telah diterima sistem.</p>
                                <span class="inline-block mt-2 text-[10px] text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $order->refundRequest->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="md:w-1/2 md:pl-8"></div>
                        </div>

                        <!-- Step 2: Ditinjau Admin -->
                        <div class="relative flex flex-col md:flex-row items-start md:items-center">
                            @php
                                $isStep2Active = $order->refundRequest->status === 'pending';
                                $isStep2Done = in_array($order->refundRequest->status, ['approved', 'rejected']);
                                $step2Color = $isStep2Done ? 'bg-emerald-500 ring-emerald-100 dark:ring-emerald-950' : ($isStep2Active ? 'bg-amber-500 ring-amber-100 dark:ring-amber-950 animate-pulse' : 'bg-slate-200 ring-slate-100 dark:ring-slate-900');
                            @endphp
                            <div class="absolute left-2.5 md:left-1/2 md:-translate-x-1/2 flex items-center justify-center w-4 h-4 rounded-full {{ $step2Color }} ring-4"></div>
                            <div class="md:w-1/2"></div>
                            <div class="ml-10 md:ml-0 md:w-1/2 md:pl-8">
                                <h4 class="font-bold text-slate-800 dark:text-slate-200">Ditinjau oleh Admin</h4>
                                <p class="text-xs text-slate-500 mt-1">Admin sedang meninjau kelayakan pengajuan pengembalian dana Anda.</p>
                                @if($isStep2Active)
                                    <span class="inline-block mt-2 text-[10px] text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded font-semibold animate-pulse">Sedang Ditinjau</span>
                                @elseif($isStep2Done)
                                    <span class="inline-block mt-2 text-[10px] text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded font-semibold">Tinjauan Selesai</span>
                                @endif
                            </div>
                        </div>

                        <!-- Step 3: Selesai / Ditolak -->
                        <div class="relative flex flex-col md:flex-row items-start md:items-center">
                            @php
                                $isApproved = $order->refundRequest->status === 'approved';
                                $isRejected = $order->refundRequest->status === 'rejected';
                                $step3Color = $isApproved ? 'bg-emerald-500 ring-emerald-100 dark:ring-emerald-950' : ($isRejected ? 'bg-rose-500 ring-rose-100 dark:ring-rose-950' : 'bg-slate-200 ring-slate-100 dark:ring-slate-900');
                            @endphp
                            <div class="absolute left-2.5 md:left-1/2 md:-translate-x-1/2 flex items-center justify-center w-4 h-4 rounded-full {{ $step3Color }} ring-4"></div>
                            <div class="ml-10 md:ml-0 md:w-1/2 md:pr-8 md:text-right">
                                @if($isApproved)
                                    <h4 class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 justify-end">
                                        <i class="fa-solid fa-circle-check"></i> Refund Berhasil
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1">Dana telah dikembalikan ke rekening/e-wallet Anda.</p>
                                    @if($order->refundRequest->refund_reference)
                                        <div class="mt-2 text-xs text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded inline-block font-mono">
                                            Ref: {{ $order->refundRequest->refund_reference }}
                                        </div>
                                    @endif
                                    <span class="block mt-2 text-[10px] text-slate-400">{{ $order->refundRequest->refunded_at ? $order->refundRequest->refunded_at->format('d M Y, H:i') : '' }}</span>
                                @elseif($isRejected)
                                    <h4 class="font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1.5 justify-end">
                                        <i class="fa-solid fa-circle-xmark"></i> Refund Ditolak
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1">Pengajuan ditolak dengan alasan:</p>
                                    <p class="text-xs font-bold text-rose-700 dark:text-rose-400 mt-1 italic">"{{ $order->refundRequest->admin_notes }}"</p>
                                @else
                                    <h4 class="font-bold text-slate-400 dark:text-slate-600">Pengembalian Dana Selesai</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-600 mt-1">Menunggu keputusan peninjauan admin.</p>
                                @endif
                            </div>
                            <div class="md:w-1/2"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Items & Shipping -->
            <div class="lg:col-span-2 flex flex-col gap-8">
                
                <!-- Items Box -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-box-open text-indigo-500"></i> Produk yang Dipesan
                    </h2>
                    
                    <div class="flex flex-col gap-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex-shrink-0 flex items-center justify-center">
                                    @if($item->product && $item->product->getFirstMediaUrl('product-images'))
                                        <img src="{{ $item->product->getFirstMediaUrl('product-images', 'thumbnail') }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fa-solid fa-microchip text-slate-400"></i>
                                    @endif
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $item->product_name }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">SKU: {{ $item->product_sku }}</p>
                                    <div class="flex items-center gap-3 mt-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                        <span>Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                        <span class="text-slate-300 dark:text-slate-700">|</span>
                                        <span>{{ $item->quantity }} Pcs</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="h-px bg-slate-100 dark:bg-slate-800"></div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Address Box -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-indigo-500"></i> Pengiriman
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Kurir Pengiriman</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ strtoupper($order->courier) }} - {{ $order->courier_service }}</p>
                            @if($order->tracking_number)
                                <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 dark:bg-slate-800/50 dark:border-slate-700">
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Nomor Resi</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold font-mono tracking-widest text-indigo-600 dark:text-indigo-400">{{ $order->tracking_number }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alamat Tujuan</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ collect($order->shipping_address)->get('recipient_name', '-') }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ collect($order->shipping_address)->get('phone', '-') }}</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mt-2">
                                {{ collect($order->shipping_address)->get('address', '-') }},<br>
                                {{ collect($order->shipping_address)->get('city', '-') }}, {{ collect($order->shipping_address)->get('province', '-') }}<br>
                                {{ collect($order->shipping_address)->get('postal_code', '-') }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Summary & Payment -->
            <div class="flex flex-col gap-6">
                
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm sticky top-24">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                        Rincian Biaya
                    </h2>

                    <div class="flex flex-col gap-3 text-sm">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal Produk</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Ongkos Kirim</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>Diskon/Voucher</span>
                            <span class="font-bold">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="h-px bg-slate-200 dark:bg-slate-700 my-2"></div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Total Pembayaran</span>
                            <span class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    @if($order->status === 'awaiting_payment' && $order->payment)
                        <div class="mt-6 p-5 rounded-2xl border border-slate-100 bg-slate-50/50 dark:border-slate-800/80 dark:bg-slate-950/40 backdrop-blur-sm">
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-receipt text-indigo-500 animate-pulse"></i> Informasi Pembayaran
                            </h3>

                            @if($order->payment->expired_at)
                                <div class="mb-5 p-4 rounded-2xl border border-rose-100/60 bg-rose-50/30 text-rose-800 dark:border-rose-950/40 dark:bg-rose-950/20 dark:text-rose-400 flex items-center justify-between shadow-inner backdrop-blur-md"
                                     x-data="{
                                        expiryTime: new Date('{{ $order->payment->expired_at->toIso8601String() }}').getTime(),
                                        timeLeft: '00:00',
                                        isExpired: false,
                                        updateTimer() {
                                            const now = new Date().getTime();
                                            const diff = this.expiryTime - now;
                                            if (diff <= 0) {
                                                this.timeLeft = 'Kedaluwarsa';
                                                this.isExpired = true;
                                                clearInterval(this.interval);
                                                setTimeout(() => window.location.reload(), 2000);
                                                return;
                                            }
                                            const hours = Math.floor(diff / (1000 * 60 * 60));
                                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                            
                                            let timeStr = '';
                                            if (hours > 0) {
                                                timeStr += hours.toString().padStart(2, '0') + ':';
                                            }
                                            timeStr += minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
                                            this.timeLeft = timeStr;
                                        },
                                        init() {
                                            this.updateTimer();
                                            this.interval = setInterval(() => this.updateTimer(), 1000);
                                        }
                                     }">
                                    <span class="text-xs font-extrabold flex items-center gap-2">
                                        <i class="fa-regular fa-clock animate-pulse text-rose-500"></i> Batas Waktu Pembayaran
                                    </span>
                                    <span class="font-black font-mono text-sm tracking-widest bg-rose-100/50 dark:bg-rose-950/50 px-2.5 py-1 rounded-lg shadow-sm border border-rose-200/50 dark:border-rose-900/50" x-text="timeLeft"></span>
                                </div>
                            @endif

                            @if(in_array($order->payment->payment_method, ['bca', 'bni', 'bri']))
                                <!-- Bank Transfer VA Layout -->
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Bank</span>
                                        <span class="text-xs font-black italic text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-2.5 py-1 rounded-lg">
                                            {{ strtoupper($order->payment->payment_method) }} Virtual Account
                                        </span>
                                    </div>
                                    <div class="flex flex-col gap-1.5 p-3.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/60">
                                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Nomor Virtual Account</span>
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-base font-black font-mono tracking-widest text-slate-900 dark:text-white" id="va-number">{{ $order->payment->va_number }}</span>
                                            <button onclick="copyToClipboard('va-number', this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/40 text-xs font-bold text-indigo-600 dark:text-indigo-400 transition-colors">
                                                <i class="fa-regular fa-copy"></i> <span>Salin</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            @elseif($order->payment->payment_method === 'mandiri')
                                <!-- Mandiri Bill Payment / Echannel Layout -->
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Bank</span>
                                        <span class="text-xs font-black italic text-yellow-605 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-950/40 px-2.5 py-1 rounded-lg">
                                            MANDIRI E-Channel
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3">
                                        <!-- Biller Code -->
                                        <div class="flex flex-col gap-1.5 p-3.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/60">
                                            <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kode Biller</span>
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-base font-black font-mono tracking-wider text-slate-900 dark:text-white" id="biller-code">{{ $order->payment->payload['biller_code'] ?? '70012' }}</span>
                                                <button onclick="copyToClipboard('biller-code', this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-950/40 dark:hover:bg-yellow-900/40 text-xs font-bold text-yellow-600 dark:text-yellow-400 transition-colors">
                                                    <i class="fa-regular fa-copy"></i> <span>Salin</span>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Bill Key -->
                                        <div class="flex flex-col gap-1.5 p-3.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/60">
                                            <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Bill Key (Kode Bayar)</span>
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-base font-black font-mono tracking-wider text-slate-900 dark:text-white" id="bill-key">{{ $order->payment->va_number }}</span>
                                                <button onclick="copyToClipboard('bill-key', this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-950/40 dark:hover:bg-yellow-900/40 text-xs font-bold text-yellow-600 dark:text-yellow-400 transition-colors">
                                                    <i class="fa-regular fa-copy"></i> <span>Salin</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @elseif($order->payment->payment_method === 'qris')
                                <!-- QRIS Layout -->
                                <div class="flex flex-col items-center gap-4 text-center">
                                    <span class="text-xs font-black tracking-widest text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-950/40 px-2.5 py-1 rounded-lg uppercase">
                                        QRIS / GPN
                                    </span>
                                    
                                    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-md">
                                        <img src="{{ $order->payment->qris_url }}" alt="QRIS QR Code" class="w-48 h-48 mx-auto object-contain">
                                    </div>
                                    
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                                        Scan QR Code di atas menggunakan aplikasi e-wallet Anda (GoPay, OVO, Dana, LinkAja, ShopeePay, dll).
                                    </p>
                                </div>
                            @endif

                            <!-- Accordion Panduan Cara Pembayaran -->
                            <div class="mt-5 border-t border-slate-200/60 dark:border-slate-800/60 pt-4" x-data="{ openStep: null }">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-300 mb-2">Panduan Pembayaran:</h4>
                                
                                @if(in_array($order->payment->payment_method, ['bca', 'bni', 'bri']))
                                    <!-- VA Guides -->
                                    <div class="flex flex-col gap-2">
                                        <!-- Step 1: ATM -->
                                        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden text-xs">
                                            <button @click="openStep = (openStep === 1 ? null : 1)" class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-900 font-bold text-slate-700 dark:text-slate-300 text-left">
                                                <span>Pembayaran via ATM</span>
                                                <i class="fa-solid" :class="openStep === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                            </button>
                                            <div x-show="openStep === 1" class="p-3 bg-slate-50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-400 space-y-1.5 leading-relaxed font-light">
                                                <p>1. Masukkan kartu ATM dan PIN Anda.</p>
                                                <p>2. Pilih menu <strong>Transaksi Lainnya</strong> > <strong>Transfer</strong> > <strong>Ke Rekening Virtual Account</strong>.</p>
                                                <p>3. Masukkan nomor VA: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $order->payment->va_number }}</span>.</p>
                                                <p>4. Validasi data tagihan, pastikan jumlah bayar Rp {{ number_format($order->total, 0, ',', '.') }}.</p>
                                                <p>5. Selesaikan transaksi dan simpan bukti bayar.</p>
                                            </div>
                                        </div>

                                        <!-- Step 2: Mobile Banking -->
                                        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden text-xs">
                                            <button @click="openStep = (openStep === 2 ? null : 2)" class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-900 font-bold text-slate-700 dark:text-slate-300 text-left">
                                                <span>Pembayaran via Mobile Banking</span>
                                                <i class="fa-solid" :class="openStep === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                            </button>
                                            <div x-show="openStep === 2" class="p-3 bg-slate-50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-400 space-y-1.5 leading-relaxed font-light">
                                                <p>1. Buka aplikasi Mobile Banking di ponsel Anda.</p>
                                                <p>2. Pilih menu <strong>Transfer</strong> > <strong>Virtual Account / Billing</strong>.</p>
                                                <p>3. Masukkan nomor VA: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $order->payment->va_number }}</span>.</p>
                                                <p>4. Konfirmasi nama merchant <strong>DevGate</strong> dan nominal total Rp {{ number_format($order->total, 0, ',', '.') }}.</p>
                                                <p>5. Masukkan PIN Mobile Banking Anda untuk menyelesaikan transfer.</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($order->payment->payment_method === 'mandiri')
                                    <!-- Mandiri Guide -->
                                    <div class="flex flex-col gap-2">
                                        <!-- ATM -->
                                        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden text-xs">
                                            <button @click="openStep = (openStep === 1 ? null : 1)" class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-900 font-bold text-slate-700 dark:text-slate-300 text-left">
                                                <span>Pembayaran via ATM Mandiri</span>
                                                <i class="fa-solid" :class="openStep === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                            </button>
                                            <div x-show="openStep === 1" class="p-3 bg-slate-50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-400 space-y-1.5 leading-relaxed font-light">
                                                <p>1. Masukkan kartu ATM Mandiri dan PIN Anda.</p>
                                                <p>2. Pilih menu <strong>Bayar/Beli</strong> > <strong>Lainnya</strong> > <strong>Multi Payment</strong>.</p>
                                                <p>3. Masukkan Kode Biller Mandiri: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $order->payment->payload['biller_code'] ?? '70012' }}</span>.</p>
                                                <p>4. Masukkan Bill Key / Kode Bayar: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $order->payment->va_number }}</span>.</p>
                                                <p>5. Pilih nomor tagihan yang muncul, konfirmasi nominal Rp {{ number_format($order->total, 0, ',', '.') }}, lalu tekan <strong>Ya</strong>.</p>
                                            </div>
                                        </div>

                                        <!-- Livin' by Mandiri -->
                                        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden text-xs">
                                            <button @click="openStep = (openStep === 2 ? null : 2)" class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-900 font-bold text-slate-700 dark:text-slate-300 text-left">
                                                <span>Pembayaran via Livin' by Mandiri</span>
                                                <i class="fa-solid" :class="openStep === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                            </button>
                                            <div x-show="openStep === 2" class="p-3 bg-slate-50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-400 space-y-1.5 leading-relaxed font-light">
                                                <p>1. Login ke aplikasi Livin' by Mandiri.</p>
                                                <p>2. Pilih menu <strong>Bayar</strong> > cari penyedia jasa <strong>Midtrans / Toko Online</strong> atau masukkan kode biller <span class="font-bold">{{ $order->payment->payload['biller_code'] ?? '70012' }}</span>.</p>
                                                <p>3. Masukkan Bill Key / Kode Bayar: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $order->payment->va_number }}</span>.</p>
                                                <p>4. Konfirmasi detail transaksi, nominal Rp {{ number_format($order->total, 0, ',', '.') }}, lalu masukkan PIN Livin' Anda.</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($order->payment->payment_method === 'qris')
                                    <!-- QRIS Guide -->
                                    <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden text-xs">
                                        <button @click="openStep = (openStep === 1 ? null : 1)" class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-900 font-bold text-slate-700 dark:text-slate-300 text-left">
                                            <span>Cara Scan QRIS</span>
                                            <i class="fa-solid" :class="openStep === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                        <div x-show="openStep === 1" class="p-3 bg-slate-50 dark:bg-slate-950/30 text-slate-500 dark:text-slate-400 space-y-1.5 leading-relaxed font-light">
                                            <p>1. Buka aplikasi e-wallet pilihan Anda (GoPay, OVO, Dana, LinkAja, ShopeePay, dll).</p>
                                            <p>2. Pilih opsi <strong>Scan / Bayar / Pay</strong>.</p>
                                            <p>3. Arahkan kamera ponsel Anda ke gambar QR Code di atas.</p>
                                            <p>4. Periksa nominal total yang tertera, pastikan jumlahnya Rp {{ number_format($order->total, 0, ',', '.') }}.</p>
                                            <p>5. Klik <strong>Bayar</strong> dan masukkan PIN e-wallet Anda untuk menyelesaikan pembayaran.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Tombol Saya Sudah Bayar -->
                            <div class="mt-5 pt-4 border-t border-slate-200/60 dark:border-slate-800/60" x-data="{ verifying: false }">
                                <form action="{{ route('orders.verify', $order->order_number) }}" method="POST" @submit="verifying = true">
                                    @csrf
                                    <button type="submit" 
                                            :disabled="verifying"
                                            class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-md hover:bg-indigo-500 hover:shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <template x-if="!verifying">
                                            <span class="flex items-center gap-2">
                                                Saya Sudah Bayar <i class="fa-solid fa-rotate text-xs"></i>
                                            </span>
                                        </template>
                                        <template x-if="verifying">
                                            <span class="flex items-center gap-2">
                                                Memeriksa Pembayaran... <i class="fa-solid fa-spinner animate-spin text-xs"></i>
                                            </span>
                                        </template>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(in_array($order->status, ['pending', 'awaiting_payment']))
                        <div class="mt-4">
                            <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50/20 hover:bg-rose-50/50 dark:hover:bg-rose-950/20 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 transition-colors">
                                    Batalkan Pesanan <i class="fa-solid fa-ban text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    @elseif(in_array($order->status, ['paid', 'processing']))
                        <div class="mt-4" x-data>
                            <button @click="$dispatch('open-refund-modal')" type="button" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50/20 hover:bg-rose-50/50 dark:hover:bg-rose-950/20 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 transition-colors">
                                Ajukan Pengembalian Dana (Refund) <i class="fa-solid fa-ban text-[10px]"></i>
                            </button>
                        </div>
                    @endif

                    @if($order->status === 'shipped')
                        <div class="mt-6">
                            <form action="{{ route('orders.complete', $order->order_number) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-md hover:bg-emerald-500 transition-colors" onclick="return confirm('Apakah Anda yakin telah menerima pesanan dengan baik?')">
                                    Pesanan Diterima <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>
            
        </div>

    </div>
</div>

@php
    $paymentMethod = $order->payment->payment_method ?? 'qris';
    $isManualRefund = in_array($paymentMethod, ['bca', 'mandiri', 'bni', 'bri']);
@endphp

<!-- Refund Form Modal -->
<div x-data="{ 
    open: false,
    reason: '',
    bank_name: '',
    account_number: '',
    account_holder: '',
    loading: false,
    errorMessage: '',
    successMessage: '',
    
    async submitRefund() {
        this.loading = true;
        this.errorMessage = '';
        this.successMessage = '';
        
        let bodyData = {
            reason: this.reason,
            _token: '{{ csrf_token() }}'
        };
        
        @if($isManualRefund)
            bodyData.bank_name = this.bank_name;
            bodyData.account_number = this.account_number;
            bodyData.account_holder = this.account_holder;
        @endif
        
        try {
            let response = await fetch('{{ route('orders.refund.store', $order->order_number) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(bodyData)
            });
            
            let data = await response.json();
            
            if (response.ok) {
                this.successMessage = data.message || 'Pengajuan refund berhasil dikirim!';
                
                // Dispatch event to open & reload chat messages
                window.dispatchEvent(new CustomEvent('refund-submitted'));
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (data.errors) {
                    this.errorMessage = Object.values(data.errors).flat().join('\n');
                } else {
                    this.errorMessage = data.message || 'Gagal mengajukan pengembalian dana.';
                }
            }
        } catch (err) {
            console.error('Refund submit error:', err);
            this.errorMessage = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
        } finally {
            this.loading = false;
        }
    }
}" 
     @open-refund-modal.window="open = true" 
     @close-refund-modal.window="open = false; errorMessage = ''; successMessage = '';"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
    
    <!-- Modal Content Container -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-3xl border border-slate-200/60 bg-white/90 p-6 text-left shadow-2xl transition-all dark:border-slate-800/80 dark:bg-slate-900/90 backdrop-blur-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-8"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <div class="absolute right-4 top-4">
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-350">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-950/50 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fa-solid fa-wallet text-rose-600 dark:text-rose-400"></i>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">
                        Pengajuan Pengembalian Dana
                    </h3>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Silakan isi formulir di bawah ini untuk mengajukan pembatalan dan pengembalian dana pesanan Anda.
                    </p>
                    
                    <form @submit.prevent="submitRefund()" class="mt-4 space-y-4">
                        
                        <!-- Alasan Refund -->
                        <div>
                            <label for="reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Alasan Pembatalan / Refund</label>
                            <textarea x-model="reason" name="reason" id="reason" rows="3" required placeholder="Jelaskan alasan mengapa Anda membatalkan pesanan..." class="w-full rounded-xl border border-slate-300/80 bg-white/50 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700/80 dark:bg-slate-950/30 dark:text-white dark:placeholder-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"></textarea>
                        </div>
                        
                        @if($isManualRefund)
                            <!-- Bank Information for Manual Transfer (Virtual Account) -->
                            <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100/50 dark:border-indigo-900/30 space-y-4">
                                <h4 class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-bank"></i> Detail Rekening Pengembalian
                                </h4>
                                
                                <!-- Bank Name -->
                                <div>
                                    <label for="bank_name" class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Nama Bank</label>
                                    <input x-model="bank_name" type="text" name="bank_name" id="bank_name" required placeholder="Contoh: BCA, Mandiri, BNI, BRI" class="w-full rounded-lg border border-slate-350/80 bg-white px-3 py-2 text-xs text-slate-800 shadow-inner dark:border-slate-700/80 dark:bg-slate-950/40 dark:text-white">
                                </div>
                                
                                <!-- Account Number -->
                                <div>
                                    <label for="account_number" class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Nomor Rekening</label>
                                    <input x-model="account_number" type="text" name="account_number" id="account_number" required placeholder="Masukkan nomor rekening bank tujuan" class="w-full rounded-lg border border-slate-350/80 bg-white px-3 py-2 text-xs text-slate-800 shadow-inner dark:border-slate-700/80 dark:bg-slate-950/40 dark:text-white">
                                </div>
                                
                                <!-- Account Holder -->
                                <div>
                                    <label for="account_holder" class="block text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Nama Pemilik Rekening</label>
                                    <input x-model="account_holder" type="text" name="account_holder" id="account_holder" required placeholder="Harus sesuai dengan nama di buku tabungan" class="w-full rounded-lg border border-slate-350/80 bg-white px-3 py-2 text-xs text-slate-800 shadow-inner dark:border-slate-700/80 dark:bg-slate-950/40 dark:text-white">
                                </div>
                            </div>
                        @else
                            <!-- QRIS Auto Refund Notice -->
                            <div class="p-4 rounded-2xl bg-pink-50/50 dark:bg-pink-950/20 border border-pink-100/50 dark:border-pink-900/30 text-xs text-pink-700 dark:text-pink-400 flex items-start gap-2.5 leading-relaxed font-semibold">
                                <i class="fa-solid fa-circle-info text-sm mt-0.5"></i>
                                <div>
                                    Pembayaran QRIS otomatis di-refund kembali ke Saldo E-Wallet / Rekening asal yang digunakan untuk scan via Midtrans Refund API setelah disetujui. Tidak memerlukan informasi rekening tambahan.
                                </div>
                            </div>
                        @endif

                        <!-- Alert Error -->
                        <div x-show="errorMessage" x-transition class="p-3.5 rounded-2xl bg-rose-50 border border-rose-150 text-xs text-rose-700 dark:bg-rose-950/20 dark:border-rose-900/30 dark:text-rose-450 flex items-start gap-2.5 font-semibold">
                            <i class="fa-solid fa-circle-exclamation text-sm mt-0.5 animate-bounce"></i>
                            <div x-text="errorMessage" class="whitespace-pre-line"></div>
                        </div>

                        <!-- Alert Success -->
                        <div x-show="successMessage" x-transition class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-150 text-xs text-emerald-700 dark:bg-emerald-950/20 dark:border-emerald-900/30 dark:text-emerald-450 flex items-start gap-2.5 font-semibold">
                            <i class="fa-solid fa-circle-check text-sm mt-0.5 animate-pulse"></i>
                            <div x-text="successMessage"></div>
                        </div>
                        
                        <div class="mt-6 flex flex-col sm:flex-row-reverse gap-3">
                            <button type="submit" :disabled="loading" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white shadow-md hover:bg-rose-500 hover:shadow-rose-500/20 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <template x-if="!loading">
                                    <span>Kirim Pengajuan Refund <i class="fa-solid fa-paper-plane text-xs"></i></span>
                                </template>
                                <template x-if="loading">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-notch animate-spin text-sm"></i> Mengirim...</span>
                                </template>
                            </button>
                            <button type="button" @click="open = false" :disabled="loading" class="w-full inline-flex justify-center items-center rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950 px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors disabled:opacity-50">
                                Kembali
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat & Bantuan Floating Widget -->
<div x-data="{ 
        openDrawer: false, 
        activeTab: 'chat', 
        messages: [], 
        messageInput: '', 
        loading: false,
        pollingInterval: null,
        
        initChat() {
            this.fetchMessages();
            // Start polling every 4 seconds
            this.pollingInterval = setInterval(() => {
                if (this.openDrawer && this.activeTab === 'chat') {
                    this.fetchMessages();
                }
            }, 4000);
        },
        
        async fetchMessages() {
            try {
                let response = await fetch('{{ route('orders.chat.index', $order->order_number) }}');
                let data = await response.json();
                this.messages = data.messages;
                this.scrollToBottom();
            } catch (err) {
                console.error('Failed to load chat messages:', err);
            }
        },
        
        async sendMessage() {
            if (!this.messageInput.trim()) return;
            let body = {
                message: this.messageInput,
                _token: '{{ csrf_token() }}'
            };
            
            try {
                this.messageInput = '';
                let response = await fetch('{{ route('orders.chat.send', $order->order_number) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                let data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.scrollToBottom();
                }
            } catch (err) {
                console.error('Failed to send message:', err);
            }
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                let container = this.$refs.chatContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
     }"
     @refund-submitted.window="fetchMessages(); openDrawer = true; activeTab = 'chat';"
     x-init="initChat()"
     class="fixed bottom-6 right-6 z-40">
     
     <!-- Floating Button -->
     <button @click="openDrawer = true" 
             class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white shadow-xl shadow-indigo-500/30 hover:bg-indigo-500 hover:scale-105 active:scale-95 transition-all">
         <i class="fa-solid fa-comments text-xl"></i>
     </button>
     
     <!-- Slide-over Drawer -->
     <div x-show="openDrawer" 
          class="fixed inset-0 z-50 overflow-hidden" 
          style="display: none;">
          
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="openDrawer = false"></div>
          
          <!-- Slider Container -->
          <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
              <div class="w-screen max-w-md transform transition-all"
                   x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full">
                   
                   <div class="flex h-full flex-col bg-white/95 shadow-2xl dark:bg-slate-900/95 border-l border-slate-200/60 dark:border-slate-800/80 backdrop-blur-xl">
                       
                       <!-- Drawer Header -->
                       <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
                           <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                               <i class="fa-solid fa-headset text-indigo-500"></i> Bantuan & Chat Admin
                           </h2>
                           <button type="button" @click="openDrawer = false" class="text-slate-400 hover:text-slate-500">
                               <i class="fa-solid fa-xmark text-lg"></i>
                           </button>
                       </div>
                       
                       <!-- Tab Switchers -->
                       <div class="flex border-b border-slate-100 dark:border-slate-800">
                           <button @click="activeTab = 'chat'; scrollToBottom();" 
                                   :class="activeTab === 'chat' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-355'" 
                                   class="flex-1 text-center py-3 border-b-2 text-xs font-bold uppercase tracking-wider transition-colors">
                               Chat Room
                           </button>
                           <button @click="activeTab = 'help'" 
                                   :class="activeTab === 'help' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-355'" 
                                   class="flex-1 text-center py-3 border-b-2 text-xs font-bold uppercase tracking-wider transition-colors">
                               Panduan Bantuan
                           </button>
                       </div>
                       
                       <!-- Drawer Content -->
                       <div class="flex-grow overflow-y-auto p-6 min-h-0 flex flex-col justify-between">
                           
                           <!-- Tab 1: Chat Room -->
                           <div x-show="activeTab === 'chat'" class="flex-grow flex flex-col justify-between h-full min-h-0">
                               <!-- Chat Bubble Container -->
                               <div x-ref="chatContainer" class="flex-grow overflow-y-auto space-y-4 mb-4 pr-1 scroll-smooth" style="max-height: calc(100vh - 250px);">
                                   
                                   <template x-if="messages.length === 0">
                                       <div class="text-center py-8 text-slate-400 dark:text-slate-500 text-xs">
                                           <i class="fa-regular fa-comments text-3xl mb-3 block text-slate-300 dark:text-slate-700"></i>
                                           Belum ada percakapan. Mulai kirim pesan untuk bertanya pada Admin marketplace kami.
                                       </div>
                                   </template>
                                   
                                   <template x-for="msg in messages" :key="msg.id">
                                       <div :class="msg.sender_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'" class="flex">
                                           <div :class="msg.sender_id === {{ Auth::id() }} ? 'bg-indigo-600 text-white rounded-tr-none shadow-indigo-600/10' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-150 rounded-tl-none'"
                                                class="max-w-[85%] rounded-2xl px-4 py-2.5 shadow-sm text-sm leading-relaxed">
                                               
                                               <!-- Sender Name -->
                                               <div class="text-[10px] font-bold opacity-75 mb-1" x-text="msg.sender.name"></div>
                                               
                                               <!-- Message Text -->
                                               <div class="whitespace-pre-line" x-text="msg.message"></div>
                                               
                                               <!-- Timestamp -->
                                               <div class="text-[9px] opacity-60 text-right mt-1" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></div>
                                           </div>
                                       </div>
                                   </template>
                               </div>
                               
                               <!-- Send Input Box -->
                               <div class="border-t border-slate-100 dark:border-slate-800 pt-4 bg-white dark:bg-slate-900">
                                   <form @submit.prevent="sendMessage()" class="flex gap-2">
                                       <input type="text" 
                                              x-model="messageInput" 
                                              placeholder="Tulis pesan..." 
                                              class="flex-grow rounded-xl border border-slate-300/80 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-inner focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700/80 dark:bg-slate-950/40 dark:text-white">
                                       <button type="submit" 
                                               class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 transition-colors shadow-md">
                                           <i class="fa-solid fa-paper-plane text-xs"></i>
                                       </button>
                                   </form>
                               </div>
                           </div>
                           
                           <!-- Tab 2: Help Guide -->
                           <div x-show="activeTab === 'help'" class="space-y-6">
                               
                               <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100/50 dark:border-indigo-900/30 text-xs text-indigo-700 dark:text-indigo-400 font-semibold leading-relaxed">
                                   <i class="fa-solid fa-circle-question text-sm mb-1.5 block"></i>
                                   Kami siap membantu Anda memproses refund dengan aman, transparan, dan tidak merugikan pihak manapun.
                               </div>

                               <div class="space-y-4">
                                   <!-- FAQ Item 1 -->
                                   <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                                       <h4 class="font-extrabold text-xs text-slate-800 dark:text-slate-200">Bagaimana kebijakan pengembalian dana (refund)?</h4>
                                       <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                           Pesanan yang telah dibayar (status 'Paid' atau 'Processing') hanya dapat dibatalkan jika belum dikirim oleh penjual. Anda harus mengajukan permohonan refund dengan menyertakan alasan pembatalan yang valid.
                                       </p>
                                   </div>
                                   
                                   <!-- FAQ Item 2 -->
                                   <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                                       <h4 class="font-extrabold text-xs text-slate-800 dark:text-slate-200">Berapa lama estimasi dana refund masuk?</h4>
                                       <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                           Untuk pembayaran QRIS, dana akan otomatis dikembalikan ke e-wallet/rekening asal via Midtrans API dalam 1-3 hari kerja. Untuk Virtual Account, pengembalian diproses manual oleh Admin kami melalui transfer rekening (estimasi 1-2 hari kerja) setelah disetujui.
                                       </p>
                                   </div>
                                   
                                   <!-- FAQ Item 3 -->
                                   <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                                       <h4 class="font-extrabold text-xs text-slate-800 dark:text-slate-200">Apakah stok barang otomatis kembali?</h4>
                                       <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                           Ya. Setelah permohonan refund Anda disetujui secara resmi oleh Admin marketplace, sistem kami akan secara otomatis memulihkan stok produk ke katalog toko agar tidak mengganggu operasional inventaris.
                                       </p>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
              </div>
          </div>
     </div>
</div>

@endsection

@section('scripts')
<script>
    function copyToClipboard(elementId, button) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fa-solid fa-check text-green-500 animate-bounce"></i> <span>Tersalin!</span>';
            button.classList.add('bg-green-50', 'text-green-700', 'dark:bg-green-950/40', 'dark:text-green-400');
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-50', 'text-green-700', 'dark:bg-green-950/40', 'dark:text-green-400');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>
@endsection
