@extends('layouts.app')

@section('title', $product->name . ' — DevGate Marketplace')

@section('meta')
    <meta name="description" content="{{ $product->meta_description ?? $product->excerpt }}">
    <meta property="og:title" content="{{ $product->meta_title ?? $product->name }}">
    <meta property="og:description" content="{{ $product->meta_description ?? $product->excerpt }}">
    <meta property="og:image" content="{{ $product->getFirstMediaUrl('product-images') ?: asset('images/product-placeholder.webp') }}">
@endsection

@section('content')
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs Navigation -->
        <nav class="flex text-xs font-medium text-slate-400 dark:text-slate-500 mb-8 gap-2 items-center">
            <a href="/" class="hover:text-indigo-500 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="{{ route('shop.index') }}" class="hover:text-indigo-500 transition-colors">Shop</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-indigo-500 transition-colors">{{ $product->category->name }}</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-slate-700 dark:text-slate-300 line-clamp-1">{{ $product->name }}</span>
        </nav>

        <!-- Product Workspace Split layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-12">
            
            <!-- Left Side: Product Images & Gallery (Col span 5) -->
            <div class="lg:col-span-5 flex flex-col gap-4">
                <div class="rounded-3xl border border-slate-200/50 bg-white/70 p-4 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    
                    <!-- Main image placeholder or gallery slider -->
                    <div class="relative w-full h-[320px] sm:h-[400px] rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-950/60 flex items-center justify-center">
                        @php
                            $images = $product->getMedia('product-images');
                        @endphp
                        
                        @if($images->count() > 0)
                            <div class="swiper main-swiper h-full w-full">
                                <div class="swiper-wrapper">
                                    @foreach($images as $img)
                                        <div class="swiper-slide flex items-center justify-center">
                                            <img src="{{ $img->getUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-next text-slate-600 dark:text-white"></div>
                                <div class="swiper-button-prev text-slate-600 dark:text-white"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                        @else
                            <!-- SVG Electronic chip placeholder -->
                            <div class="flex flex-col items-center justify-center p-8 text-slate-400 dark:text-slate-600">
                                <svg class="h-20 w-20 text-indigo-500/50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                    <rect x="4" y="4" width="16" height="16" rx="2" />
                                    <rect x="9" y="9" width="6" height="6" rx="1" />
                                    <path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3" />
                                </svg>
                                <span class="text-xs text-slate-400 mt-2">Gambar produk belum tersedia</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Essential Buy Box & Specifications (Col span 7) -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                
                <!-- Core Description Card -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-8 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm flex flex-col gap-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-indigo-550/10 text-indigo-600 dark:text-indigo-400 px-3 py-1 text-[10px] font-bold uppercase tracking-wider">
                                {{ $product->category->name }}
                            </span>
                            @if($product->brand)
                                <span class="text-xs text-slate-400 font-bold uppercase">{{ $product->brand }}</span>
                            @endif
                        </div>
                        
                        <!-- SKU label -->
                        <span class="text-[10px] text-slate-400 font-mono">SKU: {{ $product->sku }}</span>
                    </div>

                    <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Pricing Info -->
                    <div class="flex items-baseline gap-3 my-2">
                        @if($product->is_on_sale)
                            <span class="text-sm text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @endif
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-indigo-300">
                            Rp {{ number_format($product->effective_price, 0, ',', '.') }}
                        </span>
                        
                        @if($product->is_on_sale)
                            <span class="rounded-lg bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-600 dark:bg-rose-950/20">
                                Diskon {{ $product->discount_percent }}% OFF
                            </span>
                        @endif
                    </div>

                    <!-- Inventory & Specs mini logs -->
                    <div class="flex items-center gap-6 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-1.5">
                            @if($product->is_available)
                                @if($product->track_stock && $product->stock <= 5)
                                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-amber-600 font-bold">Stok kritis (Hanya sisa {{ $product->stock }})</span>
                                @else
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-emerald-600 font-medium">Stok Tersedia ({{ $product->stock }} item)</span>
                                @endif
                            @else
                                <span class="h-2 w-2 rounded-full bg-rose-600"></span>
                                <span class="text-rose-600 font-bold">Stok Habis</span>
                            @endif
                        </div>
                        <div>&bull;</div>
                        <div>
                            <i class="fa-solid fa-weight-hanging mr-1"></i> Berat: {{ $product->weight }} gram
                        </div>
                    </div>

                    <p class="text-sm text-slate-500 dark:text-slate-400 font-light leading-relaxed">
                        {{ $product->excerpt }}
                    </p>

                    <!-- Add to Cart Interactive Panel -->
                    @if($product->is_available)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex flex-wrap items-center gap-4 border-t border-slate-100 dark:border-slate-800/80 pt-6 mt-2">
                            @csrf
                            <div class="flex items-center rounded-xl border border-slate-200 bg-white/50 px-2 dark:border-slate-800 dark:bg-slate-950/40" x-data="{ qty: 1 }">
                                <button type="button" @click="if(qty > 1) qty--" class="p-2 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200"><i class="fa-solid fa-minus text-xs"></i></button>
                                <input type="number" name="quantity" x-model="qty" readonly class="w-12 text-center text-sm font-bold bg-transparent border-none outline-none focus:ring-0">
                                <button type="button" @click="if(qty < {{ $product->track_stock ? $product->stock : 99 }}) qty++" class="p-2 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200"><i class="fa-solid fa-plus text-xs"></i></button>
                            </div>

                            <div class="flex gap-2 w-full mt-2">
                                <button type="submit" class="flex-grow inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-indigo-500 transition-all shadow-indigo-600/10">
                                    <i class="fa-solid fa-cart-plus mr-2"></i> Tambah ke Keranjang
                                </button>
                                
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-chat-product', { detail: { id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', price: {{ $product->price }}, sale_price: {{ $product->sale_price ?? 'null' }}, slug: '{{ $product->slug }}', media: [{ original_url: '{{ $product->getFirstMediaUrl('product-images') ?: asset('images/product-placeholder.webp') }}' }] } }))" class="flex-shrink-0 inline-flex items-center justify-center rounded-xl bg-white border border-slate-200 px-4 py-3 text-sm font-bold text-indigo-600 hover:bg-slate-50 transition-all dark:bg-slate-900 dark:border-slate-700 dark:hover:bg-slate-800" title="Tanya Penjual via Chat">
                                    <i class="fa-regular fa-message text-lg"></i>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6 mt-2">
                            <button type="button" disabled class="w-full flex items-center justify-center rounded-xl bg-slate-100 px-6 py-3 text-sm font-bold text-slate-400 cursor-not-allowed dark:bg-slate-950 dark:text-slate-700">
                                <i class="fa-solid fa-ban mr-2"></i> Produk Tidak Tersedia / Stok Habis
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Product Specifications Card (Parsed JSON specifications) -->
                @if(is_array($product->specifications) && count($product->specifications) > 0)
                    <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-8 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-950 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-indigo-500"></i> Spesifikasi Teknis
                        </h3>
                        <div class="overflow-hidden border border-slate-100 rounded-2xl dark:border-slate-800 bg-white/20 dark:bg-slate-950/20">
                            <table class="w-full text-left text-xs sm:text-sm">
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($product->specifications as $key => $val)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10">
                                            <td class="px-4 py-3 font-semibold text-slate-500 dark:text-slate-400 w-1/3 border-r border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-950/40">{{ $key }}</td>
                                            <td class="px-4 py-3 text-slate-700 dark:text-slate-200 font-light">{{ $val }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

        </div>

        <!-- Product Long Description / Documentation -->
        @if($product->body)
            <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-10 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm mb-12">
                <h3 class="text-lg font-bold text-slate-950 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-800/80 pb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-indigo-500"></i> Deskripsi & Dokumentasi Produk
                </h3>
                <div class="prose max-w-none text-slate-600 dark:text-slate-300 text-sm leading-relaxed font-light">
                    {!! $product->body !!}
                </div>
            </div>
        @endif

        <!-- Cross-Promotion: Recommending Articles referencing this product -->
        @if($product->relatedArticles->count() > 0)
            <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 sm:p-8 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                <h3 class="text-lg font-bold tracking-tight text-slate-950 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-regular fa-newspaper text-indigo-500"></i> Panduan & Proyek Terkait
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($product->relatedArticles as $article)
                        <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-white/50 p-4 dark:border-slate-800 dark:bg-slate-950/30 group hover:border-indigo-500/30 transition-all">
                            <div class="h-16 w-24 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0">
                                <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                            </div>
                            <div class="flex-grow min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-500 transition-colors truncate">
                                    <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
                                </h4>
                                <span class="text-[10px] text-slate-400 mt-1 block"><i class="fa-regular fa-clock mr-1"></i> {{ $article->reading_time_text }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize swiper gallery if present
        if (typeof Swiper !== 'undefined' && document.querySelector('.main-swiper')) {
            new Swiper('.main-swiper', {
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    });
</script>
@endsection
