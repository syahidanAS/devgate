@extends('layouts.cms')

@section('title', 'Ubah Komponen — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span>Marketplace</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.products.index') }}" class="hover:text-white">Komponen</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Ubah</span>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-pen-to-square text-indigo-500"></i> Ubah Komponen
            </h1>
            <p class="text-slate-450 text-xs mt-1">Mengedit informasi, harga, stok, dan spesifikasi teknis dari komponen <strong class="text-indigo-400">{{ $product->name }}</strong>.</p>
        </div>
        <a 
            href="{{ route('cms.products.index') }}" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-850 hover:text-white text-xs font-bold text-slate-300 transition-all"
        >
            <i class="fa-solid fa-arrow-left-long"></i> Kembali
        </a>
    </div>

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="rounded-2xl bg-rose-500/10 border border-rose-500/30 p-4 text-xs text-rose-400">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-sm mt-0.5"></i>
                <div>
                    <h5 class="font-bold text-sm text-white mb-1">Periksa kembali isian formulir:</h5>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('cms.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        @method('PUT')

        <!-- Main Form Panel (Left 2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Section: Primary Information -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i> Informasi Utama
                </h3>

                <!-- Name Input -->
                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-bold text-slate-300">Nama Produk / Komponen <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $product->name) }}" 
                        placeholder="Contoh: ESP32-WROOM-32D Development Board" 
                        required
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                    >
                </div>

                <!-- Brand & SKU Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="brand" class="text-xs font-bold text-slate-300">Brand / Manufaktur</label>
                        <input 
                            type="text" 
                            id="brand" 
                            name="brand" 
                            value="{{ old('brand', $product->brand) }}" 
                            placeholder="Contoh: Espressif System, Adafruit" 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label for="sku" class="text-xs font-bold text-slate-300">SKU (Stock Keeping Unit)</label>
                        <input 
                            type="text" 
                            id="sku" 
                            name="sku" 
                            value="{{ old('sku', $product->sku) }}" 
                            placeholder="Biarkan kosong untuk generate otomatis" 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all font-mono"
                        >
                    </div>
                </div>

                <!-- Excerpt Input -->
                <div class="space-y-1.5">
                    <label for="excerpt" class="text-xs font-bold text-slate-300">Deskripsi Singkat / Ringkasan</label>
                    <textarea 
                        id="excerpt" 
                        name="excerpt" 
                        rows="2" 
                        maxlength="500"
                        placeholder="Deskripsi singkat produk untuk tampilan card catalog belanja..." 
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all resize-none"
                    >{{ old('excerpt', $product->excerpt) }}</textarea>
                </div>

                <!-- Description Full Input -->
                <div class="space-y-1.5">
                    <label for="description" class="text-xs font-bold text-slate-300">Deskripsi Lengkap <span class="text-rose-500">*</span></label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="6" 
                        required
                        placeholder="Jelaskan secara mendalam tentang fitur produk, kelengkapan paket, dan petunjuk dasar..." 
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                    >{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- Section: Inventory & Specifications (Alpine.js JSON builder) -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-5 backdrop-blur-sm" x-data="{ 
                specs: [
                    @if(old('specs_key'))
                        @foreach(old('specs_key') as $i => $key)
                            { key: '{{ $key }}', val: '{{ old('specs_val')[$i] ?? '' }}' },
                        @endforeach
                    @elseif(!empty($product->specifications))
                        @foreach($product->specifications as $key => $val)
                            { key: '{{ addslashes($key) }}', val: '{{ addslashes($val) }}' },
                        @endforeach
                    @else
                        { key: '', val: '' }
                    @endif
                ],
                addSpec() {
                    this.specs.push({ key: '', val: '' });
                },
                removeSpec(index) {
                    this.specs.splice(index, 1);
                }
            }">
                <div class="flex items-center justify-between border-b border-slate-800/60 pb-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-500"></i> Spesifikasi Teknis (JSON)
                    </h3>
                    <button 
                        type="button" 
                        @click="addSpec"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-600/10 border border-indigo-500/20 text-[10px] font-bold text-indigo-400 hover:bg-indigo-600 hover:text-white transition-all"
                    >
                        <i class="fa-solid fa-plus text-[8px]"></i> Tambah Baris
                    </button>
                </div>

                <p class="text-[11px] text-slate-500">Spesifikasi ini akan dipetakan ke dalam format JSON dinamis untuk ditampilkan dalam tabel data sheet teknis produk.</p>

                <!-- Dynamic specifications input fields -->
                <div class="space-y-3">
                    <template x-for="(spec, index) in specs" :key="index">
                        <div class="flex items-center gap-3 bg-slate-900/40 p-2.5 rounded-2xl border border-slate-850">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-grow">
                                <div>
                                    <input 
                                        type="text" 
                                        name="specs_key[]" 
                                        x-model="spec.key"
                                        placeholder="Kunci (contoh: Tegangan Daya)" 
                                        class="w-full rounded-xl border border-slate-800 bg-slate-950/70 px-3 py-2 text-xs text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none transition-all"
                                    >
                                </div>
                                <div>
                                    <input 
                                        type="text" 
                                        name="specs_val[]" 
                                        x-model="spec.val"
                                        placeholder="Nilai (contoh: 5V DC)" 
                                        class="w-full rounded-xl border border-slate-800 bg-slate-950/70 px-3 py-2 text-xs text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none transition-all"
                                    >
                                </div>
                            </div>
                            <button 
                                type="button" 
                                @click="removeSpec(index)"
                                class="h-8 w-8 shrink-0 flex items-center justify-center rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-600 hover:text-white transition-all"
                                title="Hapus spesifikasi"
                            >
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Section: Dynamic Gallery Multi Image Upload -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-regular fa-images text-indigo-500"></i> Galeri Foto Produk
                </h3>

                <!-- Uploaded Media List -->
                @if($product->hasMedia('product-images'))
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Gambar Saat Ini:</label>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                            @foreach($product->getMedia('product-images') as $media)
                                <div class="relative h-16 w-16 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 flex items-center justify-center">
                                    <img src="{{ $media->getUrl('thumbnail') }}" alt="Product Image" class="object-cover w-full h-full">
                                    <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                        <span class="text-[9px] px-1 py-0.5 bg-slate-950 border border-slate-850 rounded text-slate-300 font-mono">
                                            {{ number_format($media->size / 1024, 0) }} KB
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="border-t border-slate-800/40 my-3"></div>
                @endif

                <!-- Upload field -->
                <div class="space-y-3" x-data="{ 
                    previews: [],
                    handleFiles(event) {
                        this.previews = [];
                        const files = event.target.files;
                        for (let i = 0; i < files.length; i++) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.previews.push(e.target.result);
                            }
                            reader.readAsDataURL(files[i]);
                        }
                    }
                }">
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-800 hover:border-slate-700 bg-slate-950/40 rounded-3xl cursor-pointer transition-all">
                            <div class="flex flex-col items-center justify-center pt-4 pb-5">
                                <i class="fa-solid fa-cloud-arrow-up text-slate-500 text-xl mb-1.5"></i>
                                <p class="text-[11px] text-slate-400 font-bold">Unggah Gambar Tambahan</p>
                                <p class="text-[9px] text-slate-500 mt-0.5">Format PNG, JPG, JPEG (Maks 2MB, disarankan rasio 1:1)</p>
                            </div>
                            <input 
                                type="file" 
                                name="images[]" 
                                multiple 
                                class="hidden" 
                                accept="image/*"
                                @change="handleFiles"
                            >
                        </label>
                    </div>

                    <!-- Images previews grid -->
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3" x-show="previews.length > 0" x-cloak>
                        <template x-for="(preview, index) in previews" :key="index">
                            <div class="h-16 w-16 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 relative group">
                                <img :src="preview" class="object-cover w-full h-full">
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Section: SEO Metadata -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-4 backdrop-blur-sm" x-data="{ open: false }">
                <button 
                    type="button" 
                    @click="open = !open" 
                    class="w-full flex items-center justify-between text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3"
                >
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-chart text-indigo-500"></i> Optimasi SEO (Opsional)
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </button>

                <div class="space-y-4 pt-2" x-show="open" x-collapse x-cloak>
                    <div class="space-y-1.5">
                        <label for="meta_title" class="text-xs font-bold text-slate-300">Meta Title Tag</label>
                        <input 
                            type="text" 
                            id="meta_title" 
                            name="meta_title" 
                            value="{{ old('meta_title', $product->meta_title) }}" 
                            placeholder="Biarkan kosong untuk menyamakan dengan nama produk" 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white focus:border-indigo-500 focus:outline-none transition-all"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label for="meta_description" class="text-xs font-bold text-slate-300">Meta Description Tag</label>
                        <textarea 
                            id="meta_description" 
                            name="meta_description" 
                            rows="2" 
                            placeholder="Ringkasan penjelasan ramah mesin pencari Google..." 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white focus:border-indigo-500 focus:outline-none transition-all resize-none font-sans"
                        >{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar Panel (Right Column) -->
        <div class="space-y-6">
            
            <!-- Section: Save Actions & Publishing -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-indigo-500"></i> Publikasi
                </h3>

                <!-- Status Selector -->
                <div class="space-y-1.5">
                    <label for="status" class="text-xs font-bold text-slate-300">Status Katalog <span class="text-rose-500">*</span></label>
                    <select 
                        id="status" 
                        name="status" 
                        required
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-3 text-xs text-white focus:border-indigo-500 focus:outline-none transition-all"
                    >
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Aktif (Tampil di Toko)</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif (Disembunyikan)</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draf</option>
                    </select>
                </div>

                <!-- Featured Toggle -->
                <div class="flex items-center justify-between bg-slate-900/30 p-3 rounded-2xl border border-slate-850">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-white">Produk Unggulan</span>
                        <span class="text-[9px] text-slate-500 mt-0.5">Tampilkan di halaman depan.</span>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="sr-only peer" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        <div class="relative w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white peer-checked:after:border-transparent"></div>
                    </label>
                </div>

                <!-- Save Buttons -->
                <div class="space-y-2 pt-2">
                    <button 
                        type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all"
                    >
                        <i class="fa-regular fa-floppy-disk"></i> Perbarui Produk
                    </button>
                    <a 
                        href="{{ route('cms.products.index') }}" 
                        class="w-full inline-flex items-center justify-center px-4 py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-850 text-xs font-bold text-slate-400 hover:text-white transition-all"
                    >
                        Batalkan
                    </a>
                </div>
            </div>

            <!-- Section: Category & Parameters -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-500"></i> Kategori & Parameter
                </h3>

                <!-- Category Selector -->
                <div class="space-y-1.5">
                    <label for="category_id" class="text-xs font-bold text-slate-300">Kategori Marketplace <span class="text-rose-500">*</span></label>
                    <select 
                        id="category_id" 
                        name="category_id" 
                        required
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950 px-4 py-3 text-xs text-white focus:border-indigo-500 focus:outline-none transition-all"
                    >
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Weight Input -->
                <div class="space-y-1.5">
                    <label for="weight" class="text-xs font-bold text-slate-300">Berat Komponen (Gram) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input 
                            type="number" 
                            id="weight" 
                            name="weight" 
                            value="{{ old('weight', $product->weight) }}" 
                            min="1"
                            required
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 pr-12 text-xs text-white focus:border-indigo-500 focus:outline-none transition-all font-mono"
                        >
                        <span class="absolute right-4 top-3.5 text-[10px] text-slate-500 font-bold uppercase">gr</span>
                    </div>
                    <span class="text-[9px] text-slate-500 leading-normal block">Digunakan untuk kalkulasi ongkos kirim otomatis (RajaOngkir).</span>
                </div>
            </div>

            <!-- Section: Pricing & Inventory Control -->
            <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 space-y-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800/60 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-indigo-500"></i> Harga & Manajemen Stok
                </h3>

                <!-- Price Input -->
                <div class="space-y-1.5">
                    <label for="price" class="text-xs font-bold text-slate-300">Harga Normal (Rupiah) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-xs text-slate-550 font-extrabold font-mono">Rp</span>
                        <input 
                            type="number" 
                            id="price" 
                            name="price" 
                            value="{{ old('price', $product->price) }}" 
                            placeholder="Contoh: 75000" 
                            min="0"
                            required
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 pl-11 pr-4 py-3 text-xs text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none transition-all font-mono"
                        >
                    </div>
                </div>

                <!-- Sale Price Input -->
                <div class="space-y-1.5">
                    <label for="sale_price" class="text-xs font-bold text-slate-300">Harga Promo / Diskon (Rupiah)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-xs text-slate-550 font-extrabold font-mono">Rp</span>
                        <input 
                            type="number" 
                            id="sale_price" 
                            name="sale_price" 
                            value="{{ old('sale_price', $product->sale_price) }}" 
                            placeholder="Contoh: 60000 (Kosongkan jika tidak promo)" 
                            min="0"
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 pl-11 pr-4 py-3 text-xs text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none transition-all font-mono"
                        >
                    </div>
                    <span class="text-[9px] text-slate-550">Harus lebih kecil dari Harga Normal.</span>
                </div>

                <div class="border-t border-slate-800/40 my-3"></div>

                <!-- Track Stock Toggle -->
                <div class="flex items-center justify-between bg-slate-900/30 p-3 rounded-2xl border border-slate-850" x-data="{ trackStock: {{ old('track_stock', $product->track_stock ? '1' : '0') == '1' ? 'true' : 'false' }} }">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-white">Kelola Sisa Stok</span>
                        <span class="text-[9px] text-slate-500 mt-0.5">Pantau jumlah barang.</span>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="track_stock" 
                            value="1" 
                            class="sr-only peer" 
                            x-model="trackStock"
                        >
                        <div class="relative w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white peer-checked:after:border-transparent"></div>
                    </label>
                </div>

                <!-- Stock Quantity Input -->
                <div class="space-y-1.5">
                    <label for="stock" class="text-xs font-bold text-slate-300">Jumlah Stok Fisik <span class="text-rose-500">*</span></label>
                    <input 
                        type="number" 
                        id="stock" 
                        name="stock" 
                        value="{{ old('stock', $product->stock) }}" 
                        min="0"
                        required
                        class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs text-white focus:border-indigo-500 focus:outline-none transition-all font-mono"
                    >
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
