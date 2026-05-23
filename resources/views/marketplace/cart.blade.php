@extends('layouts.app')

@section('title', 'Keranjang Belanja Saya — DevGate')

@section('content')
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl mb-8">
            Keranjang Belanja Anda
        </h1>

        @if($cartItems->count() > 0)
            <!-- Cart Grid split -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: List of Items (Col span 8) -->
                <div class="lg:col-span-8 flex flex-col gap-4">
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                        <div class="flow-root">
                            <ul role="list" class="-my-6 divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($cartItems as $item)
                                    <li class="flex py-6 gap-4">
                                        <!-- Product image -->
                                        <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex items-center justify-center">
                                            @if($item->product->getFirstMediaUrl('product-images'))
                                                <img src="{{ $item->product->getFirstMediaUrl('product-images') }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="text-slate-400 dark:text-slate-600"><i class="fa-solid fa-microchip text-xl"></i></div>
                                            @endif
                                        </div>

                                        <!-- Item Details -->
                                        <div class="flex flex-1 flex-col justify-between">
                                            <div>
                                                <div class="flex justify-between text-base font-bold text-slate-900 dark:text-white">
                                                    <h3 class="hover:text-indigo-600 transition-colors truncate max-w-md">
                                                        <a href="{{ route('shop.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                                        @if($item->variant)
                                                            <span class="ml-2 rounded bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 text-[10px] font-bold text-indigo-600 dark:text-indigo-400">
                                                                Varian: {{ $item->variant->name }}
                                                            </span>
                                                        @endif
                                                    </h3>
                                                    <p class="ml-4 text-indigo-600 dark:text-indigo-400">
                                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500 mt-1">
                                                    <span>Harga: Rp {{ number_format($item->variant ? $item->variant->price : $item->product->effective_price, 0, ',', '.') }}</span>
                                                    <span>&bull;</span>
                                                    <span>Merek: {{ $item->product->brand ?? '-' }}</span>
                                                    <span>&bull;</span>
                                                    <span>Berat: {{ ($item->variant ? $item->variant->effective_weight : $item->product->weight) * $item->quantity }}g</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Interactive Quantity & Delete -->
                                            <div class="flex flex-wrap items-center justify-between gap-4 mt-4">
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center rounded-lg border border-slate-200 bg-white/50 px-1 dark:border-slate-800 dark:bg-slate-950/40">
                                                    @csrf
                                                    @method('PATCH')
                                                    <!-- Minus -->
                                                    <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200"><i class="fa-solid fa-minus text-[10px]"></i></button>
                                                    <span class="w-8 text-center text-xs font-bold text-slate-700 dark:text-slate-300">{{ $item->quantity }}</span>
                                                    <!-- Plus -->
                                                    @php
                                                        $stockLimit = $item->variant ? $item->variant->stock : $item->product->stock;
                                                    @endphp
                                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200" {{ $item->product->track_stock && $stockLimit <= $item->quantity ? 'disabled' : '' }}><i class="fa-solid fa-plus text-[10px]"></i></button>
                                                </form>

                                                <!-- Delete button -->
                                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-500 hover:text-rose-600 transition-colors">
                                                        <i class="fa-regular fa-trash-can"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary buy box (Col span 4) -->
                <div class="lg:col-span-4 sticky top-24">
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm flex flex-col gap-5">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                            Ringkasan Belanja
                        </h2>
                        
                        <div class="flex flex-col gap-3 text-sm">
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Jumlah Item</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $summary['total_quantity'] }} Pcs</span>
                            </div>
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Total Berat</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($summary['total_weight'] / 1000, 2) }} Kg</span>
                            </div>
                            
                            <div class="h-px bg-slate-100 dark:bg-slate-800 my-1"></div>
                            
                            <div class="flex justify-between text-base font-bold text-slate-900 dark:text-white">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="mt-2 flex flex-col gap-3">
                            <a href="{{ route('checkout.index') }}" class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-indigo-500 transition-all shadow-indigo-600/10">
                                Lanjutkan ke Checkout <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                            </a>
                            <a href="{{ route('shop.index') }}" class="w-full inline-flex items-center justify-center rounded-xl border border-slate-350 dark:border-slate-700 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                Kembali Belanja
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <!-- Empty cart state -->
            <div class="flex flex-col items-center justify-center py-20 text-center rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/30 dark:bg-slate-900/10">
                <i class="fa-solid fa-cart-shopping text-slate-300 dark:text-slate-700 text-6xl mb-4"></i>
                <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Keranjang Anda Kosong</h3>
                <p class="text-sm text-slate-400 mt-1 max-w-sm">Jelajahi toko kami untuk menemukan komponen IoT premium dan modul sensor menarik untuk proyek teknologi Anda.</p>
                <a href="{{ route('shop.index') }}" class="mt-6 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 transition-colors">
                    Belanja Sekarang
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
