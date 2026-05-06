<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pembayaran Online — {{ $order->no_order }}</title>
    @include('layout.head')
    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6">
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-md border border-gray-100 p-8 text-center space-y-4">
                <div class="bg-purple-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center">
                    <i class="fas fa-qrcode text-purple-600 text-3xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">Pembayaran QRIS</h1>
                <p class="text-sm text-gray-500">
                    Order: <span class="font-mono">{{ $order->no_order }}</span><br>
                    Total: <strong>Rp{{ number_format($order->cart->total_amount, 0, ',', '.') }}</strong>
                </p>
                <p id="snapStatus" class="text-sm text-gray-600">Membuka Midtrans Snap UI...</p>

                <a href="{{ route('order') }}"
                    class="block w-full py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                    Kembali ke Order
                </a>
            </div>
        </div>
    </main>

    <form id="confirmForm" method="post" action="{{ route('midtrans-confirm', ['orderId' => $order->id]) }}">
        @csrf
    </form>

    <script>
        function confirmPayment() {
            document.getElementById('confirmForm').submit();
        }

        function backToOrder(message) {
            // Use sessionStorage to pass message to /order page (non-flash since not via Laravel redirect)
            if (message) {
                sessionStorage.setItem('orderOnlineMessage', message);
            }
            window.location = '{{ route('order') }}';
        }

        document.addEventListener('DOMContentLoaded', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    document.getElementById('snapStatus').textContent = 'Pembayaran berhasil. Mengkonfirmasi...';
                    confirmPayment();
                },
                onPending: function (result) {
                    document.getElementById('snapStatus').textContent = 'Pembayaran pending. Memeriksa status...';
                    confirmPayment();
                },
                onError: function (result) {
                    backToOrder('Pembayaran gagal. Silakan coba lagi melalui tombol Lanjutkan Pembayaran di tabel order.');
                },
                onClose: function () {
                    backToOrder('Pembayaran ditutup. Klik tombol Lanjutkan Pembayaran di tabel order untuk melanjutkan.');
                },
            });
        });
    </script>

    @include('sweetalert::alert')
</body>

</html>
