<div x-data="chatWidget()" class="fixed bottom-6 right-6 z-50 font-sans">
    
    <!-- Chat Button -->
    <button @click="toggle" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-4 shadow-lg flex items-center justify-center transition-transform hover:scale-105 relative">
        <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg x-show="isOpen" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        
        <span x-show="unreadCount > 0" x-text="unreadCount" style="display: none;" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full border-2 border-white animate-bounce"></span>
    </button>

    <!-- Chat Box -->
    <div x-show="isOpen" @click.away="isOpen = false" x-transition.opacity.duration.300ms style="display: none;" class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col h-[500px]">
        
        <!-- Header -->
        <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg">Live Chat</h3>
                <p class="text-xs text-indigo-100">Kami siap membantu Anda</p>
            </div>
        </div>

        <!-- Step 1: Init / Details Form -->
        <div x-show="!sessionId" class="p-5 overflow-y-auto flex-1 bg-gray-50">
            <template x-if="!isAuth">
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 mb-2">Silakan isi data berikut untuk memulai percakapan:</p>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" x-model="form.guest_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Email</label>
                        <input type="email" x-model="form.guest_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">No. WhatsApp</label>
                        <input type="text" x-model="form.guest_whatsapp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </template>
            
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Kategori Bantuan</label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-100" :class="{'border-indigo-500 bg-indigo-50': form.category === 'marketplace'}">
                        <input type="radio" x-model="form.category" value="marketplace" class="text-indigo-600">
                        <span class="text-sm">Marketplace & Produk</span>
                    </label>
                    <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-100" :class="{'border-indigo-500 bg-indigo-50': form.category === 'project'}">
                        <input type="radio" x-model="form.category" value="project" class="text-indigo-600">
                        <span class="text-sm">Jasa Pembuatan Proyek</span>
                    </label>
                    <label class="flex items-center space-x-2 p-2 border rounded cursor-pointer hover:bg-gray-100" :class="{'border-indigo-500 bg-indigo-50': form.category === 'collaboration'}">
                        <input type="radio" x-model="form.category" value="collaboration" class="text-indigo-600">
                        <span class="text-sm">Kolaborasi & Artikel</span>
                    </label>
                </div>
            </div>

            <button @click="startSession" :disabled="loading" class="mt-6 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                <span x-show="!loading">Mulai Percakapan</span>
                <span x-show="loading">Memproses...</span>
            </button>
        </div>

        <!-- Step 2: Chat Interface -->
        <div x-show="sessionId" style="display: none;" class="flex flex-col flex-1 h-full">
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50 space-y-3" id="chat-messages-container">
                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.sender_type === 'admin' ? 'flex justify-start' : (msg.sender_type === 'system' ? 'flex justify-center' : 'flex justify-end')">
                        
                        <template x-if="msg.sender_type === 'system'">
                            <div class="bg-gray-200 text-gray-500 px-3 py-1.5 rounded-full text-[10px] font-medium border border-gray-300">
                                <i class="fa-solid fa-circle-info mr-1"></i> <span x-text="msg.message"></span>
                            </div>
                        </template>

                        <template x-if="msg.sender_type !== 'system'">
                            <div :class="msg.sender_type === 'admin' ? 'bg-white text-gray-800 border border-gray-200' : 'bg-indigo-600 text-white'" class="max-w-[85%] rounded-2xl px-4 py-3 shadow-sm text-sm">
                                <template x-if="msg.product">
                                    <div class="mb-2 bg-gray-50 rounded-xl p-2 border border-gray-200 flex gap-3 items-center">
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 shrink-0 overflow-hidden">
                                            <img :src="msg.product.media && msg.product.media[0] ? msg.product.media[0].original_url : '/placeholder.jpg'" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <h4 class="font-bold text-xs truncate text-gray-800" x-text="msg.product.name"></h4>
                                            <p class="text-xs font-semibold text-indigo-600 mt-0.5" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(msg.product.sale_price ?? msg.product.price)"></p>
                                        </div>
                                        <a :href="'/shop/' + msg.product.slug" target="_blank" class="shrink-0 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 transition px-2 py-1.5 rounded text-[10px] font-bold">
                                            Lihat
                                        </a>
                                    </div>
                                </template>
                                <p x-text="msg.message"></p>
                                <span :class="msg.sender_type === 'admin' ? 'text-gray-400' : 'text-indigo-200'" class="text-[10px] mt-1.5 block text-right" x-text="formatTime(msg.created_at)"></span>
                            </div>
                        </template>
                    </div>
                </template>
                <div x-show="messages.length === 0" class="text-center text-gray-400 text-sm mt-10">
                    Kirim pesan untuk memulai obrolan...
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-3 border-t bg-white relative">
                <template x-if="sessionStatus === 'closed'">
                    <div class="bg-gray-100 text-gray-500 text-center py-2 rounded-lg text-xs border border-gray-200">
                        Sesi obrolan ini telah diakhiri.
                    </div>
                </template>

                <template x-if="sessionStatus !== 'closed'">
                    <div>
                        <!-- Selected Product Preview -->
                        <template x-if="selectedProduct">
                            <div class="mb-3 bg-indigo-50 rounded-lg p-2 border border-indigo-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-box text-indigo-500 text-xs"></i>
                                    <div>
                                        <p class="text-[10px] text-indigo-400">Tanya Produk:</p>
                                        <p class="text-xs font-bold text-indigo-900 truncate max-w-[150px]" x-text="selectedProduct.name"></p>
                                    </div>
                                </div>
                                <button type="button" @click="selectedProduct = null" class="text-indigo-400 hover:text-rose-500 p-1">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </template>

                        <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                            <input type="text" x-model="newMessage" placeholder="Ketik pesan..." class="flex-1 rounded-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2">
                            <button type="submit" :disabled="(!newMessage.trim() && !selectedProduct) || sending" class="bg-indigo-600 text-white rounded-full p-2 hover:bg-indigo-700 disabled:opacity-50">
                                <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>

<script>
    // Global Audio Context to fix iOS/Mobile Autoplay restrictions
    let chatAudioCtx = null;

    function initAudio() {
        if (!chatAudioCtx) {
            chatAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (chatAudioCtx.state === 'suspended') {
            chatAudioCtx.resume();
        }
    }

    function playWidgetSound() {
        if (!chatAudioCtx) return; // Silent if not initialized yet
        try {
            const osc = chatAudioCtx.createOscillator();
            const gainNode = chatAudioCtx.createGain();

            osc.connect(gainNode);
            gainNode.connect(chatAudioCtx.destination);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(800, chatAudioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1200, chatAudioCtx.currentTime + 0.1);

            gainNode.gain.setValueAtTime(0, chatAudioCtx.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.3, chatAudioCtx.currentTime + 0.05);
            gainNode.gain.exponentialRampToValueAtTime(0.001, chatAudioCtx.currentTime + 0.2);

            osc.start(chatAudioCtx.currentTime);
            osc.stop(chatAudioCtx.currentTime + 0.2);
        } catch (e) {
            console.warn('Audio blocked by browser', e);
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('chatWidget', () => ({
            isOpen: false,
            isAuth: {{ auth()->check() ? 'true' : 'false' }},
            sessionId: localStorage.getItem('devgate_chat_session_id') || null,
            form: {
                guest_name: '',
                guest_email: '',
                guest_whatsapp: '',
                category: 'marketplace'
            },
            messages: [],
            newMessage: '',
            loading: false,
            sending: false,
            echoChannel: null,
            unreadCount: 0,
            selectedProduct: null,
            sessionStatus: '',

            init() {
                window.addEventListener('open-chat-product', (e) => {
                    this.handleProductShare(e.detail);
                });

                if (this.sessionId) {
                    this.loadMessages();
                    this.listenToPusher();
                }
            },

            toggle() {
                initAudio(); // Unlock audio on user interaction
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.unreadCount = 0;
                    if (this.sessionId) {
                        setTimeout(() => this.scrollToBottom(), 100);
                    }
                }
            },

            handleProductShare(product) {
                initAudio();
                this.isOpen = true;
                this.selectedProduct = product;
                this.form.category = 'marketplace'; // auto switch to marketplace
                if (this.sessionId) {
                    setTimeout(() => this.scrollToBottom(), 100);
                }
            },

            async startSession() {
                initAudio(); // Unlock audio on user interaction
                if (!this.isAuth) {
                    if (!this.form.guest_name || !this.form.guest_email || !this.form.guest_whatsapp) {
                        alert('Mohon lengkapi data diri Anda.');
                        return;
                    }
                }

                this.loading = true;
                try {
                    const response = await fetch('/chat/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });
                    
                    const data = await response.json();
                    if (data.session && data.session.id) {
                        this.sessionId = data.session.id;
                        localStorage.setItem('devgate_chat_session_id', this.sessionId);
                        this.listenToPusher();
                    } else {
                        alert(data.message || 'Gagal memulai sesi chat');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
                this.loading = false;
            },

            async loadMessages() {
                try {
                    const response = await fetch(`/chat/${this.sessionId}/messages`);
                    if (response.status === 404 || response.status === 403) {
                        // Session expired or invalid
                        this.sessionId = null;
                        localStorage.removeItem('devgate_chat_session_id');
                        return;
                    }
                    const data = await response.json();
                    this.messages = data.messages || [];
                    this.sessionStatus = data.status || '';
                    setTimeout(() => this.scrollToBottom(), 100);
                } catch (e) {
                    console.error('Failed to load messages', e);
                }
            },

            async sendMessage() {
                if ((!this.newMessage.trim() && !this.selectedProduct) || this.sending) return;
                
                const msgText = this.newMessage;
                this.newMessage = '';
                this.sending = true;

                // Optimistic UI update
                const optimisticMsg = {
                    id: Date.now(),
                    message: msgText,
                    product: this.selectedProduct,
                    sender_type: this.isAuth ? 'user' : 'guest',
                    created_at: new Date().toISOString()
                };
                
                const productId = this.selectedProduct ? this.selectedProduct.id : null;
                this.selectedProduct = null;
                
                this.messages.push(optimisticMsg);
                setTimeout(() => this.scrollToBottom(), 10);

                try {
                    const response = await fetch(`/chat/${this.sessionId}/message`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            message: msgText,
                            product_id: productId
                        })
                    });
                    const data = await response.json();
                    
                    // Replace optimistic message with real one
                    const index = this.messages.findIndex(m => m.id === optimisticMsg.id);
                    if (index !== -1) {
                        this.messages[index] = data.message;
                    }
                } catch (e) {
                    console.error(e);
                    alert('Gagal mengirim pesan.');
                }
                this.sending = false;
            },

            listenToPusher() {
                if (!window.Echo) return;
                
                if (this.echoChannel) {
                    window.Echo.leaveChannel('chat.session.' + this.sessionId);
                }
                
                this.echoChannel = window.Echo.channel('chat.session.' + this.sessionId)
                    .listen('.NewChatMessage', (e) => {
                        // Avoid duplicating if we sent it
                        if (e.message.sender_type === 'admin' || e.message.sender_type === 'system') {
                            this.messages.push(e.message);
                            if (e.message.sender_type === 'system' && e.message.message.includes('diakhiri')) {
                                this.sessionStatus = 'closed';
                            }
                            playWidgetSound();
                            if (!this.isOpen) {
                                this.unreadCount++;
                            }
                            setTimeout(() => this.scrollToBottom(), 100);
                        }
                    });
            },

            scrollToBottom() {
                const container = document.getElementById('chat-messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }
        }));
    });
</script>
