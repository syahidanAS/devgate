@extends('layouts.cms')

@section('title', 'Manajemen Live Chat — DevGate')

@section('breadcrumbs')
<div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
    <span>Portal CMS</span>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-white">Live Chat</span>
</div>
@endsection

@section('content')
<div class="h-[calc(100vh-140px)] flex bg-slate-950 rounded-3xl border border-slate-800 overflow-hidden shadow-2xl relative" x-data="adminChat()">
    
    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="absolute bottom-6 right-6 z-50 bg-indigo-600 text-white px-5 py-4 rounded-2xl shadow-xl border border-indigo-500/50 max-w-sm flex items-start gap-3"
         style="display: none;">
         <div class="mt-1 flex-shrink-0 text-indigo-200">
             <i class="fa-solid fa-bell"></i>
         </div>
         <div>
             <h4 class="font-bold text-sm" x-text="toast.title"></h4>
             <p class="text-xs text-indigo-100 mt-1 line-clamp-2" x-text="toast.message"></p>
         </div>
         <button @click="toast.show = false" class="ml-2 text-indigo-200 hover:text-white">
             <i class="fa-solid fa-xmark"></i>
         </button>
    </div>

    <!-- Sidebar: Session List -->
    <div class="w-1/3 border-r border-slate-800 flex flex-col bg-slate-900/50">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-white font-bold text-lg"><i class="fa-solid fa-comments text-indigo-500 mr-2"></i> Daftar Pesan</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            @forelse($sessions as $session)
                <button 
                    @click="openSession({{ $session->id }}, '{{ $session->guest_name ?? ($session->user ? $session->user->name : 'Unknown') }}', '{{ strtoupper($session->category) }}')"
                    class="w-full text-left p-4 rounded-2xl border transition-all duration-200"
                    :class="activeSessionId === {{ $session->id }} ? 'bg-indigo-600/20 border-indigo-500/50' : 'bg-slate-950 border-slate-800 hover:bg-slate-800'"
                >
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-white font-bold text-sm truncate">
                            {{ $session->guest_name ?? ($session->user ? $session->user->name : 'User') }}
                        </span>
                        <span class="text-[9px] px-2 py-0.5 rounded-full uppercase tracking-wider font-bold
                            @if($session->category === 'marketplace') bg-cyan-500/20 text-cyan-400 border border-cyan-500/30
                            @elseif($session->category === 'project') bg-violet-500/20 text-violet-400 border border-violet-500/30
                            @else bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                            @endif">
                            {{ $session->category }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-400 truncate">
                        {{ $session->messages->last()?->message ?? 'Belum ada pesan' }}
                    </div>
                </button>
            @empty
                <div class="text-center text-slate-500 text-sm mt-10">
                    Tidak ada chat tersedia.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Content: Active Chat -->
    <div class="flex-1 flex flex-col relative bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-slate-950">
        
        <template x-if="!activeSessionId">
            <div class="flex-1 flex flex-col items-center justify-center text-slate-500">
                <i class="fa-regular fa-message text-6xl mb-4 opacity-50"></i>
                <p>Pilih obrolan dari daftar untuk mulai membalas.</p>
            </div>
        </template>

        <template x-if="activeSessionId">
            <div class="flex flex-col h-full w-full">
                <!-- Header -->
                <div class="h-16 border-b border-slate-800 bg-slate-900/80 backdrop-blur-md px-6 flex items-center justify-between z-10 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/30">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-sm" x-text="activeSessionName"></h3>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest" x-text="'Kategori: ' + activeSessionCategory"></p>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4" id="admin-chat-messages">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_type === 'admin' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.sender_type === 'admin' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-200 border border-slate-700'" class="max-w-[70%] rounded-2xl px-5 py-3 shadow-lg text-sm leading-relaxed">
                                <p x-text="msg.message"></p>
                                <span class="text-[10px] opacity-60 mt-2 block" x-text="formatTime(msg.created_at)"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="loading" class="text-center text-slate-500 text-sm">Memuat pesan...</div>
                </div>

                <!-- Input Area -->
                <div class="p-4 border-t border-slate-800 bg-slate-900/80 backdrop-blur-md shrink-0">
                    <form @submit.prevent="sendMessage" class="flex gap-3">
                        <input type="text" x-model="newMessage" placeholder="Ketik balasan..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-600">
                        <button type="submit" :disabled="!newMessage.trim() || sending" class="bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl px-6 font-bold flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-paper-plane mr-2" x-show="!sending"></i>
                            <i class="fa-solid fa-spinner fa-spin mr-2" x-show="sending" style="display: none;"></i>
                            Kirim
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>

@section('scripts')
<script>
    function playChatSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gainNode = ctx.createGain();

            osc.connect(gainNode);
            gainNode.connect(ctx.destination);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(600, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.1);

            gainNode.gain.setValueAtTime(0, ctx.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 0.05);
            gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);

            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.3);
        } catch (e) {
            console.warn('Audio play failed', e);
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('adminChat', () => ({
            activeSessionId: null,
            activeSessionName: '',
            activeSessionCategory: '',
            messages: [],
            newMessage: '',
            loading: false,
            sending: false,
            echoChannel: null,
            globalChannels: [],
            toast: { show: false, title: '', message: '' },
            toastTimeout: null,

            init() {
                this.listenToGlobalCategories();
            },

            showToast(title, message) {
                this.toast.title = title;
                this.toast.message = message;
                this.toast.show = true;
                playChatSound();
                if (this.toastTimeout) clearTimeout(this.toastTimeout);
                this.toastTimeout = setTimeout(() => {
                    this.toast.show = false;
                }, 4000);
            },

            listenToGlobalCategories() {
                if (!window.Echo) return;
                
                // Get categories from PHP side using session data rendering
                const categories = [];
                @if(Auth::user()->hasRole('superadmin'))
                    categories.push('marketplace', 'project', 'collaboration');
                @else
                    @if(Auth::user()->hasRole('admin-marketplace')) categories.push('marketplace'); @endif
                    @if(Auth::user()->hasRole('author')) categories.push('collaboration'); @endif
                @endif

                categories.forEach(cat => {
                    const channel = window.Echo.channel('admin.chat.' + cat)
                        .listen('.NewChatMessage', (e) => {
                            if (e.message.sender_type !== 'admin') {
                                // Refresh session list if needed (basic implementation)
                                // Only show pop up if we are not actively viewing this exact session
                                if (this.activeSessionId !== e.message.chat_session_id) {
                                    this.showToast('Pesan Baru: ' + cat.toUpperCase(), e.message.message);
                                }
                            }
                        });
                    this.globalChannels.push(channel);
                });
            },

            async openSession(id, name, category) {
                if (this.activeSessionId === id) return;
                
                if (this.echoChannel) {
                    window.Echo.leaveChannel('chat.session.' + this.activeSessionId);
                }

                this.activeSessionId = id;
                this.activeSessionName = name;
                this.activeSessionCategory = category;
                this.messages = [];
                this.loading = true;

                try {
                    const response = await fetch(`/cms/chats/${id}/messages`);
                    const data = await response.json();
                    this.messages = data.messages || [];
                    setTimeout(() => this.scrollToBottom(), 50);
                    
                    this.listenToPusher();
                } catch (e) {
                    console.error('Failed to fetch messages', e);
                }
                this.loading = false;
            },

            async sendMessage() {
                if (!this.newMessage.trim() || this.sending) return;

                const text = this.newMessage;
                this.newMessage = '';
                this.sending = true;

                // Optimistic Update
                const optimisticMsg = {
                    id: 'temp-' + Date.now(),
                    message: text,
                    sender_type: 'admin',
                    created_at: new Date().toISOString()
                };
                this.messages.push(optimisticMsg);
                setTimeout(() => this.scrollToBottom(), 10);

                try {
                    const response = await fetch(`/cms/chats/${this.activeSessionId}/reply`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: text })
                    });
                    
                    const data = await response.json();
                    const index = this.messages.findIndex(m => m.id === optimisticMsg.id);
                    if (index !== -1) {
                        this.messages[index] = data.message;
                    }
                } catch (e) {
                    console.error('Failed to send reply', e);
                    alert('Gagal mengirim pesan.');
                }
                this.sending = false;
            },

            listenToPusher() {
                if (!window.Echo) return;
                
                this.echoChannel = window.Echo.channel('chat.session.' + this.activeSessionId)
                    .listen('.NewChatMessage', (e) => {
                        if (e.message.sender_type !== 'admin') {
                            this.messages.push(e.message);
                            setTimeout(() => this.scrollToBottom(), 50);
                            playChatSound(); // Play sound even if active
                        }
                    });
            },

            scrollToBottom() {
                const el = document.getElementById('admin-chat-messages');
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }
        }));
    });
</script>
@endsection
@endsection
