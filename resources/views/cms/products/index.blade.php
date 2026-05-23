@extends('layouts.cms')

@section('title', 'Kelola Komponen — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Marketplace</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Komponen</span>
</div>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    selectedIds: [],
    selectAll: false,
    allIds: {{ $products->pluck('id')->toJson() }},
    toggleAll() {
        if (this.selectAll) {
            this.selectedIds = [...this.allIds];
        } else {
            this.selectedIds = [];
        }
    }
}">

    <!-- Page Header & Action Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-800/60 pb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-boxes-stacked text-indigo-500"></i> Kelola Komponen & IoT
            </h1>
            <p class="text-slate-450 text-xs mt-1">Daftar, ubah spesifikasi, kelola stok barang, dan atur katalog produk marketplace elektronik.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Search & Filter Form -->
            <form action="{{ route('cms.products.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama atau SKU produk..." 
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 pl-11 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all backdrop-blur-sm"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-500 text-xs"></i>
                    @if(request('search'))
                        <a href="{{ route('cms.products.index') }}" class="absolute right-4 top-3.5 text-slate-500 hover:text-white transition-colors">
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                        </a>
                    @endif
                </div>

                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
            </form>

            <template x-if="selectedIds.length > 0">
                <form action="{{ route('cms.products.bulkDestroy') }}" method="POST" class="w-full sm:w-auto flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' produk terpilih?')">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white shadow-lg shadow-rose-600/25 transition-all w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus (<span x-text="selectedIds.length"></span>)
                    </button>
                </form>
            </template>

            <a 
                href="{{ route('cms.products.create') }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all w-full sm:w-auto justify-center"
            >
                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Komponen
            </a>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-800/60 bg-slate-950/60">
                        <th class="py-4 px-4 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-700 bg-slate-900/50 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-slate-950">
                        </th>
                        <th class="py-4 px-6">Informasi Produk</th>
                        <th class="py-4 px-6">SKU & Kategori</th>
                        <th class="py-4 px-6">Harga (IDR)</th>
                        <th class="py-4 px-6">Stok & Berat</th>
                        <th class="py-4 px-6">Status Katalog</th>
                        <th class="py-4 px-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($products as $product)
                        <tr class="group hover:bg-slate-900/20 transition-all">
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" :value="{{ $product->id }}" x-model="selectedIds" class="rounded border-slate-700 bg-slate-900/50 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-slate-950">
                            </td>
                            <!-- Image, Brand & Name -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 shrink-0 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 flex items-center justify-center relative">
                                        @if($product->hasMedia('product-images'))
                                            <img src="{{ $product->getFirstMediaUrl('product-images', 'thumbnail') }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                        @else
                                            <i class="fa-solid fa-microchip text-slate-650 text-lg"></i>
                                        @endif
                                    </div>
                                    <div class="max-w-[240px]">
                                        <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">
                                            {{ $product->brand ?: 'Tanpa Brand' }}
                                        </div>
                                        <div class="text-xs font-bold text-white group-hover:text-indigo-400 transition-colors leading-tight mt-0.5 truncate" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </div>
                                        <div class="text-[9px] text-slate-550 font-mono mt-1 flex items-center gap-1">
                                            <i class="fa-regular fa-eye"></i> {{ $product->view_count }}x dilihat
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- SKU & Category -->
                            <td class="py-4 px-6 text-slate-300">
                                <div class="text-xs font-mono font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-barcode text-slate-600 text-[10px]"></i> {{ $product->sku }}
                                </div>
                                <div class="text-[10px] text-slate-450 mt-1 font-semibold">
                                    {{ $product->category ? $product->category->name : 'Kategori Terhapus' }}
                                </div>
                            </td>

                            <!-- Price (with discount calculations) -->
                            <td class="py-4 px-6">
                                @if($product->is_on_sale)
                                    <div>
                                        <div class="text-xs font-extrabold text-emerald-400">
                                            {{ $product->formatted_effective_price }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                            <span class="line-through">{{ $product->formatted_price }}</span>
                                            <span class="px-1 py-0.2 rounded bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[9px] font-bold">
                                                -{{ $product->discount_percent }}%
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs font-bold text-white">
                                        {{ $product->formatted_price }}
                                    </div>
                                @endif
                            </td>

                            <!-- Stock & Weight -->
                            <td class="py-4 px-6 text-slate-300">
                                <div class="flex items-center">
                                    @if(!$product->track_stock)
                                        <span class="inline-flex items-center rounded-lg border border-indigo-500/20 bg-indigo-500/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-indigo-400">
                                            Selalu Tersedia
                                        </span>
                                    @elseif($product->stock === 0)
                                        <span class="inline-flex items-center rounded-lg border border-rose-500/20 bg-rose-500/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-rose-400">
                                            Stok Habis (0)
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span class="inline-flex items-center rounded-lg border border-amber-500/20 bg-amber-500/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-400">
                                            Kritis ({{ $product->stock }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-400">
                                            Tersedia ({{ $product->stock }})
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-weight-hanging text-[9px]"></i> {{ number_format($product->weight, 0, ',', '.') }} gram
                                </div>
                            </td>

                            <!-- Catalog Status -->
                            <td class="py-4 px-6">
                                @if($product->status === 'active')
                                    <span class="inline-flex items-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> aktif
                                    </span>
                                @elseif($product->status === 'inactive')
                                    <span class="inline-flex items-center rounded-lg border border-rose-500/20 bg-rose-500/10 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-rose-400">
                                        tidak aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-slate-700 bg-slate-800/80 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                        draf
                                    </span>
                                @endif
                            </td>

                            <!-- Actions edit/delete buttons -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a 
                                        href="{{ route('cms.products.edit', $product->id) }}" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-350 hover:bg-indigo-600 hover:border-indigo-500 hover:text-white transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-pen text-[10px]"></i> Edit
                                    </a>
                                    
                                    <form action="{{ route('cms.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-rose-455 hover:bg-rose-600 hover:border-rose-500 hover:text-white transition-all shadow-sm"
                                        >
                                            <i class="fa-regular fa-trash-can text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="h-16 w-16 rounded-full bg-slate-900/60 border border-slate-850 flex items-center justify-center text-slate-500 text-xl">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </div>
                                    <p class="text-sm">Tidak ada produk komponen ditemukan.</p>
                                    @if(request('search'))
                                        <a href="{{ route('cms.products.index') }}" class="text-xs text-indigo-400 hover:underline">Hapus Pencarian</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination controls -->
        @if($products->hasPages())
            <div class="border-t border-slate-800/60 px-6 py-4 bg-slate-950/30">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
