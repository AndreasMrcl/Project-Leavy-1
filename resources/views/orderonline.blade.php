<!DOCTYPE html>
<html lang="en">

<head>
    <title>Online Payment — {{ $order->no_order }}</title>
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
                <div class="bg-purple-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center border border-purple-200">
                    <i class="fas fa-qrcode text-purple-600 text-3xl"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-800">QRIS Payment</h1>
                <p class="text-sm text-gray-500">
                    Order: <span class="font-mono font-bold text-gray-700">{{ $order->no_order }}</span><br>
                    Total: <strong class="font-mono text-gray-800">Rp{{ number_format($order->cart->total_amount, 0, ',', '.') }}</strong>
                </p>
                <p id="snapStatus" class="text-sm text-gray-600">Opening Midtrans Snap UI...</p>

                <a href="{{ route('order') }}"
                    class="block w-full py-3 bg-gray-200 text-gray-800 rounded-lg font-bold hover:bg-gray-300 transition text-center text-sm">
                    Back to Orders
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
                    document.getElementById('snapStatus').textContent = 'Payment successful. Confirming...';
                    confirmPayment();
                },
                onPending: function (result) {
                    document.getElementById('snapStatus').textContent = 'Payment pending. Checking status...';
                    confirmPayment();
                },
                onError: function (result) {
                    backToOrder('Payment failed. Please try again via the Continue Payment button on the orders table.');
                },
                onClose: function () {
                    backToOrder('Payment closed. Click the Continue Payment button on the orders table to resume.');
                },
            });
        });
    </script>

    @include('sweetalert::alert')
</body>

</html>
