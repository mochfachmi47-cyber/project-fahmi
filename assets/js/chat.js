/**
 * FORSAKDA 26 - Live Chat System (Santri & Alumni Chat Room)
 */

class ForsakdaChat {
    constructor(options) {
        this.container = document.getElementById(options.containerId || 'chatMessagesContainer');
        this.form = document.getElementById(options.formId || 'chatForm');
        this.input = document.getElementById(options.inputId || 'chatMessageInput');
        this.statusEl = document.getElementById(options.statusId || 'chatStatusBadge');
        this.apiUrl = options.apiUrl || '../../actions/chat_api.php';
        this.currentUserId = options.currentUserId || 0;
        this.csrfToken = options.csrfToken || '';
        this.room = options.room || 'general';
        this.lastMessageId = 0;
        this.pollingInterval = null;
        this.soundEnabled = true;

        this.init();
    }

    init() {
        if (!this.container || !this.form || !this.input) return;

        // Fetch initial messages
        this.loadMessages(true);

        // Start polling every 2.5 seconds
        this.pollingInterval = setInterval(() => {
            this.loadMessages(false);
        }, 2500);

        // Bind form submit
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // Send on Ctrl+Enter or Cmd+Enter
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
    }

    playNotificationSound() {
        if (!this.soundEnabled) return;
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
            osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15); // A5
            gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.25);
        } catch (e) {
            // AudioContext not allowed before user gesture
        }
    }

    async loadMessages(isInitial = false) {
        try {
            const url = `${this.apiUrl}?action=get_messages&room=${encodeURIComponent(this.room)}&last_id=${this.lastMessageId}`;
            const res = await fetch(url);
            const data = await res.json();

            if (data.status === 'success' && data.messages.length > 0) {
                let hasNewFromOthers = false;

                data.messages.forEach(msg => {
                    if (msg.id > this.lastMessageId) {
                        this.lastMessageId = msg.id;
                    }
                    if (parseInt(msg.user_id) !== parseInt(this.currentUserId)) {
                        hasNewFromOthers = true;
                    }
                    this.renderMessage(msg);
                });

                if (!isInitial && hasNewFromOthers) {
                    this.playNotificationSound();
                }

                this.scrollToBottom();
            }
        } catch (err) {
            console.error('Chat load error:', err);
        }
    }

    async sendMessage() {
        const text = this.input.value.trim();
        if (!text) return;

        this.input.disabled = true;

        try {
            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('csrf_token', this.csrfToken);
            formData.append('room', this.room);
            formData.append('pesan', text);

            const res = await fetch(this.apiUrl, {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.status === 'success') {
                this.input.value = '';
                // Immediately fetch new messages
                this.loadMessages(false);
            } else {
                alert(data.message || 'Gagal mengirim pesan');
            }
        } catch (err) {
            console.error('Send error:', err);
            alert('Terjadi kesalahan jaringan saat mengirim pesan.');
        } finally {
            this.input.disabled = false;
            this.input.focus();
        }
    }

    renderMessage(msg) {
        const isMe = parseInt(msg.user_id) === parseInt(this.currentUserId);
        const div = document.createElement('div');
        div.className = `flex items-end gap-2.5 mb-4 ${isMe ? 'justify-end' : 'justify-start'} animate-fade-in`;

        const avatarUrl = msg.foto ? `../../${msg.foto}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(msg.nama)}&background=059669&color=fff`;

        const roleBadge = msg.role === 'admin'
            ? '<span class="text-[10px] bg-amber-500/20 text-amber-600 px-1.5 py-0.5 rounded font-semibold ml-1">Admin</span>'
            : '<span class="text-[10px] bg-emerald-500/20 text-emerald-600 px-1.5 py-0.5 rounded font-semibold ml-1">Santri 27</span>';

        const escapedText = this.escapeHtml(msg.pesan).replace(/\n/g, '<br>');

        if (isMe) {
            div.innerHTML = `
                <div class="flex flex-col items-end max-w-[78%]">
                    <span class="text-[11px] text-slate-400 mb-1">Anda &bull; ${msg.time_formatted || 'Baru saja'}</span>
                    <div class="chat-bubble-me text-sm px-4 py-2.5 rounded-2xl shadow-sm">
                        ${escapedText}
                    </div>
                </div>
            `;
        } else {
            div.innerHTML = `
                <img src="${avatarUrl}" alt="${this.escapeHtml(msg.nama)}" class="w-8 h-8 rounded-full object-cover border border-emerald-500/30 flex-shrink-0 mb-1">
                <div class="flex flex-col items-start max-w-[78%]">
                    <div class="flex items-center gap-1 mb-1">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">${this.escapeHtml(msg.nama)}</span>
                        ${roleBadge}
                        <span class="text-[10px] text-slate-400 ml-1.5">${msg.time_formatted || ''}</span>
                    </div>
                    <div class="chat-bubble-other text-sm px-4 py-2.5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 dark:bg-slate-800 dark:text-slate-100">
                        ${escapedText}
                    </div>
                </div>
            `;
        }

        this.container.appendChild(div);
    }

    scrollToBottom() {
        if (this.container) {
            this.container.scrollTop = this.container.scrollHeight;
        }
    }

    escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    insertEmoji(emoji) {
        if (this.input) {
            this.input.value += emoji;
            this.input.focus();
        }
    }
}

window.ForsakdaChat = ForsakdaChat;
