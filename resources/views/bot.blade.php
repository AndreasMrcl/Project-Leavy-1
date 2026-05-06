<!DOCTYPE html>
<html lang="en">

<head>
    <title>Support</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layout.head')
</head>

<body class="bg-gray-50">

    <!-- sidenav  -->
    @include('layout.sidebar')
    <!-- end sidenav -->

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        <!-- Navbar -->
        @include('layout.navbar')
        <!-- end Navbar -->

        <div class="p-5">
            <div class="w-full bg-white rounded-xl h-fit mx-auto p-2">
                <div class="space-y-2">
                    <div id="chatHistory" class="space-y-2 rounded-xl h-[400px] md:h-[550px] overflow-y-auto">
                        @foreach ($chats as $chat)
                            <div class="">
                                <div class="text-right">
                                    <div class="inline-block border text-black p-2 rounded-xl max-w-[65%]">
                                        {{ $chat->prompt }}
                                    </div>
                                </div>
                                <div class="text-left">
                                    <div class="inline-block text-black p-2 border rounded-xl max-w-[65%]">
                                        {!! $chat->response !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="loadingIndicator" class="hidden text-center text-gray-500 p-2 font-semibold">
                        Loading...
                    </div>

                    @php
                        $suggestions = [
                            'Berapa total penjualan hari ini?',
                            'Berapa pendapatan bulan ini?',
                            'Jumlah order hari ini berapa?',
                            'Jenis pembayaran terbanyak apa?',
                            'Tren penjualan minggu ini seperti apa?',
                            'Produk paling laris apa?',
                            'Berapa rata-rata penjualan hari ini?',
                            'Berapa total transaksi hari ini?',
                        ];
                    @endphp

                    <div class="flex overflow-x-auto gap-2 py-2">
                        @foreach ($suggestions as $s)
                            <button type="button"
                                onclick="document.getElementById('prompt').value = '{{ $s }}';"
                                class="bg-gray-200 hover:bg-gray-300 text-sm px-3 py-2 rounded-xl whitespace-nowrap">
                                {{ $s }}
                            </button>
                        @endforeach
                    </div>

                    <form id="promptForm" class="space-y-3" method="post" action="{{ route('gen') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div>
                            <input type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-xl w-full"
                                id="prompt" name="prompt" placeholder="Halo" required />
                        </div>
                        <button type="submit" class="bg-blue-500 text-white p-4 w-full hover:text-black rounded-xl">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('promptForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const promptInput = document.getElementById('prompt');
            const prompt = promptInput.value.trim();
            const chatHistory = document.getElementById('chatHistory');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const submitBtn = this.querySelector('button[type="submit"]');

            if (!prompt) return;

            try {
                loadingIndicator.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

                const res = await fetch("{{ route('gen') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        prompt
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    const userMsg = document.createElement('div');
                    userMsg.innerHTML = `
                        <div class="text-right">
                            <div class="inline-block border text-black p-2 rounded-xl max-w-[65%]">
                                ${prompt}
                            </div>
                        </div>
                    `;

                    const botMsg = document.createElement('div');
                    botMsg.innerHTML = `
                        <div class="text-left">
                            <div class="inline-block text-black p-2 border rounded-xl max-w-[65%]">
                                ${data.response}
                            </div>
                        </div>
                    `;

                    const wrapper = document.createElement('div');
                    wrapper.classList.add("space-y-1");
                    wrapper.appendChild(userMsg);
                    wrapper.appendChild(botMsg);

                    chatHistory.appendChild(wrapper);
                    chatHistory.scrollTop = chatHistory.scrollHeight;

                    // Reset input field
                    promptInput.value = "";
                    promptInput.focus();
                } else {
                    alert(data.error || "Terjadi kesalahan.");
                }
            } catch (error) {
                alert("Gagal menghubungi server.");
            } finally {
                loadingIndicator.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>

    @include('sweetalert::alert')
</body>

</html>
