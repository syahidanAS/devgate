@extends('layouts.cms')

@section('title', 'Tulis Artikel Baru — DevGate')

@section('styles')
<!-- Quill snow theme CDN -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-toolbar.ql-snow {
        border: 1px solid #1e293b !important;
        background-color: #0b0f19 !important;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 10px 15px;
    }
    .ql-toolbar .ql-stroke {
        stroke: #94a3b8 !important;
    }
    .ql-toolbar .ql-fill {
        fill: #94a3b8 !important;
    }
    .ql-toolbar .ql-picker {
        color: #94a3b8 !important;
    }
    .ql-toolbar .ql-picker-options {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
    }
    .ql-container.ql-snow {
        border: 1px solid #1e293b !important;
        background-color: rgba(2, 6, 23, 0.4) !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        font-family: 'Outfit', sans-serif;
        color: #f8fafc;
        font-size: 0.875rem;
    }
    .ql-editor {
        min-height: 380px;
    }
    .ql-editor.ql-blank::before {
        color: #475569 !important;
        font-style: normal !important;
    }
</style>
@endsection

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="{{ route('cms.articles.index') }}" class="hover:text-white transition-colors">Blog & Artikel</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Tulis Baru</span>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-800/60 pb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-feather-pointed text-indigo-500"></i> Tulis Artikel Baru
            </h1>
            <p class="text-slate-400 text-xs mt-1">Gunakan editor visual untuk menulis, menyematkan gambar terkompresi, dan merilis artikel teknologi.</p>
        </div>
        <a 
            href="{{ route('cms.articles.index') }}" 
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-800 bg-slate-900/50 text-xs font-bold text-slate-400 hover:bg-slate-900 hover:text-white transition-all"
        >
            <i class="fa-solid fa-arrow-left-long"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <form id="article-form" action="{{ route('cms.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Side: Article Editor Details (2 Columns) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Main Details Card -->
                <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl space-y-5">
                    
                    <!-- Article Title -->
                    <div class="space-y-2">
                        <label for="title" class="block text-xs font-bold text-slate-450 uppercase tracking-wider">Judul Artikel</label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            value="{{ old('title') }}" 
                            placeholder="Misal: Tutorial ESP32: Membaca Sensor Suhu DHT22..." 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                            required
                        >
                        @error('title')
                            <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Excerpt (Short Summary) -->
                    <div class="space-y-2">
                        <label for="excerpt" class="block text-xs font-bold text-slate-455 uppercase tracking-wider">Ringkasan Singkat (Excerpt)</label>
                        <textarea 
                            name="excerpt" 
                            id="excerpt" 
                            rows="2" 
                            placeholder="Tulis ringkasan singkat artikel dalam 1-2 kalimat untuk pratinjau di halaman depan..." 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all resize-none"
                            maxlength="500"
                        >{{ old('excerpt') }}</textarea>
                        @error('excerpt')
                            <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rich Text Visual Editor -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-455 uppercase tracking-wider">Konten Artikel</label>
                        
                        <!-- Rich editor container -->
                        <div id="editor"></div>
                        
                        <!-- Hidden text field holding actual rich body code for Laravel -->
                        <input type="hidden" name="body" id="body_input" value="{{ old('body') }}">
                        
                        @error('body')
                            <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Related Shop Components & SEO Meta Card -->
                <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl space-y-5">
                    
                    <!-- Related Components Title -->
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-tags text-indigo-500 text-xs"></i> Hubungkan Komponen Toko (Cross-Promotion)
                        </h3>
                        <p class="text-slate-500 text-[10px] mt-0.5">Sematkan produk komponen dari marketplace yang relevan dengan bahasan artikel ini.</p>
                    </div>

                    <!-- Component Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-48 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-800">
                        @forelse($products as $product)
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-900 bg-slate-950/50 hover:bg-slate-900/40 cursor-pointer select-none transition-all">
                                <input 
                                    type="checkbox" 
                                    name="products[]" 
                                    value="{{ $product->id }}" 
                                    class="rounded border-slate-800 bg-slate-950 text-indigo-500 focus:ring-indigo-500 h-4 w-4"
                                    {{ old('products') && in_array($product->id, old('products')) ? 'checked' : '' }}
                                >
                                <div class="overflow-hidden">
                                    <span class="text-xs font-bold text-slate-300 block truncate">{{ $product->name }}</span>
                                    <span class="text-[9px] text-slate-550 font-mono">Rp {{ number_format($product->price) }}</span>
                                </div>
                            </label>
                        @empty
                            <div class="col-span-2 text-center py-4 text-xs text-slate-600">
                                Belum ada komponen marketplace yang aktif.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-900 pt-4 space-y-4">
                        <!-- SEO Settings Title -->
                        <div>
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-search text-indigo-500 text-xs"></i> Optimasi SEO (Search Engine)
                            </h3>
                            <p class="text-slate-500 text-[10px] mt-0.5">Sesuaikan metadata pencarian Google/Bing untuk artikel ini.</p>
                        </div>

                        <!-- Meta Title -->
                        <div class="space-y-2">
                            <label for="meta_title" class="block text-xs font-bold text-slate-450 uppercase tracking-wider">SEO Meta Title</label>
                            <input 
                                type="text" 
                                name="meta_title" 
                                id="meta_title" 
                                value="{{ old('meta_title') }}" 
                                placeholder="Kosongkan untuk menggunakan judul asli artikel..." 
                                class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                            >
                        </div>

                        <!-- Meta Description -->
                        <div class="space-y-2">
                            <label for="meta_description" class="block text-xs font-bold text-slate-450 uppercase tracking-wider">SEO Meta Description</label>
                            <textarea 
                                name="meta_description" 
                                id="meta_description" 
                                rows="2" 
                                placeholder="Tulis deskripsi penelusuran google dalam 150-160 karakter..." 
                                class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white placeholder-slate-650 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all resize-none"
                                maxlength="500"
                            >{{ old('meta_description') }}</textarea>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Right Side: Publishing Parameters (1 Column) -->
            <div class="space-y-6">
                
                <!-- Settings Card -->
                <div class="rounded-3xl border border-slate-800/80 bg-slate-950/45 p-6 shadow-xl space-y-5">
                    
                    <!-- Publishing status selection -->
                    <div class="space-y-2" x-data="{ status: '{{ old('status', 'draft') }}' }">
                        <label for="status" class="block text-xs font-bold text-slate-450 uppercase tracking-wider">Status Publikasi</label>
                        <select 
                            name="status" 
                            id="status" 
                            x-model="status"
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                            required
                        >
                            <option value="draft">Simpan Draft</option>
                            <option value="published">Publish Sekarang</option>
                            <option value="scheduled">Jadwalkan Rilis</option>
                        </select>

                        <!-- Scheduled calendar, shown only when scheduled status selected -->
                        <div 
                            class="space-y-2 mt-3 pt-3 border-t border-slate-900"
                            x-show="status === 'scheduled'"
                            x-transition
                            x-cloak
                        >
                            <label for="scheduled_at" class="block text-xs font-bold text-slate-450 uppercase tracking-wider">Tanggal & Jam Rilis</label>
                            <input 
                                type="datetime-local" 
                                name="scheduled_at" 
                                id="scheduled_at" 
                                value="{{ old('scheduled_at') }}" 
                                class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                            >
                            @error('scheduled_at')
                                <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category Selector -->
                    <div class="space-y-2">
                        <label for="category_id" class="block text-xs font-bold text-slate-450 uppercase tracking-wider">Kategori Artikel</label>
                        <select 
                            name="category_id" 
                            id="category_id" 
                            class="w-full rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-2.5 text-xs text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
                            required
                        >
                            <option value="" disabled selected>Pilih Kategori...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thumbnail Image Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-450 uppercase tracking-wider">Thumbnail Utama</label>
                        
                        <!-- Dropzone container preview -->
                        <div 
                            class="relative rounded-2xl border border-dashed border-slate-800 bg-slate-950/40 p-4 text-center cursor-pointer hover:border-indigo-500/50 transition-all"
                            x-data="{ preview: null }"
                            @click="$refs.thumbnail_file.click()"
                        >
                            <input 
                                type="file" 
                                name="thumbnail" 
                                x-ref="thumbnail_file"
                                class="hidden" 
                                accept="image/*"
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { preview = e.target.result; };
                                        reader.readAsDataURL(file);
                                    } else {
                                        preview = null;
                                    }
                                "
                            >
                            
                            <!-- State: Upload visual preview -->
                            <template x-if="preview">
                                <div class="relative rounded-xl overflow-hidden max-h-40 border border-slate-800">
                                    <img :src="preview" alt="Preview Thumbnail" class="h-full w-full object-cover">
                                    <button 
                                        type="button" 
                                        @click.stop="preview = null; $refs.thumbnail_file.value = ''"
                                        class="absolute top-2 right-2 h-7 w-7 rounded-full bg-slate-950/80 backdrop-blur text-rose-400 hover:text-white flex items-center justify-center transition-colors text-xs border border-slate-850"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </template>

                            <!-- State: Empty upload placeholder -->
                            <template x-if="!preview">
                                <div class="py-6 space-y-2 text-slate-500">
                                    <i class="fa-regular fa-images text-3xl text-slate-700 block mb-1"></i>
                                    <span class="text-xs font-bold block text-slate-400">Pilih Thumbnail</span>
                                    <span class="text-[9px] text-slate-600 block">Rekomendasi rasio 16:9, maksimal 2MB.</span>
                                </div>
                            </template>
                        </div>
                        @error('thumbnail')
                            <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tags Input (Checkboxes list) -->
                    <div class="space-y-3 pt-3 border-t border-slate-900">
                        <label class="block text-xs font-bold text-slate-450 uppercase tracking-wider">Tag Artikel (Tags)</label>
                        <div class="flex flex-wrap gap-2 max-h-36 overflow-y-auto pr-1">
                            @foreach($tags as $tag)
                                <label class="relative flex items-center select-none cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="tags[]" 
                                        value="{{ $tag->id }}" 
                                        class="peer hidden"
                                        {{ old('tags') && in_array($tag->id, old('tags')) ? 'checked' : '' }}
                                    >
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-800 bg-slate-900/30 text-slate-450 peer-checked:bg-indigo-600/15 peer-checked:border-indigo-500/50 peer-checked:text-indigo-400 hover:border-slate-700 transition-all">
                                        #{{ $tag->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('tags')
                            <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Toggles group -->
                    <div class="space-y-3 pt-3 border-t border-slate-900">
                        
                        <!-- Toggle Allow Comments -->
                        <label class="flex items-center justify-between cursor-pointer select-none">
                            <div class="leading-tight">
                                <span class="text-xs font-bold text-slate-350 block">Izinkan Komentar</span>
                                <span class="text-[9px] text-slate-550">Pembaca dapat mengirimkan tanggapan.</span>
                            </div>
                            <input 
                                type="checkbox" 
                                name="allow_comments" 
                                value="1" 
                                class="rounded border-slate-800 bg-slate-950 text-indigo-500 focus:ring-indigo-500 h-4.5 w-4.5 shadow-sm"
                                {{ old('allow_comments', '1') == '1' ? 'checked' : '' }}
                            >
                        </label>

                        <!-- Toggle Featured -->
                        <label class="flex items-center justify-between cursor-pointer select-none pt-2">
                            <div class="leading-tight">
                                <span class="text-xs font-bold text-slate-350 block">Artikel Unggulan (Featured)</span>
                                <span class="text-[9px] text-slate-550">Tampilkan di carousel header halaman blog.</span>
                            </div>
                            <input 
                                type="checkbox" 
                                name="is_featured" 
                                value="1" 
                                class="rounded border-slate-800 bg-slate-950 text-indigo-500 focus:ring-indigo-500 h-4.5 w-4.5 shadow-sm"
                                {{ old('is_featured') ? 'checked' : '' }}
                            >
                        </label>

                    </div>

                </div>

                <!-- Form Submit buttons -->
                <div class="rounded-3xl border border-slate-800/80 bg-slate-955/20 p-4 shadow-xl flex items-center justify-between gap-3">
                    <a 
                        href="{{ route('cms.articles.index') }}" 
                        class="px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-900/30 text-xs font-bold text-slate-400 hover:bg-slate-900 hover:text-white transition-all"
                    >
                        Batalkan
                    </a>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/25 transition-all"
                    >
                        Simpan Artikel
                    </button>
                </div>

            </div>

        </div>

    </form>

</div>
@endsection

@section('scripts')
<!-- jQuery library from CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Quill JS Library from CDN -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Quill
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Tulis isi konten artikel teknologi / tutorial di sini secara mendalam...',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'blockquote'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image', 'code-block'],
                    ['clean']
                ]
            }
        });

        // Set initial body value if available (e.g. on validation fallback)
        const oldBody = $('#body_input').val();
        if (oldBody) {
            quill.root.innerHTML = oldBody;
        }

        // Override default image handler to perform AJAX WebP compression & upload
        const toolbar = quill.getModule('toolbar');
        toolbar.addHandler('image', function() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = function() {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('image', file);

                    // Show loader text in Quill selection area
                    const range = quill.getSelection(true);
                    quill.insertText(range.index, '[Mengunggah gambar...]', { 'italic': true, 'color': '#6366f1' });

                    $.ajax({
                        url: "{{ route('cms.media.upload') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhrFields: {
                            withCredentials: true
                        },
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                            "Accept": "application/json"
                        },
                        success: function(result) {
                            // Remove loader text
                            quill.deleteText(range.index, 20);

                            if (result.success && result.url) {
                                // Insert final public WebP URL back as an image embed
                                quill.insertEmbed(range.index, 'image', result.url);
                            } else {
                                alert("Gagal mengunggah gambar: " + (result.message || "Terjadi kesalahan"));
                            }
                        },
                        error: function(xhr) {
                            quill.deleteText(range.index, 20);
                            console.error("Error:", xhr);
                            alert("Terjadi kesalahan koneksi saat mengunggah gambar.");
                        }
                    });
                }
            };
        });

        // Handle form submit via jQuery AJAX
        const $form = $('#article-form');
        $form.on('submit', function(e) {
            e.preventDefault();

            // Clear any old validation errors
            $('.ajax-error').remove();
            
            // Sync Quill HTML content to hidden input text field
            const $bodyInput = $('#body_input');
            const editorContent = quill.root.innerHTML;
            
            // Robust check to see if Quill is truly empty (empty editor has length of 1 due to trailing newline)
            const isEditorEmpty = quill.getLength() <= 1 || 
                                  editorContent === '<p><br></p>' || 
                                  editorContent === '<p></p>' || 
                                  editorContent === '';
            
            if (isEditorEmpty) {
                $bodyInput.val('');
            } else {
                $bodyInput.val(editorContent);
            }

            // Disable submit button and show loading state
            const $submitBtn = $form.find('button[type="submit"]');
            const originalBtnHtml = $submitBtn.html();
            $submitBtn.prop('disabled', true);
            $submitBtn.html('<i class="fa-solid fa-spinner animate-spin mr-1.5"></i> Menyimpan...');

            const formData = new FormData(this);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhrFields: {
                    withCredentials: true
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(result) {
                    if (result.success) {
                        // Show premium success toast
                        showToast(result.message || 'Artikel berhasil disimpan!', 'success');
                        
                        // Redirect after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1500);
                    } else {
                        showToast(result.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                        $submitBtn.prop('disabled', false);
                        $submitBtn.html(originalBtnHtml);
                    }
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false);
                    $submitBtn.html(originalBtnHtml);
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMsg = 'Harap periksa isian Anda: <br>';
                        
                        // Map errors to fields
                        for (const [field, messages] of Object.entries(errors)) {
                            errorMsg += `&bull; ${messages[0]}<br>`;
                            
                            // Inject small validation message below the corresponding field
                            let $inputElement = $(`[name="${field}"]`).length ? $(`[name="${field}"]`) : 
                                                $(`[name="${field}[]"]`).length ? $(`[name="${field}[]"]`) :
                                                (field === 'body' ? $('#editor') : null);
                                               
                            if (field === 'thumbnail') {
                                $inputElement = $('[x-ref="thumbnail_file"]').parent();
                            }
                            
                            if ($inputElement && $inputElement.length) {
                                const errP = `<p class="ajax-error text-rose-500 text-[10px] font-semibold mt-1">${messages[0]}</p>`;
                                
                                // Insert error text after input parent or container
                                $inputElement.parent().append(errP);
                            }
                        }
                        
                        showToast(errorMsg, 'error');
                    } else {
                        // General system/connection error
                        console.error("Submission error:", xhr);
                        const responseMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Gagal menghubungi server. Periksa koneksi internet Anda.";
                        showToast(responseMsg, "error");
                    }
                }
            });
        });

        // Dynamic glassmorphic toast notification function
        function showToast(message, type = 'success') {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed top-6 right-6 z-50 space-y-3 max-w-sm w-full';
                document.body.appendChild(toastContainer);
            }
            
            const toast = document.createElement('div');
            if (type === 'success') {
                toast.className = 'flex items-center justify-between rounded-2xl bg-emerald-500/10 border border-emerald-500/30 p-4 text-xs font-semibold text-emerald-450 backdrop-blur-md shadow-lg transform translate-y-2 opacity-0 transition-all duration-300';
                toast.innerHTML = `
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-sm text-emerald-500"></i>
                        <span>${message}</span>
                    </div>
                `;
            } else {
                toast.className = 'flex items-start justify-between rounded-2xl bg-rose-500/10 border border-rose-500/30 p-4 text-xs font-semibold text-rose-455 backdrop-blur-md shadow-lg transform translate-y-2 opacity-0 transition-all duration-300';
                toast.innerHTML = `
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-sm text-rose-550 mt-0.5 animate-bounce"></i>
                        <div>
                            <p class="font-bold text-white mb-0.5">Validasi Gagal</p>
                            <p class="text-[10px] text-rose-350 leading-relaxed">${message}</p>
                        </div>
                    </div>
                `;
            }
            
            toastContainer.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-y-[-10px]', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        }
    });
</script>
@endsection
