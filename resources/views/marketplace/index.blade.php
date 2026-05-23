@extends('layouts.app')

@section('title', 'Toko IoT & Komponen Elektronik — DevGate Marketplace')

@section('content')
<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Header Banner Section -->
        <div class="mb-10 text-center md:text-left flex flex-col md:flex-row items-center justify-between gap-6 border-b border-slate-200/50 pb-8 dark:border-slate-800/50">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-600 bg-clip-text text-transparent dark:from-white dark:via-slate-200 dark:to-indigo-400">
                    Toko IoT & Komponen Elektronik
                </h1>
                <p class="mt-2 text-sm sm:text-base text-slate-500 dark:text-slate-400">
                    Sediakan komponen terbaik untuk proyek Embedded System, IoT, Automation, dan Robotika Anda.
                </p>
            </div>
            
            <!-- Quick Search Bar -->
            <form action="{{ route('shop.index') }}" method="GET" class="relative max-w-sm w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari komponen..." class="w-full rounded-2xl border border-slate-200 bg-white/70 pl-11 pr-4 py-2.5 text-sm dark:border-slate-800 dark:bg-slate-900/70 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all backdrop-blur-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 text-sm"></i>
            </form>
        </div>

        <!-- Main Workspace split grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar Filters Area (Col span 1) -->
            <aside class="lg:col-span-1 rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm h-fit">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-950 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-500"></i> Filter Komponen
                </h3>

                <form action="{{ route('shop.index') }}" method="GET" class="flex flex-col gap-6" id="filter-form">
                    <!-- Preserve search parameter if present -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Category filter radio list -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-3">Kategori</h4>
                        <div class="flex flex-col gap-2 max-h-52 overflow-y-auto pr-2">
                            <label class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded-full text-indigo-600 focus:ring-indigo-500 border-slate-350 dark:border-slate-800 dark:bg-slate-950">
                                <span>Semua Kategori</span>
                            </label>
                            @foreach($categories as $category)
                                <label class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                                    <span class="flex items-center gap-2.5">
                                        <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'checked' : '' }} onchange="this.form.submit()" class="rounded-full text-indigo-600 focus:ring-indigo-500 border-slate-350 dark:border-slate-800 dark:bg-slate-950">
                                        <span>{{ $category->name }}</span>
                                    </span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400">{{ $category->products()->active()->count() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Brand filter list -->
                    @if($brands->count() > 0)
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-3">Merek / Produsen</h4>
                            <div class="flex flex-col gap-2 max-h-40 overflow-y-auto pr-2">
                                <label class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                                    <input type="radio" name="brand" value="" {{ !request('brand') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded-full text-indigo-600 focus:ring-indigo-500 border-slate-350 dark:border-slate-800 dark:bg-slate-950">
                                    <span>Semua Merek</span>
                                </label>
                                @foreach($brands as $brand)
                                    <label class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                                        <input type="radio" name="brand" value="{{ $brand }}" {{ request('brand') === $brand ? 'checked' : '' }} onchange="this.form.submit()" class="rounded-full text-indigo-600 focus:ring-indigo-500 border-slate-350 dark:border-slate-800 dark:bg-slate-950">
                                        <span>{{ $brand }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Price Filter inputs -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-3">Rentang Harga (Rp)</h4>
                        <div class="flex flex-col gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full rounded-xl border border-slate-200 bg-white/50 px-3 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full rounded-xl border border-slate-200 bg-white/50 px-3 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- In stock filter -->
                    <div>
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 dark:text-slate-300 cursor-pointer font-medium">
                            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                            <span>Tampilkan Stok Tersedia</span>
                        </label>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex gap-2 border-t border-slate-100 dark:border-slate-800 pt-4 mt-2">
                        <a href="{{ route('shop.index') }}" class="w-1/2 flex items-center justify-center rounded-xl border border-slate-350 dark:border-slate-700 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Reset
                        </a>
                        <button type="submit" class="w-1/2 flex items-center justify-center rounded-xl bg-indigo-600 py-2 text-xs font-bold text-white shadow-md hover:bg-indigo-500 transition-colors">
                            Terapkan
                        </button>
                    </div>
                </form>
            </aside>

            <!-- Catalog Section (Col span 3) -->
            <div class="lg:col-span-3 flex flex-col gap-6">
                
                <!-- Filter status info and sorting -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/40 pb-4 dark:border-slate-800/40">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk</span>
                    
                    <div class="flex items-center gap-2 justify-end self-end sm:self-auto">
                        <span class="text-xs text-slate-400 font-medium">Urutkan:</span>
                        <select name="sort" form="filter-form" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white/70 py-1.5 px-3 text-xs dark:border-slate-800 dark:bg-slate-900/70 focus:border-indigo-500 focus:outline-none">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        </select>
                    </div>
                </div>

                @if($products->count() > 0)
                    <!-- Product catalog grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            @php
                                $imgUrl = $product->getFirstMediaUrl('product-images');
                            @endphp
                            <div class="flex flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/60 bg-white/70 p-4 shadow-sm hover:shadow-md dark:border-slate-800/80 dark:bg-slate-900/40 hover:scale-[1.01] transition-all backdrop-blur-sm relative group">
                                
                                <!-- Sale Badge overlay -->
                                @if($product->is_on_sale)
                                    <span class="absolute top-6 left-6 z-10 rounded-lg bg-rose-600 px-2 py-0.5 text-[9px] font-extrabold tracking-wider uppercase text-white shadow-sm">
                                        Promo {{ $product->discount_percent }}% OFF
                                    </span>
                                @endif

                                <div>
                                    <!-- Thumbnail image -->
                                    <div class="relative w-full h-44 rounded-2xl overflow-hidden mb-4 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                        @else
                                            <!-- Breathtaking SVG Electronic Chip Placeholder -->
                                            <div class="h-full w-full bg-gradient-to-tr from-indigo-900/10 to-violet-900/10 flex flex-col items-center justify-center p-6 text-slate-400 dark:text-slate-600">
                                                <svg class="h-12 w-12 text-indigo-500/70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="4" y="4" width="16" height="16" rx="2" />
                                                    <rect x="9" y="9" width="6" height="6" rx="1" />
                                                    <path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Category & Brand Info -->
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 mb-1.5">
                                        <span class="font-medium uppercase tracking-wider">{{ $product->category->name }}</span>
                                        @if($product->brand)
                                            <span class="font-bold text-slate-500 dark:text-slate-400">{{ $product->brand }}</span>
                                        @endif
                                    </div>

                                    <!-- Product Name -->
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug mb-2">
                                        <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                    
                                    <!-- Excerpt description -->
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-light line-clamp-2 leading-relaxed mb-4">
                                        {{ $product->excerpt }}
                                    </p>
                                </div>

                                <div>
                                    <!-- Stock info -->
                                    <div class="flex items-center gap-1.5 text-[10px] mb-3">
                                        @if($product->is_available)
                                            @if($product->track_stock && $product->stock <= 5)
                                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span class="text-amber-600 font-bold">Stok Menipis ({{ $product->stock }} item)</span>
                                            @else
                                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                <span class="text-emerald-600 font-medium">Tersedia</span>
                                            @endif
                                        @else
                                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                                            <span class="text-rose-600 font-bold">Stok Habis</span>
                                        @endif
                                    </div>

                                    <!-- Price tag & Add to cart button -->
                                    <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-3 mt-auto">
                                        <div class="flex flex-col">
                                            @if($product->is_on_sale)
                                                <span class="text-[10px] text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            @endif
                                            <span class="text-sm font-bold text-slate-900 dark:text-white bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-indigo-300">
                                                Rp {{ number_format($product->effective_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        
                                        @if($product->is_available)
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-bold text-white shadow-md hover:bg-indigo-500 transition-colors shadow-indigo-600/10 hover:shadow-indigo-600/20">
                                                    <i class="fa-solid fa-cart-plus mr-1"></i> Beli
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" disabled class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-400 cursor-not-allowed dark:bg-slate-950 dark:text-slate-700">
                                                Habis
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Custom Pagination links -->
                    <div class="mt-10">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- Empty products catalog state -->
                    <div class="flex flex-col items-center justify-center py-20 text-center rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/30 dark:bg-slate-900/10">
                        <i class="fa-solid fa-box-open text-slate-300 dark:text-slate-700 text-6xl mb-4"></i>
                        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Komponen tidak ditemukan</h3>
                        <p class="text-sm text-slate-400 mt-1 max-w-sm">Maaf, kami tidak menemukan komponen elektronik yang sesuai dengan preferensi filter Anda.</p>
                        <a href="{{ route('shop.index') }}" class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-md hover:bg-indigo-500 transition-colors">
                            Reset Pencarian
                        </a>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
