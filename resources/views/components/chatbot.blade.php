@php
    // You can tweak palette to match your theme
    $accent = 'primary';
@endphp

<style>
    .chatbot-fab {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 1030;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chatbot-fab:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .chatbot-fab i {
        font-size: 1.5rem;
    }

    .chatbot-panel {
        position: fixed;
        bottom: 96px;
        left: 24px;
        width: 380px;
        max-height: 75vh;
        z-index: 1030;
        display: none;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chatbot-panel .card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    }

    .chatbot-panel .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-weight: 600;
        font-size: 1.05rem;
    }

    #chatbotClose {
        border-radius: 50%;
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        line-height: 1;
        opacity: 0.9;
        transition: all 0.2s;
    }

    #chatbotClose:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .chatbot-messages {
        height: 420px;
        overflow-y: auto;
        padding: 1.25rem;
        background: linear-gradient(to bottom, #fafafa 0%, #ffffff 100%);
    }

    .chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chatbot-messages::-webkit-scrollbar-thumb {
        background: #d0d0d0;
        border-radius: 3px;
    }

    .chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #b0b0b0;
    }

    .chat-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-row.user {
        justify-content: flex-end;
    }

    .chat-row.model {
        justify-content: flex-start;
    }

    .chat-bubble {
        border-radius: 18px;
        padding: 0.75rem 1rem;
        max-width: 75%;
        font-size: 0.95rem;
        line-height: 1.5;
        word-wrap: break-word;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }

    .chat-bubble:hover {
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }

    .chat-bubble.user {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .chat-bubble.model {
        background: white;
        color: #333;
        border: 1px solid #e8e8e8;
        border-bottom-left-radius: 4px;
    }

    #chatbotForm {
        padding: 1rem;
        background: white;
        border-top: 1px solid #e8e8e8;
    }

    #chatbotInput {
        border-radius: 24px;
        padding: 0.65rem 1.25rem;
        border: 1px solid #e0e0e0;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    #chatbotInput:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    #chatbotSend {
        border-radius: 24px;
        padding: 0.65rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
        white-space: nowrap;
    }

    #chatbotSend:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Typing indicator */
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 0.5rem;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #999;
        animation: typing 1.4s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.7;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .chatbot-panel {
            left: 12px;
            right: 12px;
            width: auto;
            max-height: 80vh;
        }

        .chatbot-fab {
            left: 16px;
            bottom: 16px;
            width: 56px;
            height: 56px;
        }

        .chat-bubble {
            max-width: 85%;
        }
    }
</style>







<div class="chatbot-panel">
    <div class="card shadow border-0">
        <div class="card-header bg-{{ $accent }} text-white d-flex justify-content-between align-items-center">
            <span>💬 Assistant</span>
            <button type="button" class="btn btn-sm btn-light" id="chatbotClose">&times;</button>
        </div>
        <div class="card-body p-0 d-flex flex-column">
            <div id="chatbotMessages" class="chatbot-messages">
                <!-- Messages will be appended here -->
                <div class="chat-row model">
                    <div class="chat-bubble model">
                        Hi! I'm here to help with causes, events, workshops, and projects. How can I help?
                    </div>
                </div>
            </div>
            <form id="chatbotForm" class="d-flex gap-2">
                @csrf
                <input id="chatbotInput" class="form-control" placeholder="Type your message..." autocomplete="off">
                <button id="chatbotSend" type="submit" class="btn btn-{{ $accent }}">Send</button>
            </form>
        </div>
    </div>
</div>

<button class="btn btn-{{ $accent }} chatbot-fab" id="chatbotFab" title="Chat with us">
    <i class="bi bi-chat-dots"></i>
</button>






<script>
    (function(){
        const fab = document.getElementById('chatbotFab');
        const panel = document.querySelector('.chatbot-panel');
        const closeBtn = document.getElementById('chatbotClose');
        const form = document.getElementById('chatbotForm');
        const input = document.getElementById('chatbotInput');
        const messagesEl = document.getElementById('chatbotMessages');
        const sendBtn = document.getElementById('chatbotSend');

        let history = [
            { role: 'model', text: "Hi! I'm here to help with causes, events, workshops, and projects. How can I help?" }
        ];

        function appendMessage(role, text) {
            const row = document.createElement('div');
            row.className = 'chat-row ' + (role === 'user' ? 'user' : 'model');
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble ' + (role === 'user' ? 'user' : 'model');

            if (text === '…') {
                // Create typing indicator
                bubble.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
            } else {
                bubble.innerText = text;
            }

            row.appendChild(bubble);
            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        fab.addEventListener('click', () => {
            panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
            if (panel.style.display === 'block') input.focus();
        });

        closeBtn.addEventListener('click', () => {
            panel.style.display = 'none';
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = (input.value || '').trim();
            if (!text) return;

            appendMessage('user', text);
            history.push({ role: 'user', text });
            input.value = '';
            input.disabled = true;
            sendBtn.disabled = true;

            appendMessage('model', '…');
            const typingRow = messagesEl.lastElementChild;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const res = await fetch('{{ route('chatbot.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ messages: history }),
                    credentials: 'same-origin'
                });

                let data;
                const ct = res.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    data = await res.json();
                } else {
                    const txt = await res.text();
                    data = { reply: txt.slice(0, 200) || 'No response body.' };
                }

                typingRow.remove();
                const reply = (data && data.reply) ? data.reply : "Sorry, I couldn't reply.";
                appendMessage('model', reply);
                history.push({ role: 'model', text: reply });

            } catch (err) {
                typingRow.remove();
                appendMessage('model', 'Network error. Please try again.');
            } finally {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            }
        });
    })();
</script>
