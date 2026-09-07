<x-app-layout>
    @php
        $chatInit = [
            'conversationId' => $conversation?->id,
            'messages' => $messages,
        ];
    @endphp
    <script>window.chatInit = @json($chatInit);</script>

    <div class="ai-page" x-data="officeAssistant()">
        {{-- Header halaman AI --}}
        <header class="ai-header">
            <span class="ai-header-icon"><x-icon name="message" class="h-5 w-5" /></span>
            <div class="min-w-0">
                <h2 class="ai-header-title">Office Assistant</h2>
                <p class="ai-header-sub">AI Assistant · data realtime dari sistem</p>
            </div>
        </header>

        {{-- Area percakapan --}}
        <div class="ai-conversation" x-ref="messages" @scroll="onScroll">
            {{-- Empty state --}}
            <div x-show="messages.length === 0" class="ai-empty" x-cloak>
                <span class="ai-empty-icon">✦</span>
                <p class="ai-empty-title">Office Assistant</p>
                <p class="ai-empty-desc">Ada yang bisa saya bantu?</p>
                <p class="ai-empty-hint">Tanyakan tentang project, tugas, dokumen, atau data perusahaan.</p>
            </div>

            <template x-for="msg in messages" :key="msg.id">
                <div>
                    {{-- Pesan AI: transparan, ikon di kiri --}}
                    <div x-show="msg.role === 'assistant'" class="ai-row" x-cloak>
                        <span class="ai-row-icon"><x-icon name="message" class="h-4 w-4" /></span>
                        <div class="ai-content">
                            <div class="md" x-html="renderMarkdown(msg.content)"></div>
                            <div class="ai-sources" x-show="msg.sources && msg.sources.length" x-cloak>
                                <template x-for="src in msg.sources" :key="src.name">
                                    <span class="source-chip">📄 <span x-text="src.name"></span><span x-show="src.category" x-text="' · '+src.category"></span></span>
                                </template>
                            </div>
                        </div>
                    </div>
                    {{-- Pesan user: kompak, rata kanan --}}
                    <div x-show="msg.role === 'user'" class="user-row" x-cloak>
                        <div class="user-msg" x-text="msg.content"></div>
                    </div>
                </div>
            </template>

            {{-- Loading state --}}
            <div x-show="busy" class="ai-row" x-cloak>
                <span class="ai-row-icon"><x-icon name="message" class="h-4 w-4" /></span>
                <div class="typing" role="status" aria-label="AI sedang berpikir">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Error state --}}
            <div x-show="error" class="ai-error" x-cloak>
                <p x-text="error"></p>
                <button type="button" @click="error = ''" class="ai-error-close" aria-label="Tutup pesan kesalahan">✕</button>
            </div>
        </div>

        {{-- Tombol kembali ke bawah --}}
        <button type="button" class="ai-jump" x-show="!atBottom" @click="scrollToBottom(true)" x-cloak aria-label="Kembali ke pesan terbaru">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7-7-7m14-8l-7 7-7-7" />
            </svg>
        </button>

        {{-- Composer --}}
        <form class="ai-composer" @submit.prevent="send">
            <textarea x-model="draft" x-ref="input" rows="1" placeholder="Tulis pesan…"
                      aria-label="Tulis pesan untuk Office Assistant"
                      @input="resize"
                      @keydown.enter="onEnter($event)"></textarea>
            <button type="submit" class="ai-send" :disabled="busy" aria-label="Kirim pesan" x-bind:title="busy ? 'Mengirim…' : 'Kirim'">
                <svg x-show="!busy" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                    <path d="M3.4 20.6 21.5 12 3.4 3.4 3.4 10l12.9 2L3.4 14z" />
                </svg>
                <svg x-show="busy" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>
        </form>
    </div>

    <style>
        /* ===== Full-screen: hanya aktif di halaman AI (style ini dimuat hanya di view ini) ===== */
        .main-content{height:100dvh;overflow:hidden}
        .main-content main{display:flex;flex-direction:column;min-height:0;padding-bottom:0!important}
        .ai-page{position:relative;height:100%;width:100%;max-width:850px;margin:0 auto;display:flex;flex-direction:column;min-height:0;padding:0 clamp(1rem,4vw,2rem)}

        /* ===== Header halaman AI ===== */
        .ai-header{position:sticky;top:0;z-index:20;display:flex;align-items:center;gap:.75rem;padding:.9rem 0;margin-bottom:.5rem;border-bottom:1px solid var(--card-border);background:var(--card-bg)}
        .ai-header-icon{display:inline-flex;align-items:center;justify-content:center;width:2.4rem;height:2.4rem;flex:none;border-radius:.85rem;color:#fff;background:linear-gradient(135deg,#2563eb,#7c3aed);box-shadow:0 6px 16px -8px rgba(99,102,241,.55)}
        .ai-header-title{font-size:1rem;font-weight:700;color:var(--text-primary);line-height:1.2}
        .ai-header-sub{font-size:.75rem;color:var(--text-muted);line-height:1.3}

        /* ===== Area percakapan ===== */
        .ai-conversation{flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;display:flex;flex-direction:column;gap:1.25rem;padding:1rem .25rem 1.25rem}
        .ai-row{display:flex;gap:.75rem;align-items:flex-start}
        .ai-row-icon{display:inline-flex;align-items:center;justify-content:center;width:1.75rem;height:1.75rem;flex:none;border-radius:.625rem;color:#fff;background:linear-gradient(135deg,#2563eb,#7c3aed);margin-top:.1rem}
        .ai-content{flex:1;min-width:0}
        .ai-content .md{line-height:1.6;color:var(--text-primary);font-size:.925rem;text-align:left}

        /* ===== Pesan user ===== */
        .user-row{display:flex;justify-content:flex-end}
        .user-msg{max-width:75%;background:#eef2ff;color:#1e293b;border-radius:16px 16px 4px 16px;padding:.6rem 1rem;line-height:1.55;font-size:.925rem;text-align:left;white-space:pre-wrap;overflow-wrap:anywhere;word-break:break-word;border:1px solid #e0e7ff}
        .dark .user-msg{background:#2D3748;color:#f1f5f9;border-color:#374151}

        /* ===== Sumber KB ===== */
        .ai-sources{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.6rem}
        .source-chip{display:inline-flex;align-items:center;gap:.3rem;border:1px solid var(--card-border);background:var(--card-bg-hover);color:var(--text-secondary);border-radius:.5rem;padding:.2rem .6rem;font-size:.75rem}

        /* ===== Empty state ===== */
        .ai-empty{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem 1rem;gap:.35rem}
        .ai-empty-icon{font-size:1.6rem;color:var(--text-muted)}
        .ai-empty-title{font-size:1.05rem;font-weight:700;color:var(--text-primary)}
        .ai-empty-desc{font-size:.95rem;color:var(--text-secondary)}
        .ai-empty-hint{font-size:.8rem;color:var(--text-muted);max-width:30rem}

        /* ===== Loading & error ===== */
        .typing{display:inline-flex;gap:.3rem;align-items:center;padding:.65rem 1rem;border-radius:1rem;background:var(--card-bg-hover)}
        .typing span{width:.4rem;height:.4rem;border-radius:50%;background:var(--text-muted);animation:typing 1.2s infinite ease-in-out}
        .typing span:nth-child(2){animation-delay:.15s}
        .typing span:nth-child(3){animation-delay:.3s}
        @keyframes typing{0%,60%,100%{transform:translateY(0);opacity:.5}30%{transform:translateY(-4px);opacity:1}}
        .ai-error{display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;border:1px solid rgba(220,38,38,.3);background:rgba(254,226,226,.85);color:#b91c1c;border-radius:.75rem;padding:.6rem .85rem;font-size:.85rem}
        .dark .ai-error{background:rgba(127,29,29,.5);color:#fecaca;border-color:rgba(248,113,113,.35)}
        .ai-error-close{flex:none;background:none;border:none;color:inherit;cursor:pointer;font-size:.85rem;padding:.1rem .3rem;border-radius:.375rem}
        .ai-error-close:hover{background:rgba(0,0,0,.06)}

        /* ===== Tombol kembali ke bawah ===== */
        .ai-jump{position:absolute;right:clamp(.5rem,3vw,2rem);bottom:7rem;display:inline-flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;border-radius:9999px;color:var(--text-secondary);background:var(--card-bg);border:1px solid var(--card-border);box-shadow:var(--shadow-lift);cursor:pointer;transition:transform .1s,background-color .15s}
        .ai-jump:hover{background:var(--card-bg-hover)}
        .ai-jump:active{transform:scale(.94)}

        /* ===== Composer ===== */
        .ai-composer{position:relative;display:flex;align-items:flex-end;gap:.6rem;padding:.55rem;margin-bottom:clamp(.75rem,2vh,1.5rem);border-radius:24px;background:var(--card-bg);border:1px solid var(--card-border);box-shadow:var(--shadow-lift)}
        .ai-composer:focus-within{border-color:rgba(59,130,246,.6);box-shadow:var(--shadow-lift),0 0 0 3px rgba(59,130,246,.15)}
        .ai-composer textarea{flex:1;resize:none;background:transparent!important;border:none!important;outline:none!important;box-shadow:none!important;color:var(--input-text)!important;font-size:.925rem;line-height:1.5;padding:.45rem .65rem;max-height:180px;overflow-y:auto}
        .ai-composer textarea::placeholder{color:var(--text-muted)}
        .ai-send{flex:none;display:inline-flex;align-items:center;justify-content:center;width:2.7rem;height:2.7rem;border-radius:9999px;background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;transition:opacity .15s,transform .1s}
        .ai-send:hover:not(:disabled){opacity:.92}
        .ai-send:active:not(:disabled){transform:scale(.94)}
        .ai-send:disabled{opacity:.5;cursor:not-allowed}

        /* ===== Markdown ===== */
        .md>:first-child{margin-top:0}.md>:last-child{margin-bottom:0}
        .md p{margin:.5rem 0}
        .md ul,.md ol{margin:.5rem 0;padding-left:1.4rem;display:flex;flex-direction:column;gap:.25rem}
        .md ul{list-style:disc}.md ol{list-style:decimal}
        .md li>ul,.md li>ol{margin:.25rem 0}
        .md h1,.md h2,.md h3,.md h4{font-weight:700;line-height:1.3;margin:1rem 0 .5rem}
        .md h1{font-size:1.25rem}.md h2{font-size:1.125rem}.md h3{font-size:1rem}
        .md a{color:#2563eb;text-decoration:underline;text-underline-offset:2px}.dark .md a{color:#60a5fa}
        .md strong{font-weight:700}
        .md code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:.875em;background:var(--card-bg-hover);padding:.15rem .35rem;border-radius:.375rem}
        .md pre{background:var(--card-bg-hover);border:1px solid var(--card-border);border-radius:.75rem;padding:.75rem 1rem;overflow-x:auto;margin:.75rem 0;max-width:100%}
        .md pre code{background:transparent;padding:0;border-radius:0}
        .md blockquote{border-left:3px solid var(--card-border);padding-left:1rem;color:var(--text-secondary);margin:.75rem 0}
        .md table{display:block;width:fit-content;max-width:100%;overflow-x:auto;border-collapse:collapse;margin:.75rem 0;font-size:.875rem;white-space:nowrap}
        .md th,.md td{border:1px solid var(--card-border);padding:.45rem .75rem;text-align:left}
        .md th{background:var(--card-bg-hover);font-weight:600}
        .md hr{border:none;border-top:1px solid var(--card-border);margin:1rem 0}

        /* ===== Reduced motion ===== */
        @media (prefers-reduced-motion: reduce){
            .typing span{animation:none;opacity:.6}
        }
    </style>

    <script>
        function officeAssistant() {
            return {
                conversationId: window.chatInit.conversationId,
                messages: window.chatInit.messages ?? [],
                draft: '',
                busy: false,
                error: '',
                atBottom: true,
                init() {
                    this.scrollToBottom(false);
                    this.$nextTick(() => {
                        if (window.matchMedia('(min-width: 768px)').matches) this.$refs.input?.focus();
                    });
                },
                onScroll() {
                    const el = this.$refs.messages;
                    this.atBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 120;
                },
                resize() {
                    const el = this.$refs.input;
                    if (!el) return;
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 180) + 'px';
                },
                onEnter(e) {
                    if (e.isComposing) return;
                    if (e.shiftKey) {
                        this.$nextTick(() => this.resize());
                        return;
                    }
                    e.preventDefault();
                    this.send();
                },
                renderMarkdown(content) {
                    try {
                        return window.DOMPurify.sanitize(window.marked.parse(content ?? '', { breaks: true }));
                    } catch (e) {
                        const el = document.createElement('span');
                        el.textContent = content ?? '';
                        return el.innerHTML;
                    }
                },
                scrollToBottom(smooth) {
                    this.$nextTick(() => {
                        const el = this.$refs.messages;
                        el.scrollTo({ top: el.scrollHeight, behavior: smooth && !this.reducedMotion() ? 'smooth' : 'auto' });
                    });
                },
                reducedMotion() {
                    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                },
                async send() {
                    const text = this.draft.trim();
                    if (!text || this.busy) return;
                    this.error = '';
                    this.messages.push({ id: 'temp-' + Date.now(), role: 'user', content: text });
                    this.draft = '';
                    this.busy = true;
                    this.$nextTick(() => this.resize());
                    this.scrollToBottom(true);
                    try {
                        const res = await fetch('{{ route('ai.assistant.send') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ message: text, conversation_id: this.conversationId }),
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            this.error = data.message || 'Terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
                            this.messages.pop();
                            return;
                        }
                        this.conversationId = data.conversation_id;
                        this.messages.push({ id: data.conversation_id + '-' + Date.now(), role: 'assistant', content: data.message, sources: data.sources ?? [] });
                        this.scrollToBottom(true);
                    } catch (e) {
                        this.error = 'Gagal terhubung ke server. Silakan coba lagi.';
                        this.messages.pop();
                    } finally {
                        this.busy = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>