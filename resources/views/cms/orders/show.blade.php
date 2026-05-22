@extends('layouts.cms')

@section('title', 'Detail Invoice #' . $order->order_number . ' — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Marketplace</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.orders.index') }}" class="hover:text-white">Pesanan</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Invoice #{{ $order->order_number }}</span>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    @if($order->refundRequest && $order->status === 'refunding')
        <div class="rounded-3xl border border-amber-500/30 bg-amber-500/5 p-6 backdrop-blur-sm shadow-xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white">Review Pengajuan Pengembalian Dana</h3>
                    <p class="text-slate-400 text-xs">Customer mengajukan pembatalan pesanan dan refund dana.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-950/40 p-4 rounded-2xl border border-slate-800/80 text-xs">
                <div>
                    <span class="text-slate-500 font-bold uppercase tracking-wider block mb-1">Alasan Refund:</span>
                    <span class="text-slate-200 font-medium bg-slate-900/50 p-2 rounded-lg block italic">"{{ $order->refundRequest->reason }}"</span>
                </div>
                <div>
                    <span class="text-slate-500 font-bold uppercase tracking-wider block mb-1">Metode Pengembalian:</span>
                    @if($order->refundRequest->refund_method === 'midtrans_api')
                        <span class="text-pink-400 font-bold bg-pink-500/10 border border-pink-500/20 px-2.5 py-1 rounded-lg inline-block mt-1">Otomatis via Midtrans API (QRIS)</span>
                    @else
                        <span class="text-indigo-400 font-bold bg-indigo-500/10 border border-indigo-500/20 px-2.5 py-1 rounded-lg inline-block mt-1">Manual Transfer Bank (Virtual Account)</span>
                    @endif
                </div>
                
                @if($order->refundRequest->refund_method === 'manual_bank_transfer')
                    <div class="md:col-span-2 border-t border-slate-800/60 pt-3 mt-1 space-y-2">
                         <span class="text-slate-500 font-bold uppercase tracking-wider block">Rekening Bank Tujuan Customer:</span>
                         <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-900/30 p-3 rounded-xl">
                             <div><span class="text-slate-500">Bank:</span> <strong class="text-slate-350">{{ strtoupper($order->refundRequest->bank_name) }}</strong></div>
                             <div><span class="text-slate-500">No Rekening:</span> <strong class="text-slate-350 font-mono tracking-wider">{{ $order->refundRequest->account_number }}</strong></div>
                             <div><span class="text-slate-500">Atas Nama:</span> <strong class="text-slate-350">{{ $order->refundRequest->account_holder }}</strong></div>
                         </div>
                    </div>
                @endif
            </div>
            
            <!-- Action Forms -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2" x-data="{ action: null }">
                 <!-- Setujui Form Trigger -->
                 <div class="flex-grow">
                     @if($order->refundRequest->refund_method === 'midtrans_api')
                         <!-- QRIS Auto Approve Form -->
                         <form action="{{ route('cms.orders.refund.approve', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses refund otomatis ini via Midtrans API?')">
                             @csrf
                             <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-lg transition-colors">
                                 <i class="fa-solid fa-circle-check"></i> Setujui & Refund Otomatis (Midtrans API)
                             </button>
                         </form>
                     @else
                         <!-- VA Manual Approve Form with Receipt ID input -->
                         <div x-show="action !== 'approve'" class="w-full">
                             <button type="button" @click="action = 'approve'" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-lg transition-colors">
                                 <i class="fa-solid fa-circle-check"></i> Setujui Refund (Input No Referensi Transfer)
                             </button>
                         </div>
                         <div x-show="action === 'approve'" x-transition class="w-full p-4 rounded-2xl bg-emerald-950/20 border border-emerald-500/20 space-y-3" x-cloak>
                             <h4 class="text-xs font-bold text-emerald-400">Konfirmasi Transfer Manual</h4>
                             <form action="{{ route('cms.orders.refund.approve', $order->id) }}" method="POST" class="space-y-3">
                                 @csrf
                                 <div>
                                     <label for="refund_reference" class="block text-[10px] text-slate-455 font-bold uppercase mb-1">Nomor Referensi Bank / ID Transaksi:</label>
                                     <input type="text" name="refund_reference" id="refund_reference" required placeholder="Contoh: TRX-18237918 atau BTR-982739" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white placeholder-slate-650">
                                 </div>
                                 <div class="flex gap-2">
                                     <button type="submit" class="flex-grow inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white">
                                         Kirim Bukti & Selesaikan
                                     </button>
                                     <button type="button" @click="action = null" class="px-3 py-2 rounded-lg bg-slate-850 hover:bg-slate-800 text-xs text-slate-450">Batal</button>
                                 </div>
                             </form>
                         </div>
                     @endif
                 </div>
                 
                 <!-- Tolak Form Trigger -->
                 <div class="flex-grow">
                     <div x-show="action !== 'reject'" class="w-full">
                         <button type="button" @click="action = 'reject'" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white shadow-lg transition-colors">
                             <i class="fa-solid fa-circle-xmark"></i> Tolak Pengajuan Refund
                         </button>
                     </div>
                     <div x-show="action === 'reject'" x-transition class="w-full p-4 rounded-2xl bg-rose-950/20 border border-rose-500/20 space-y-3" x-cloak>
                         <h4 class="text-xs font-bold text-rose-400">Tolak Pengajuan Refund</h4>
                         <form action="{{ route('cms.orders.refund.reject', $order->id) }}" method="POST" class="space-y-3">
                             @csrf
                             <div>
                                 <label for="admin_notes" class="block text-[10px] text-slate-455 font-bold uppercase mb-1">Alasan Penolakan:</label>
                                 <textarea name="admin_notes" id="admin_notes" required rows="2" placeholder="Tulis alasan mengapa pengembalian dana ini ditolak..." class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-white placeholder-slate-650"></textarea>
                             </div>
                             <div class="flex gap-2">
                                 <button type="submit" class="flex-grow inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white">
                                     Tolak Refund Sekarang
                                 </button>
                                 <button type="button" @click="action = null" class="px-3 py-2 rounded-lg bg-slate-850 hover:bg-slate-800 text-xs text-slate-455">Batal</button>
                             </div>
                         </form>
                     </div>
                 </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-0.5 rounded bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-bold tracking-widest uppercase">Invoice</span>
                <h1 class="text-xl font-extrabold text-white tracking-tight">Pesanan #{{ $order->order_number }}</h1>
            </div>
            <p class="text-slate-450 text-xs mt-1">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB &bull; Pembeli: <strong class="text-slate-300">{{ $order->user ? $order->user->name : 'N/A' }}</strong></p>
        </div>
        <a 
            href="{{ route('cms.orders.index') }}" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-850 hover:text-white text-xs font-bold text-slate-300 transition-all"
        >
            <i class="fa-solid fa-arrow-left-long"></i> Kembali
        </a>
    </div>

    <!-- Order Visual Step Timeline -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 backdrop-blur-sm">
        <div class="relative flex flex-col md:flex-row justify-between items-center gap-6 md:gap-4">
            
            @php
                $steps = [
                    ['key' => 'awaiting_payment', 'label' => 'Menunggu Pembayaran', 'icon' => 'fa-regular fa-clock', 'date' => $order->created_at],
                    ['key' => 'paid', 'label' => 'Telah Dibayar', 'icon' => 'fa-solid fa-receipt', 'date' => $order->paid_at],
                    ['key' => 'processing', 'label' => 'Sedang Diproses', 'icon' => 'fa-solid fa-box-open', 'date' => $order->paid_at ? $order->updated_at : null],
                    ['key' => 'shipped', 'label' => 'Dalam Pengiriman', 'icon' => 'fa-solid fa-truck-fast', 'date' => $order->shipped_at],
                    ['key' => 'completed', 'label' => 'Selesai / Tiba', 'icon' => 'fa-solid fa-circle-check', 'date' => $order->delivered_at ?: ($order->status === 'completed' ? $order->updated_at : null)],
                ];

                // Determine active index
                $statusIndex = match ($order->status) {
                    'awaiting_payment', 'pending' => 0,
                    'paid' => 1,
                    'processing' => 2,
                    'shipped' => 3,
                    'completed', 'delivered' => 4,
                    default => -1,
                };
            @endphp

            @foreach($steps as $index => $step)
                <div class="flex-grow flex flex-row md:flex-col items-center gap-3 md:text-center w-full md:w-auto relative">
                    <!-- Step bubble -->
                    <div 
                        class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center border transition-all duration-500 font-bold {{ $index <= $statusIndex ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-600/25' : 'bg-slate-900 border-slate-800 text-slate-500' }}"
                    >
                        <i class="{{ $step['icon'] }} text-sm"></i>
                    </div>

                    <!-- Step label & date -->
                    <div class="text-left md:text-center">
                        <p class="text-xs font-bold transition-colors {{ $index <= $statusIndex ? 'text-white' : 'text-slate-500' }}">{{ $step['label'] }}</p>
                        @if($step['date'])
                            <p class="text-[9px] text-slate-550 font-mono mt-0.5">{{ $step['date']->format('d M Y, H:i') }}</p>
                        @else
                            <p class="text-[9px] text-slate-650 mt-0.5 italic">Belum tercapai</p>
                        @endif
                    </div>
                </div>

                @if($index < count($steps) - 1)
                    <!-- Connector line (Desktop only) -->
                    <div class="hidden md:block flex-grow h-[2px] bg-slate-800 relative -top-3.5">
                        <div class="absolute inset-y-0 left-0 bg-indigo-600 transition-all duration-700" style="width: {{ $index < $statusIndex ? '100%' : '0%' }}"></div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column Left: Order Items & Delivery Breakdown (2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- List Items Table -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
                <div class="px-6 py-4 border-b border-slate-800/60 bg-slate-950/60">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-list text-indigo-500"></i> Detail Item Belanja
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-[9px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60 bg-slate-950/40">
                                <th class="py-3 px-6">Produk / Komponen</th>
                                <th class="py-3 px-6 text-center">Jumlah</th>
                                <th class="py-3 px-6 text-right">Harga Satuan</th>
                                <th class="py-3 px-6 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @foreach($order->items as $item)
                                <tr class="group hover:bg-slate-900/10 transition-all">
                                    <!-- Image, Brand & Name -->
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 shrink-0 rounded-lg overflow-hidden bg-slate-900 border border-slate-800 flex items-center justify-center">
                                                @if($item->product && $item->product->hasMedia('product-images'))
                                                    <img src="{{ $item->product->getFirstMediaUrl('product-images', 'thumbnail') }}" alt="{{ $item->product->name }}" class="object-cover w-full h-full">
                                                @else
                                                    <i class="fa-solid fa-microchip text-slate-600 text-base"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-wider">{{ $item->product ? $item->product->brand : 'Brand' }}</p>
                                                <p class="text-xs font-bold text-white mt-0.5">{{ $item->product ? $item->product->name : 'Komponen Terhapus' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Qty -->
                                    <td class="py-3.5 px-6 text-center text-slate-300 font-mono font-bold">
                                        {{ $item->quantity }} pcs
                                    </td>

                                    <!-- Price Unit -->
                                    <td class="py-3.5 px-6 text-right text-slate-300 font-mono">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>

                                    <!-- Subtotal Item -->
                                    <td class="py-3.5 px-6 text-right text-indigo-300 font-mono font-bold">
                                        Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Grand Total Cost Summary Box -->
                <div class="bg-slate-950/70 border-t border-slate-800/80 px-6 py-4 space-y-2">
                    <div class="flex justify-between items-center text-xs text-slate-400">
                        <span>Total Belanja Produk (Subtotal):</span>
                        <span class="font-mono text-white">{{ $order->formatted_subtotal }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-400">
                        <span>Ongkos Kirim RajaOngkir:</span>
                        <span class="font-mono text-white">{{ $order->formatted_shipping_cost }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between items-center text-xs text-rose-400">
                            <span>Potongan Diskon (Voucher/Promo):</span>
                            <span class="font-mono">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-slate-800/60 my-2 pt-2 flex justify-between items-center">
                        <span class="text-xs font-bold text-white">Total Akhir (Grand Total):</span>
                        <span class="text-sm font-extrabold text-emerald-400 font-mono">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($order->notes)
                <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-2 backdrop-blur-sm">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-regular fa-comment-dots text-indigo-500"></i> Catatan Pembelian
                    </h4>
                    <p class="text-xs text-slate-350 bg-slate-900/40 border border-slate-850 rounded-2xl p-3.5 leading-relaxed italic">
                        "{{ $order->notes }}"
                    </p>
                </div>
            @endif

            <!-- Admin-Customer Chatroom Card -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-4 backdrop-blur-sm"
                 x-data="{ 
                    messages: [], 
                    messageInput: '', 
                    pollingInterval: null,
                    
                    initChat() {
                        this.fetchMessages();
                        // Poll every 4 seconds
                        this.pollingInterval = setInterval(() => {
                            this.fetchMessages();
                        }, 4000);
                    },
                    
                    async fetchMessages() {
                        try {
                            let response = await fetch('{{ route('cms.orders.chat.index', $order->id) }}');
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
                            let response = await fetch('{{ route('cms.orders.chat.send', $order->id) }}', {
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
                 x-init="initChat()">
                 
                 <h3 class="text-xs font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                     <i class="fa-solid fa-headset text-indigo-500 animate-pulse"></i> Chatroom Komunikasi Pembeli
                 </h3>
                 
                 <!-- Message List -->
                 <div x-ref="chatContainer" class="space-y-4 max-h-72 overflow-y-auto pr-1 scroll-smooth font-semibold">
                     <template x-if="messages.length === 0">
                         <div class="text-center py-6 text-slate-550 text-xs">
                             <i class="fa-regular fa-comments text-2xl mb-2 block text-slate-700"></i>
                             Belum ada percakapan dengan pembeli. Kirim pesan pertama untuk menyapa pembeli.
                         </div>
                     </template>
                     
                     <template x-for="msg in messages" :key="msg.id">
                         <div :class="msg.sender_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'" class="flex">
                             <div :class="msg.sender_id === {{ Auth::id() }} ? 'bg-indigo-600 text-white rounded-tr-none shadow-indigo-600/10' : 'bg-slate-900 text-slate-200 border border-slate-800/80 rounded-tl-none'"
                                  class="max-w-[85%] rounded-2xl px-4 py-2.5 shadow-sm text-xs leading-relaxed">
                                 
                                 <!-- Sender Name -->
                                 <div class="text-[9px] font-bold opacity-75 mb-1" x-text="msg.sender.name"></div>
                                 
                                 <!-- Message -->
                                 <div class="whitespace-pre-line text-xs" x-text="msg.message"></div>
                                 
                                 <!-- Time -->
                                 <div class="text-[8px] opacity-65 text-right mt-1" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></div>
                             </div>
                         </div>
                     </template>
                 </div>
                 
                 <!-- Input Form -->
                 <div class="border-t border-slate-800/60 pt-3">
                     <form @submit.prevent="sendMessage()" class="flex gap-2">
                         <input type="text" 
                                x-model="messageInput" 
                                placeholder="Tulis pesan ke pembeli..." 
                                class="flex-grow rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-xs text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none transition-all">
                         <button type="submit" 
                                 class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 transition-colors shadow-md">
                             <i class="fa-solid fa-paper-plane text-xs"></i>
                         </button>
                     </form>
                 </div>
            </div>

        </div>

        <!-- Column Right: Alamat & Update Status (1 Column) -->
        <div class="space-y-6">
            
            <!-- Update Order Status Panel -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-4 backdrop-blur-sm" x-data="{ status: '{{ $order->status }}' }">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-indigo-500"></i> Ubah Status Pesanan
                </h3>

                <form action="{{ route('cms.orders.status', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Status selector input -->
                    <div class="space-y-1.5">
                        <label for="status" class="text-[10px] font-bold text-slate-400 uppercase">Pilih Status Baru:</label>
                        <select 
                            id="status" 
                            name="status" 
                            x-model="status"
                            required
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-3 text-xs text-white focus:border-indigo-500 focus:outline-none transition-all"
                        >
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                            <option value="awaiting_payment" {{ $order->status === 'awaiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Telah Dibayar (Paid)</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Sedang Diproses (Processing)</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Dalam Pengiriman (Shipped)</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Sampai Tujuan (Delivered)</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Batalkan Pesanan (Cancelled)</option>
                            <option value="refunding" {{ $order->status === 'refunding' ? 'selected' : '' }}>Pengajuan Refund (Refunding)</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Dikembalikan (Refunded)</option>
                        </select>
                    </div>

                    <!-- Tracking Number Input (Slides in when status is Shipped) -->
                    <div class="space-y-1.5 pt-1" x-show="status === 'shipped'" x-transition x-cloak>
                        <label for="tracking_number" class="text-[10px] font-bold text-slate-400 uppercase">Nomor Resi Kurir (AWB) <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            id="tracking_number" 
                            name="tracking_number" 
                            value="{{ old('tracking_number', $order->tracking_number) }}" 
                            placeholder="Contoh: JP3298918239 atau JNE1234908" 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-3 text-xs text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none transition-all font-mono"
                            :required="status === 'shipped'"
                        >
                        <span class="text-[9px] text-slate-500 leading-normal block">Nomor resi kurir pengiriman wajib diisi apabila pesanan dialihkan menjadi Dalam Pengiriman.</span>
                    </div>

                    <!-- Update Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all"
                    >
                        <i class="fa-regular fa-floppy-disk"></i> Perbarui Status
                    </button>
                </form>
            </div>

            <!-- Customer Identity Box -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-3 backdrop-blur-sm">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-regular fa-user text-indigo-500"></i> Akun Pelanggan
                </h3>
                @if($order->user)
                    <div class="space-y-1 text-slate-350 text-xs leading-relaxed">
                        <p class="font-bold text-white text-sm">{{ $order->user->name }}</p>
                        <p class="font-mono">@&#64;{{ $order->user->username }}</p>
                        <p><i class="fa-regular fa-envelope text-[10px] text-slate-500 mr-1.5"></i> {{ $order->user->email }}</p>
                    </div>
                @else
                    <p class="text-xs text-slate-500 italic">Akun Pelanggan tidak terdaftar.</p>
                @endif
            </div>

            <!-- Shipping Address Details Card -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-4 backdrop-blur-sm">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-indigo-500"></i> Alamat Pengiriman
                </h3>

                @if(is_array($order->shipping_address))
                    <div class="space-y-1 text-slate-300 text-xs leading-relaxed">
                        <p class="font-bold text-white text-sm">{{ $order->shipping_address['name'] ?? '' }}</p>
                        <p><i class="fa-solid fa-phone text-[10px] text-slate-500 mr-1.5"></i> {{ $order->shipping_address['phone'] ?? '' }}</p>
                        <p class="mt-2 text-slate-400 bg-slate-900/40 border border-slate-850 p-2.5 rounded-xl font-mono text-[11px]">
                            {{ $order->shipping_address['address_line'] ?? '' }}
                        </p>
                        <div class="pt-1.5 space-y-0.5">
                            <p><span class="text-slate-500">Kota/Provinsi:</span> {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['province'] ?? '' }}</p>
                            <p><span class="text-slate-500">Kode Pos:</span> {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500 italic">Data alamat tidak valid.</p>
                @endif

                <div class="border-t border-slate-800/40 my-3"></div>

                <!-- Courier Info -->
                <div class="space-y-1 text-xs">
                    <p class="font-bold text-white">Ekspedisi Logistik:</p>
                    <p class="text-slate-300 uppercase font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-truck text-slate-550 text-[10px]"></i> 
                        {{ $order->courier }} ({{ $order->courier_service }})
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
