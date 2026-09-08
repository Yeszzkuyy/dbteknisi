<x-app-layout>
<div class="flex flex-col h-[calc(100vh-8rem)]">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4 flex-shrink-0">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">WhatsApp Center</h1>
            <p class="text-slate-500 mt-1 text-sm">Inbox terpadu 4 nomor WA Company — balas & konversi pesan jadi Lead</p>
        </div>
        @if(auth()->user()->can('manage-marketing') && !app()->isProduction())
            <button type="button" @click="simulateMessage()"
                    class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium transition">
                + Simulasi Pesan Masuk
            </button>
        @endif
    </div>

    <div x-data="whatsappCenter()" x-init="init()" class="flex flex-col flex-1 min-h-0" x-cloak>

        {{-- Tabs WA Company --}}
        <div class="flex gap-2 mb-4 flex-shrink-0 flex-wrap">
            <template x-for="acc in accounts" :key="acc.id">
                <button type="button"
                        @click="setAccount(acc)"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2"
                        :class="acc.id === activeAccount.id
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700'">
                    <span x-text="acc.label"></span>
                    <span x-show="unread[acc.account_code] > 0"
                          x-text="unread[acc.account_code]"
                          class="min-w-5 h-5 px-1.5 rounded-full text-xs font-bold flex items-center justify-center"
                          :class="acc.id === activeAccount.id ? 'bg-white text-blue-600' : 'bg-red-500 text-white'"></span>
                </button>
            </template>
        </div>

        {{-- Split View --}}
        <div class="flex flex-1 min-h-0 gap-4">

            {{-- Kiri: daftar percakapan --}}
            <div class="w-80 lg:w-96 flex-shrink-0 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl shadow-sm flex flex-col min-h-0">
                <div class="p-3 border-b border-slate-100 dark:border-slate-700">
                    <input type="text" x-model="search" placeholder="Cari percakapan..."
                           class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
                    <template x-if="filteredConversations.length === 0">
                        <div class="p-6 text-center text-sm text-slate-400">Belum ada percakapan</div>
                    </template>
                    <template x-for="conv in filteredConversations" :key="conv.sender_number">
                        <button type="button" @click="openConversation(conv)"
                                class="w-full text-left px-4 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-700"
                                :class="activeSender === conv.sender_number ? 'bg-blue-50 dark:bg-slate-700' : ''">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                                     :class="conv.customer ? 'bg-blue-600' : 'bg-slate-400'">
                                    <span x-text="(conv.sender_name || '?').charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-medium text-sm text-slate-800 dark:text-slate-100 truncate" x-text="conv.sender_name"></span>
                                        <span class="text-xs text-slate-400 flex-shrink-0" x-text="timeLabel(conv.last_at)"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 mt-0.5">
                                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="conv.last_message"></span>
                                        <span x-show="conv.unread > 0" x-text="conv.unread"
                                              class="min-w-5 h-5 px-1.5 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0"></span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <span x-show="conv.lead_id"
                                              class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Lead</span>
                                        <span x-show="!conv.lead_id && conv.customer"
                                              class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">Klien Existing</span>
                                        <span x-show="!conv.lead_id && !conv.customer"
                                              class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">Kontak Baru</span>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Kanan: chat window --}}
            <div class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl shadow-sm flex flex-col min-h-0">
                <template x-if="!activeConv">
                    <div class="flex-1 flex items-center justify-center text-slate-400">
                        <div class="text-center">
                            <div class="text-4xl mb-2">💬</div>
                            <p class="text-sm">Pilih percakapan untuk mulai membalas</p>
                        </div>
                    </div>
                </template>

                <template x-if="activeConv">
                    <div class="flex flex-col flex-1 min-h-0">
                        {{-- Header chat --}}
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3 flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                <span x-text="(activeConv.sender_name || '?').charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-medium text-slate-800 dark:text-slate-100 truncate" x-text="activeConv.sender_name"></div>
                                <div class="text-xs text-slate-400" x-text="activeConv.sender_number"></div>
                            </div>
                            <template x-if="activeConv.customer">
                                <a :href="'{{ route('customers.index') }}' + '/' + activeConv.customer.id"
                                   class="px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium transition flex-shrink-0">
                                    Lihat Customer
                                </a>
                            </template>
                            <template x-if="activeConv.lead_id">
                                <a :href="'{{ route('leads.index') }}' + '/' + activeConv.lead_id"
                                   class="px-3 py-2 rounded-xl bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium transition flex-shrink-0">
                                    Lihat Lead
                                </a>
                            </template>
                            @can('manage-marketing')
                                <button type="button" @click="openConvertModal()"
                                        class="px-3 py-2 rounded-xl bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium transition flex-shrink-0">
                                    ➕ Jadikan Lead
                                </button>
                            @endcan
                        </div>

                        {{-- Area pesan --}}
                        <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="messageArea">
                            <template x-for="msg in messages" :key="msg.id">
                                <div class="flex" :class="msg.direction === 'outbound' ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm shadow-sm"
                                         :class="msg.direction === 'outbound'
                                            ? 'bg-blue-600 text-white rounded-br-md'
                                            : 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-bl-md'">
                                        <p class="whitespace-pre-wrap break-words" x-text="msg.message_body"></p>
                                        <p class="text-[10px] mt-1 opacity-70" x-text="msg.created_at"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Kotak balas --}}
                        <div class="p-3 border-t border-slate-100 dark:border-slate-700 flex items-end gap-2 flex-shrink-0">
                            <textarea x-model="reply" rows="2" placeholder="Ketik balasan..."
                                      class="flex-1 px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            <button type="button" @click="sendReply()" :disabled="!reply.trim()"
                                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition disabled:opacity-50">
                                Kirim
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Modal Konversi Lead --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="modalOpen = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1">Jadikan Lead</h3>
            <p class="text-sm text-slate-500 mb-4">
                Konversi kontak <span class="font-medium text-slate-700 dark:text-slate-200" x-text="activeConv?.sender_name"></span>
                menjadi Lead baru di sistem.
            </p>
            <form @submit.prevent="convertLead($el)">
                @csrf
                <input type="hidden" name="sender_number" :value="activeConv?.sender_number">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Nama Kontak</label>
                        <input type="text" name="customer_name" required x-model="convertName"
                               class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Segment</label>
                        <select name="segment" required
                                class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="end_user">End User</option>
                            <option value="vendor">Vendor</option>
                            <option value="system_integrator">System Integrator</option>
                            <option value="kontraktor">Kontraktor</option>
                            <option value="gov">Government</option>
                            <option value="principle">Principle</option>
                            <option value="distributor">Distributor</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">Kebutuhan</label>
                        <textarea name="kebutuhan" rows="3" placeholder="Deskripsi kebutuhan dari chat..."
                                  class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-5">
                    <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 font-medium transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-green-100 hover:bg-green-200 text-green-700 font-medium transition">
                        Simpan Lead
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function whatsappCenter() {
        return {
            accounts: @json($accountTabs),
            activeAccount: null,
            activeConv: null,
            activeSender: null,
            conversations: [],
            messages: [],
            reply: '',
            search: '',
            unread: {},
            convertName: '',
            modalOpen: false,
            timer: null,

            init() {
                if (this.accounts.length === 0) return;
                this.activeAccount = this.accounts[0];
                this.loadConversations();
                this.refreshStatus();
                this.timer = setInterval(() => {
                    this.refreshStatus();
                    this.loadConversations();
                    if (this.activeConv) this.loadMessages(this.activeConv.sender_number, true);
                }, 5000);
            },

            setAccount(acc) {
                if (this.activeAccount?.id === acc.id) return;
                this.activeAccount = acc;
                this.activeConv = null;
                this.activeSender = null;
                this.messages = [];
                this.loadConversations();
                this.refreshStatus();
            },

            get filteredConversations() {
                const q = (this.search || '').toLowerCase();
                return this.conversations.filter(c =>
                    !q || (c.sender_name || '').toLowerCase().includes(q) || c.sender_number.includes(q)
                );
            },

            async refreshStatus() {
                try {
                    const res = await fetch('{{ route('whatsapp-center.status') }}');
                    const data = await res.json();
                    this.unread = Object.fromEntries(data.map(d => [d.account_code, d.unread]));
                } catch (e) {}
            },

            async loadConversations() {
                if (!this.activeAccount) return;
                try {
                    const res = await fetch('{{ route('whatsapp-center.conversations', ['account' => ':id']) }}'.replace(':id', this.activeAccount.id));
                    this.conversations = await res.json();
                } catch (e) {}
            },

            openConversation(conv) {
                this.activeConv = conv;
                this.activeSender = conv.sender_number;
                this.loadMessages(conv.sender_number);
            },

            async loadMessages(sender, silent = false) {
                try {
                    const res = await fetch('{{ route('whatsapp-center.messages', ['account' => ':id', 'sender' => ':sender']) }}'
                        .replace(':id', this.activeAccount.id).replace(':sender', sender));
                    const data = await res.json();
                    this.messages = data.messages;
                    if (this.activeConv && data.customer) this.activeConv.customer = data.customer;
                    this.$nextTick(() => this.scrollToBottom());
                } catch (e) {}
            },

            async sendReply() {
                if (!this.reply.trim() || !this.activeConv) return;
                try {
                    const res = await fetch('{{ route('whatsapp-center.reply', ['account' => ':id', 'sender' => ':sender']) }}'
                        .replace(':id', this.activeAccount.id).replace(':sender', this.activeConv.sender_number), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ message_body: this.reply.trim() }),
                    });
                    const msg = await res.json();
                    this.messages.push(msg);
                    this.reply = '';
                    this.$nextTick(() => this.scrollToBottom());
                    this.loadConversations();
                    this.refreshStatus();
                } catch (e) {}
            },

            openConvertModal() {
                if (!this.activeConv) return;
                this.convertName = this.activeConv.sender_name;
                this.modalOpen = true;
            },

            async convertLead(form) {
                if (!this.activeConv) return;
                const fd = new FormData(form);
                try {
                    const res = await fetch('{{ route('whatsapp-center.convert', ['account' => ':id', 'sender' => ':sender']) }}'
                        .replace(':id', this.activeAccount.id).replace(':sender', this.activeConv.sender_number), {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: fd,
                    });
                    const data = await res.json();
                    if (data.redirect) {
                        form.reset();
                        this.modalOpen = false;
                        window.location.href = data.redirect;
                    }
                } catch (e) {}
            },

            async simulateMessage() {
                if (!this.activeAccount) return;
                const demo = [
                    ['Rina Putri', '6281234500001', 'Halo, apakah ada paket internet untuk kantor kecil?'],
                    ['Budi Santoso', '6281234500002', 'Minta penawaran wifi untuk 2 ruko dong'],
                    ['Sari Dewi', '6281234500003', 'Butuh quote instalasi jaringan 1 kantor'],
                    ['Andi Wijaya', '6281234500004', 'Bisa survey minggu depan?'],
                ];
                const [name, number, body] = demo[Math.floor(Math.random() * demo.length)];
                try {
                    await fetch('{{ route('whatsapp-center.simulate', ['account' => ':id']) }}'.replace(':id', this.activeAccount.id), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ sender_number: number, sender_name: name, message_body: body }),
                    });
                    this.loadConversations();
                    this.refreshStatus();
                } catch (e) {}
            },

            scrollToBottom() {
                const el = this.$refs.messageArea;
                if (el) el.scrollTop = el.scrollHeight;
            },

            timeLabel(iso) {
                if (!iso) return '';
                const d = new Date(iso);
                const now = new Date();
                if (d.toDateString() === now.toDateString()) {
                    return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                }
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            },
        };
    }
</script>
</x-app-layout>