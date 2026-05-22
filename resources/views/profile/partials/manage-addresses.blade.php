<section x-data="{
    showAddForm: false,
    editingId: null,
    deleteConfirmId: null,
    form: {
        label: 'Rumah',
        recipient_name: '',
        phone: '',
        address: '',
        city: '',
        province: '',
        postal_code: '',
        is_default: false,
    },
    resetForm() {
        this.form = { label: 'Rumah', recipient_name: '', phone: '', address: '', city: '', province: '', postal_code: '', is_default: false };
        this.showAddForm = false;
        this.editingId = null;
    },
    startEdit(addr) {
        this.form = {
            label: addr.label,
            recipient_name: addr.recipient_name,
            phone: addr.phone,
            address: addr.address,
            city: addr.city,
            province: addr.province,
            postal_code: addr.postal_code,
            is_default: addr.is_default,
        };
        this.editingId = addr.id;
        this.showAddForm = false;
    }
}">

    {{-- Section Header --}}
    <header class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-indigo-500"></i>
                Alamat Pengiriman
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola alamat pengiriman untuk mempercepat proses checkout.
            </p>
        </div>
        <button
            @click="showAddForm = !showAddForm; editingId = null; resetForm(); showAddForm = true"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-500 transition-all active:scale-95"
        >
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Alamat
        </button>
    </header>

    {{-- Add / Edit Form --}}
    <div
        x-show="showAddForm || editingId !== null"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="mt-6 rounded-2xl border border-indigo-200 bg-indigo-50/50 dark:border-indigo-900/50 dark:bg-indigo-950/20 p-5"
    >
        <h3 class="text-sm font-semibold text-indigo-700 dark:text-indigo-400 mb-4 flex items-center gap-2">
            <i x-show="showAddForm" class="fa-solid fa-plus-circle"></i>
            <i x-show="editingId !== null" x-cloak class="fa-solid fa-pen-to-square"></i>
            <span x-text="editingId !== null ? 'Edit Alamat' : 'Tambah Alamat Baru'"></span>
        </h3>

        {{-- ADD FORM --}}
        <form x-show="showAddForm" method="POST" action="{{ route('addresses.store') }}" class="space-y-4">
            @csrf
            @include('profile.partials._address-form-fields', ['formTarget' => 'add'])
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 transition-all active:scale-95 shadow-md shadow-indigo-600/20">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Alamat
                </button>
                <button type="button" @click="resetForm()" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                    Batal
                </button>
            </div>
        </form>

        {{-- EDIT FORMS (one per address) --}}
        @foreach($addresses as $addr)
        <form
            x-show="editingId === {{ $addr->id }}"
            x-cloak
            method="POST"
            action="{{ route('addresses.update', $addr) }}"
            class="space-y-4"
        >
            @csrf
            @method('PATCH')
            @include('profile.partials._address-form-fields', ['address' => $addr, 'formTarget' => 'edit-'.$addr->id])
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 transition-all active:scale-95 shadow-md shadow-indigo-600/20">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <button type="button" @click="resetForm()" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                    Batal
                </button>
            </div>
        </form>
        @endforeach
    </div>

    {{-- Address Cards List --}}
    <div class="mt-6 space-y-3">
        @forelse($addresses as $addr)
        <div class="group relative rounded-2xl border {{ $addr->is_default ? 'border-indigo-300 bg-indigo-50/60 dark:border-indigo-800 dark:bg-indigo-950/30' : 'border-slate-200 bg-white dark:border-slate-700/60 dark:bg-slate-800/40' }} p-5 transition-all hover:shadow-md">

            {{-- Top row: label + badge + actions --}}
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 rounded-lg {{ $addr->is_default ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }} px-2.5 py-1 text-xs font-semibold">
                        <i class="fa-solid {{ $addr->label === 'Kantor' ? 'fa-building' : ($addr->label === 'Kost' ? 'fa-house-chimney-user' : 'fa-house') }} text-[10px]"></i>
                        {{ $addr->label }}
                    </span>
                    @if($addr->is_default)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 px-2.5 py-0.5 text-[11px] font-semibold">
                            <i class="fa-solid fa-circle-check text-[9px]"></i> Utama
                        </span>
                    @endif
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center gap-1 shrink-0">
                    @if(!$addr->is_default)
                    <form method="POST" action="{{ route('addresses.setDefault', $addr) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="Jadikan utama" class="rounded-lg p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-all text-xs">
                            <i class="fa-regular fa-star"></i>
                        </button>
                    </form>
                    @endif

                    <button
                        @click="startEdit({{ json_encode(['id' => $addr->id, 'label' => $addr->label, 'recipient_name' => $addr->recipient_name, 'phone' => $addr->phone, 'address' => $addr->address, 'city' => $addr->city, 'province' => $addr->province, 'postal_code' => $addr->postal_code, 'is_default' => $addr->is_default]) }})"
                        title="Edit alamat"
                        class="rounded-lg p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-all text-xs"
                    >
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>

                    <button
                        @click="deleteConfirmId = {{ $addr->id }}"
                        title="Hapus alamat"
                        class="rounded-lg p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-all text-xs"
                    >
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- Address Details --}}
            <div class="mt-3 space-y-1">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $addr->recipient_name }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-phone text-[10px]"></i>
                    {{ $addr->phone }}
                </p>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    {{ $addr->address }},
                    {{ $addr->city }},
                    {{ $addr->province }},
                    {{ $addr->postal_code }}
                </p>
            </div>

            {{-- Delete Confirmation Inline --}}
            <div
                x-show="deleteConfirmId === {{ $addr->id }}"
                x-cloak
                x-transition
                class="mt-4 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 dark:border-rose-900/40 dark:bg-rose-950/30 p-3"
            >
                <i class="fa-solid fa-triangle-exclamation text-rose-500 shrink-0"></i>
                <p class="text-xs text-rose-700 dark:text-rose-400 flex-grow">Hapus alamat ini secara permanen?</p>
                <form method="POST" action="{{ route('addresses.destroy', $addr) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500 transition-all">
                        Hapus
                    </button>
                </form>
                <button @click="deleteConfirmId = null" class="text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                    Batal
                </button>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 py-12 text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                <i class="fa-solid fa-location-dot text-2xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Belum ada alamat pengiriman</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Tambahkan alamat untuk mempercepat checkout</p>
            <button
                @click="showAddForm = true; $nextTick(() => $el.closest('section').scrollIntoView({behavior: 'smooth'}))"
                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-all"
            >
                <i class="fa-solid fa-plus text-xs"></i> Tambah Alamat Pertama
            </button>
        </div>
        @endforelse
    </div>

</section>
