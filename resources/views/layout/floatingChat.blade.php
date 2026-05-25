<!-- Floating Chat Widget -->
<div id="chatWidget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3">
    <!-- Chat Panel -->
    <div id="chatPanel"
        class="hidden w-[360px] max-w-[calc(100vw-3rem)] bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
        style="height: 540px;">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 bg-red-600 text-white">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <i class="material-icons text-white">support_agent</i>
                </div>
                <div>
                    <h3 class="font-semibold text-sm leading-tight">Support</h3>
                    <p class="text-xs text-red-100">AI Assistant</p>
                </div>
            </div>
            <button id="closeChatPanel" class="hover:bg-red-700 p-1.5 rounded-full transition" title="Close">
                <i class="material-icons text-base">close</i>
            </button>
        </div>

        <!-- History -->
        <div id="chatHistory" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <div id="chatInitialLoader" class="text-center text-gray-400 text-sm py-8">
                <i class="fas fa-circle-notch fa-spin"></i> Loading...
            </div>
        </div>

        <!-- Bot typing indicator -->
        <div id="chatLoading" class="hidden px-4 py-2 text-center text-gray-500 text-xs bg-gray-50">
            <i class="fas fa-circle-notch fa-spin"></i> Bot is typing...
        </div>

        <!-- Suggestions -->
        @php
            $chatSuggestions = [
                'What is the total sales today?',
                'What is the revenue this month?',
                'How many orders today?',
                'What is the best-selling product?',
                'What is the sales trend this week?',
                'What is the most-used payment type?',
            ];
        @endphp
        <div class="flex overflow-x-auto gap-2 px-3 py-2 border-t border-gray-200 bg-white">
            @foreach ($chatSuggestions as $s)
                <button type="button"
                    class="chat-suggestion bg-gray-100 hover:bg-gray-200 text-xs px-3 py-1.5 rounded-full whitespace-nowrap text-gray-700 transition flex-shrink-0">
                    {{ $s }}
                </button>
            @endforeach
        </div>

        <!-- Form -->
        <form id="chatForm" class="p-3 border-t border-gray-200 bg-white flex gap-2 items-center">
            <input type="text" id="chatPrompt" name="prompt" placeholder="Ask something..." required
                maxlength="500"
                class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            <button type="submit" id="chatSubmit"
                class="bg-red-600 hover:bg-red-700 text-white w-10 h-10 rounded-full flex items-center justify-center transition flex-shrink-0">
                <i class="material-icons text-base">send</i>
            </button>
        </form>
    </div>

    <!-- FAB Toggle -->
    <button id="chatFab"
        class="bg-red-600 hover:bg-red-700 text-white w-14 h-14 rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95"
        title="Open Chat">
        <i id="chatFabIcon" class="material-icons text-2xl">support_agent</i>
    </button>
</div>

<script>
    (function () {
        const fab = document.getElementById('chatFab');
        const fabIcon = document.getElementById('chatFabIcon');
        const panel = document.getElementById('chatPanel');
        const closeBtn = document.getElementById('closeChatPanel');
        const history = document.getElementById('chatHistory');
        const initialLoader = document.getElementById('chatInitialLoader');
        const loading = document.getElementById('chatLoading');
        const form = document.getElementById('chatForm');
        const input = document.getElementById('chatPrompt');
        const submitBtn = document.getElementById('chatSubmit');

        const csrfToken = '{{ csrf_token() }}';
        const chatsUrl = '{{ route('chats') }}';
        const genUrl = '{{ route('gen') }}';

        let historyLoaded = false;

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function appendMessage(prompt, responseHtml) {
            const wrap = document.createElement('div');
            wrap.className = 'space-y-1';
            wrap.innerHTML = `
                <div class="flex justify-end">
                    <div class="bg-red-600 text-white px-3 py-2 rounded-2xl rounded-br-sm max-w-[80%] text-sm break-words">${escapeHtml(prompt)}</div>
                </div>
                <div class="flex justify-start">
                    <div class="bg-white border border-gray-200 text-gray-800 px-3 py-2 rounded-2xl rounded-bl-sm max-w-[80%] text-sm break-words prose prose-sm">${responseHtml}</div>
                </div>
            `;
            history.appendChild(wrap);
            history.scrollTop = history.scrollHeight;
        }

        function showEmptyState() {
            history.innerHTML = '<div class="text-center text-gray-400 text-sm py-8">No conversation yet.<br>Start with a question below.</div>';
        }

        async function loadHistory() {
            if (historyLoaded) return;
            try {
                const res = await fetch(chatsUrl, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Failed');
                const data = await res.json();
                history.innerHTML = '';
                if (data.chats && data.chats.length) {
                    data.chats.forEach(c => appendMessage(c.prompt, c.response));
                } else {
                    showEmptyState();
                }
                historyLoaded = true;
            } catch (e) {
                initialLoader.innerHTML = '<span class="text-red-500">Failed to load history.</span>';
            }
        }

        function openPanel() {
            panel.classList.remove('hidden');
            fabIcon.textContent = 'close';
            loadHistory();
            setTimeout(() => input.focus(), 100);
        }

        function closePanel() {
            panel.classList.add('hidden');
            fabIcon.textContent = 'support_agent';
        }

        fab.addEventListener('click', () => {
            panel.classList.contains('hidden') ? openPanel() : closePanel();
        });

        closeBtn.addEventListener('click', closePanel);

        document.querySelectorAll('.chat-suggestion').forEach(btn => {
            btn.addEventListener('click', () => {
                input.value = btn.textContent.trim();
                input.focus();
            });
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const prompt = input.value.trim();
            if (!prompt) return;

            // Clear empty state if present
            const emptyState = history.querySelector('.text-center.text-gray-400');
            if (emptyState) history.innerHTML = '';

            loading.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const res = await fetch(genUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ prompt })
                });
                const data = await res.json();

                if (res.ok) {
                    appendMessage(prompt, data.response);
                    input.value = '';
                } else {
                    alert(data.error || 'An error occurred.');
                }
            } catch (err) {
                alert('Failed to contact server.');
            } finally {
                loading.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    })();
</script>
