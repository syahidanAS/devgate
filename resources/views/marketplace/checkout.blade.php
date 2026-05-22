@extends('layouts.app')

@section('title', 'Proses Checkout Pesanan — DevGate')

@section('content')
<div class="py-8 sm:py-12" x-data="checkoutApp()">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl mb-8">
            Checkout Pembelian
        </h1>

        <!-- Core Split Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Shipping and Courier inputs (Col span 7) -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                
                <!-- 1. Alamat Pengiriman Card -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    <div class="flex items-center justify-between gap-4 mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-base font-bold text-slate-950 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-indigo-500"></i> Alamat Pengiriman
                        </h2>
                        <button type="button" @click="addressModalOpen = true" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <i class="fa-solid fa-plus text-[10px]"></i> Alamat Baru
                        </button>
                    </div>

                    <!-- List of existing addresses -->
                    <div class="flex flex-col gap-3">
                        <template x-for="addr in addresses" :key="addr.id">
                            <label :class="{ 'border-indigo-500 bg-indigo-50/20 dark:border-indigo-500 dark:bg-indigo-950/20': selectedAddressId === addr.id }"
                                   class="relative flex flex-col sm:flex-row gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer hover:border-slate-300 dark:border-slate-800 dark:hover:border-slate-700 transition-all">
                                
                                <input type="radio" name="checkout_address" :value="addr.id" :checked="selectedAddressId === addr.id" @change="selectAddress(addr.id)" class="mt-1 text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                                
                                <div class="flex-grow">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200" x-text="addr.recipient_name"></span>
                                        <span class="text-[10px] font-mono font-medium rounded bg-slate-100 text-slate-500 px-1.5 py-0.5 dark:bg-slate-800 dark:text-slate-400" x-text="addr.label"></span>
                                        <template x-if="addr.is_default">
                                            <span class="text-[9px] font-bold rounded bg-indigo-600 text-white px-1.5 py-0.5">Utama</span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1" x-text="addr.phone"></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-light mt-1.5" x-text="addr.address + ', ' + addr.city + ', ' + addr.province + ' ' + addr.postal_code"></p>
                                </div>
                            </label>
                        </template>

                        <div x-show="addresses.length === 0" class="text-center py-6 text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                            <p class="text-xs">Belum ada alamat pengiriman terdaftar. Silakan tambahkan alamat baru.</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Pilihan Kurir Pengiriman Card -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm" :class="{ 'opacity-50 pointer-events-none': !selectedAddressId }">
                    <h2 class="text-base font-bold text-slate-950 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-indigo-500"></i> Opsi Pengiriman
                    </h2>

                    <!-- Courier Brands selection -->
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <template x-for="c in ['jne', 'jnt']" :key="c">
                            <button type="button" @click="selectCourier(c)" 
                                    :class="{ 'border-indigo-600 bg-indigo-50/15 dark:border-indigo-500 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400': selectedCourier === c, 'border-slate-200 dark:border-slate-800 text-slate-500 hover:border-slate-350': selectedCourier !== c }"
                                    class="flex flex-col items-center justify-center py-4 px-3 rounded-2xl border font-bold text-xs sm:text-sm tracking-widest gap-2 shadow-sm transition-all">
                                <span x-text="c === 'jnt' ? 'J&T' : c.toUpperCase()"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Courier Services Rates list -->
                    <div class="relative">
                        <!-- Loading spinner -->
                        <div x-show="ongkirLoading" class="flex justify-center items-center py-8">
                            <i class="fa-solid fa-circle-notch animate-spin text-3xl text-indigo-500"></i>
                        </div>

                        <!-- Services Options list -->
                        <div x-show="!ongkirLoading && shippingServices.length > 0" class="flex flex-col gap-3">
                            <template x-for="srv in shippingServices" :key="srv.service">
                                <label :class="{ 'border-indigo-500 bg-indigo-50/20 dark:border-indigo-500 dark:bg-indigo-950/20': selectedService === srv.service }"
                                       class="flex items-center justify-between rounded-2xl border border-slate-250 p-4 cursor-pointer hover:border-slate-300 dark:border-slate-800 transition-all">
                                    
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="shipping_service" :value="srv.service" :checked="selectedService === srv.service" @change="selectService(srv)" class="text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                                        <div>
                                            <span class="text-sm font-bold text-slate-850 dark:text-slate-100" x-text="srv.service"></span>
                                            <span class="text-[10px] text-slate-400 font-medium ml-2" x-text="srv.description"></span>
                                            <span class="text-[10px] font-semibold rounded bg-slate-100 text-slate-500 px-1.5 py-0.5 dark:bg-slate-800 dark:text-slate-400 block sm:inline mt-1 sm:mt-0 sm:ml-2" x-text="'Etd: ' + srv.etd"></span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="'Rp ' + formatPrice(srv.cost)"></span>
                                </label>
                            </template>
                        </div>

                        <div x-show="!ongkirLoading && shippingServices.length === 0 && selectedCourier" class="text-center py-6 text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                            <p class="text-xs">Layanan pengiriman tidak tersedia. Silakan pilih kurir lain atau cek alamat Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- 3. Catatan Pesanan -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm">
                    <h2 class="text-sm font-bold text-slate-950 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <i class="fa-regular fa-clipboard text-indigo-500"></i> Catatan Pembelian
                    </h2>
                    <textarea x-model="notes" rows="3" placeholder="Masukkan catatan tambahan untuk kurir atau penjual (opsional)..." class="w-full rounded-2xl border border-slate-200 bg-white/50 px-3.5 py-2.5 text-sm dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500 focus:outline-none"></textarea>
                </div>

                <!-- 4. Pilihan Metode Pembayaran Card -->
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm" :class="{ 'opacity-50 pointer-events-none': !selectedService }">
                    <h2 class="text-sm font-bold text-slate-950 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-credit-card text-indigo-500"></i> Metode Pembayaran
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- BCA VA -->
                        <label :class="{ 'border-indigo-500 bg-indigo-50/20 dark:border-indigo-500 dark:bg-indigo-950/20': selectedPaymentMethod === 'bca' }"
                               class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer hover:border-slate-350 dark:border-slate-800 dark:hover:border-slate-700 transition-all">
                            <input type="radio" name="payment_method_select" value="bca" x-model="selectedPaymentMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                            <div class="flex-grow flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-850 dark:text-slate-100">BCA Virtual Account</span>
                                    <span class="text-[10px] text-slate-400">Verifikasi Otomatis</span>
                                </div>
                                <span class="text-xs font-black italic text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-1 rounded-lg">BCA</span>
                            </div>
                        </label>

                        <!-- BNI VA -->
                        <label :class="{ 'border-indigo-500 bg-indigo-50/20 dark:border-indigo-500 dark:bg-indigo-950/20': selectedPaymentMethod === 'bni' }"
                               class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer hover:border-slate-350 dark:border-slate-800 dark:hover:border-slate-700 transition-all">
                            <input type="radio" name="payment_method_select" value="bni" x-model="selectedPaymentMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                            <div class="flex-grow flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-850 dark:text-slate-100">BNI Virtual Account</span>
                                    <span class="text-[10px] text-slate-400">Verifikasi Otomatis</span>
                                </div>
                                <span class="text-xs font-black italic text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/40 px-2 py-1 rounded-lg">BNI</span>
                            </div>
                        </label>

                        <!-- BRI VA -->
                        <label :class="{ 'border-indigo-500 bg-indigo-50/20 dark:border-indigo-500 dark:bg-indigo-950/20': selectedPaymentMethod === 'bri' }"
                               class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer hover:border-slate-350 dark:border-slate-800 dark:hover:border-slate-700 transition-all">
                            <input type="radio" name="payment_method_select" value="bri" x-model="selectedPaymentMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                            <div class="flex-grow flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-850 dark:text-slate-100">BRI Virtual Account</span>
                                    <span class="text-[10px] text-slate-400">Verifikasi Otomatis</span>
                                </div>
                                <span class="text-xs font-black italic text-blue-500 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-1 rounded-lg">BRI</span>
                            </div>
                        </label>

                        <!-- Mandiri E-Channel -->
                        <label :class="{ 'border-indigo-500 bg-indigo-50/20 dark:border-indigo-500 dark:bg-indigo-950/20': selectedPaymentMethod === 'mandiri' }"
                               class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer hover:border-slate-355 dark:border-slate-800 dark:hover:border-slate-700 transition-all">
                            <input type="radio" name="payment_method_select" value="mandiri" x-model="selectedPaymentMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                            <div class="flex-grow flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-850 dark:text-slate-100">Mandiri E-Channel</span>
                                    <span class="text-[10px] text-slate-400">Verifikasi Otomatis</span>
                                </div>
                                <span class="text-xs font-black italic text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-950/40 px-2 py-1 rounded-lg">Mandiri</span>
                            </div>
                        </label>

                        <!-- QRIS -->
                        <label :class="{ 'border-indigo-500 bg-indigo-50/20 dark:border-indigo-500 dark:bg-indigo-950/20': selectedPaymentMethod === 'qris' }"
                               class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 cursor-pointer hover:border-slate-350 dark:border-slate-800 dark:hover:border-slate-700 transition-all sm:col-span-2">
                            <input type="radio" name="payment_method_select" value="qris" x-model="selectedPaymentMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800 dark:bg-slate-950">
                            <div class="flex-grow flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-850 dark:text-slate-100">QRIS (Gopay, OVO, Dana, LinkAja, ShopeePay)</span>
                                    <span class="text-[10px] text-slate-400">Scan QR Code Langsung</span>
                                </div>
                                <span class="text-xs font-black tracking-widest text-pink-650 dark:text-pink-400 bg-pink-50 dark:bg-pink-950/40 px-2 py-1 rounded-lg">QRIS</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Right: Order summary & totals (Col span 5) -->
            <div class="lg:col-span-5 sticky top-24">
                <div class="rounded-3xl border border-slate-200/60 bg-white/70 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/40 backdrop-blur-sm flex flex-col gap-6">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-950 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        Ringkasan Order
                    </h2>

                    <!-- Items List -->
                    <div class="flex flex-col gap-3 max-h-48 overflow-y-auto pr-2">
                        @foreach($cartItems as $item)
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg overflow-hidden border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex-shrink-0 flex items-center justify-center">
                                    @if($item->product->getFirstMediaUrl('product-images'))
                                        <img src="{{ $item->product->getFirstMediaUrl('product-images', 'thumbnail') }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fa-solid fa-microchip text-slate-400"></i>
                                    @endif
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $item->product->name }}</h4>
                                    <span class="text-[10px] text-slate-400">{{ $item->quantity }} Pcs</span>
                                </div>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Billing Breakdown -->
                    <div class="flex flex-col gap-3 text-xs border-t border-slate-100 dark:border-slate-800 pt-4">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal Produk</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300" x-text="'Rp ' + formatPrice(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Ongkos Kirim</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300" x-text="shippingCost > 0 ? 'Rp ' + formatPrice(shippingCost) : 'Pilih Kurir'"></span>
                        </div>
                        
                        <div class="h-px bg-slate-100 dark:bg-slate-800 my-1"></div>
                        
                        <div class="flex justify-between text-sm font-bold text-slate-900 dark:text-white">
                            <span>Total Pembayaran</span>
                            <span class="text-indigo-600 dark:text-indigo-400 text-base font-extrabold" x-text="'Rp ' + formatPrice(grandTotal)"></span>
                        </div>
                    </div>

                    <!-- Order Action Processing Form -->
                    <form :action="processRoute" method="POST">
                        @csrf
                        <input type="hidden" name="address_id" :value="selectedAddressId">
                        <input type="hidden" name="courier" :value="selectedCourier">
                        <input type="hidden" name="courier_service" :value="selectedService">
                        <input type="hidden" name="shipping_cost" :value="shippingCost">
                        <input type="hidden" name="notes" :value="notes">
                        <input type="hidden" name="payment_method" :value="selectedPaymentMethod">

                        <button type="submit" 
                                :disabled="!selectedAddressId || !selectedCourier || !selectedService || !selectedPaymentMethod"
                                :class="{ 'bg-slate-100 dark:bg-slate-950 text-slate-450 dark:text-slate-700 cursor-not-allowed shadow-none': !selectedAddressId || !selectedCourier || !selectedService || !selectedPaymentMethod }"
                                class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-indigo-500 transition-all shadow-indigo-600/10">
                            Proses Pembayaran <i class="fa-solid fa-credit-card ml-2 text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Add Address Modal -->
        <div x-show="addressModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <!-- Modal backdrop -->
            <div x-show="addressModalOpen" x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="addressModalOpen = false"></div>

            <!-- Modal Content Panel -->
            <div x-show="addressModalOpen" x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative max-w-lg w-full bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-xl overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Alamat Baru</h3>
                    <button @click="addressModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form @submit.prevent="submitAddress" class="mt-4 flex flex-col gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-500">Label Alamat *</label>
                            <input type="text" x-model="newAddr.label" required placeholder="Rumah / Kantor" class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-500">Nama Penerima *</label>
                            <input type="text" x-model="newAddr.recipient_name" required class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500">Nomor Telepon *</label>
                        <input type="text" x-model="newAddr.phone" required class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-500">Alamat Lengkap *</label>
                        <textarea x-model="newAddr.address" required rows="2" placeholder="Nama jalan, nomor rumah, RT/RW..." class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-500">Provinsi *</label>
                            <select x-model="newAddr.province" @change="loadCities($event.target.value)" required class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500">
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov['province_id'] }}|{{ $prov['province'] }}">{{ $prov['province'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5 relative">
                            <label class="text-xs font-semibold text-slate-500">Kota / Kabupaten *</label>
                            <select x-model="newAddr.city" required :disabled="citiesLoading || newAddrCities.length === 0" class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40 focus:border-indigo-500">
                                <option value="">Pilih Kota</option>
                                <template x-for="city in newAddrCities" :key="city.city_id">
                                    <option :value="city.city_id + '|' + (city.type ? city.type + ' ' + city.city_name : city.city_name)" x-text="city.type ? city.type + ' ' + city.city_name : city.city_name"></option>
                                </template>
                            </select>
                            <div x-show="citiesLoading" class="absolute right-3 top-8"><i class="fa-solid fa-circle-notch animate-spin text-[10px] text-indigo-500"></i></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-500">Kode Pos *</label>
                            <input type="text" x-model="newAddr.postal_code" required class="rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-xs dark:border-slate-800 dark:bg-slate-950/40">
                        </div>
                        <div class="flex items-center gap-2 mt-5">
                            <input type="checkbox" x-model="newAddr.is_default" id="is_default_chk" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-800">
                            <label for="is_default_chk" class="text-xs font-semibold text-slate-500 cursor-pointer">Jadikan Alamat Utama</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800 pt-4 mt-2">
                        <button type="button" @click="addressModalOpen = false" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white hover:bg-indigo-500">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function checkoutApp() {
        return {
            addresses: @json($addresses),
            selectedAddressId: @json($defaultAddress ? $defaultAddress->id : null),
            selectedCourier: '',
            selectedService: '',
            selectedPaymentMethod: '',
            shippingCost: 0,
            subtotal: {{ $summary['subtotal'] }},
            grandTotal: {{ $summary['subtotal'] }},
            notes: '',
            addressModalOpen: false,
            ongkirLoading: false,
            citiesLoading: false,
            shippingServices: [],
            
            // new address form fields
            newAddr: {
                label: '',
                recipient_name: '',
                phone: '',
                address: '',
                province: '',
                city: '',
                postal_code: '',
                is_default: false
            },
            newAddrCities: [],
            
            processRoute: "{{ route('checkout.process') }}",
            ongkirRoute: "{{ route('checkout.ongkir') }}",
            addressStoreRoute: "{{ route('checkout.address.store') }}",
            
            init() {
                if (this.selectedAddressId) {
                    this.selectAddress(this.selectedAddressId);
                }
            },
            
            formatPrice(price) {
                return new Intl.NumberFormat('id-ID').format(price);
            },
            
            selectAddress(id) {
                this.selectedAddressId = id;
                this.selectedCourier = '';
                this.selectedService = '';
                this.shippingCost = 0;
                this.shippingServices = [];
                this.grandTotal = this.subtotal;
            },
            
            selectCourier(courier) {
                this.selectedCourier = courier;
                this.selectedService = '';
                this.shippingCost = 0;
                this.shippingServices = [];
                this.grandTotal = this.subtotal;
                
                this.calculateOngkir();
            },
            
            selectService(srv) {
                this.selectedService = srv.service;
                this.shippingCost = srv.cost;
                this.grandTotal = this.subtotal + srv.cost;
            },
            
            calculateOngkir() {
                if (!this.selectedAddressId || !this.selectedCourier) return;
                
                this.ongkirLoading = true;
                
                axios.post(this.ongkirRoute, {
                    address_id: this.selectedAddressId,
                    courier: this.selectedCourier
                })
                .then(response => {
                    if (response.data.success) {
                        this.shippingServices = response.data.services;
                    }
                })
                .catch(error => {
                    console.error('Ongkir calculate error:', error);
                    const msg = error.response?.data?.message || 'Gagal menghitung ongkos kirim.';
                    window.dispatchEvent(new CustomEvent('flash-message', { detail: { text: msg, type: 'error' } }));
                })
                .finally(() => {
                    this.ongkirLoading = false;
                });
            },
            
            loadCities(provinceVal) {
                if (!provinceVal) {
                    this.newAddrCities = [];
                    this.newAddr.city = '';
                    return;
                }
                
                const provId = provinceVal.split('|')[0];
                this.citiesLoading = true;
                
                axios.get(`/checkout/cities/${provId}`)
                .then(response => {
                    if (response.data.success) {
                        this.newAddrCities = response.data.results;
                    }
                })
                .catch(error => {
                    console.error('Load cities error:', error);
                    const msg = error.response?.data?.message || 'Gagal memuat daftar kota.';
                    window.dispatchEvent(new CustomEvent('flash-message', { detail: { text: msg, type: 'error' } }));
                })
                .finally(() => {
                    this.citiesLoading = false;
                });
            },
            
            submitAddress() {
                axios.post(this.addressStoreRoute, this.newAddr)
                .then(response => {
                    if (response.data.success) {
                        const newAddress = response.data.address;
                        this.addresses.push(newAddress);
                        this.selectAddress(newAddress.id);
                        this.addressModalOpen = false;
                        
                        // Reset form
                        this.newAddr = {
                            label: '',
                            recipient_name: '',
                            phone: '',
                            address: '',
                            province: '',
                            city: '',
                            postal_code: '',
                            is_default: false
                        };
                        this.newAddrCities = [];
                    }
                })
                .catch(error => {
                    console.error('Save address error:', error);
                    const msg = error.response?.data?.message || 'Gagal menyimpan alamat.';
                    window.dispatchEvent(new CustomEvent('flash-message', { detail: { text: msg, type: 'error' } }));
                });
            }
        }
    }
</script>
@endsection
