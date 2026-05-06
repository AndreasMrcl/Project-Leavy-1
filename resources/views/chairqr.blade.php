<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>QR Code - {{ $chair->name }}</title>
    @include('layout.head')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-6">
    <div class="no-print w-full max-w-xl mb-4 flex justify-between items-center">
        <a href="{{ route('chair') }}"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow hover:bg-gray-300 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()"
            class="px-6 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition flex items-center gap-2">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-10 max-w-xl w-full text-center space-y-6 border border-gray-200">
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wider">Scan untuk pesan</p>
            <h1 class="text-4xl font-extrabold text-gray-900 mt-2">{{ $chair->name }}</h1>
        </div>

        <div class="flex justify-center">
            <div class="p-4 bg-white border-4 border-gray-900 rounded-xl">
                {!! QrCode::size(320)->margin(0)->generate($signinUrl) !!}
            </div>
        </div>

        <div class="text-gray-600 space-y-1">
            <p class="font-semibold">Cara pesan:</p>
            <ol class="text-sm text-left inline-block">
                <li>1. Buka kamera HP Anda</li>
                <li>2. Arahkan ke QR di atas</li>
                <li>3. Tap link yang muncul</li>
                <li>4. Pilih menu &amp; bayar</li>
            </ol>
        </div>

        <p class="no-print text-xs text-gray-400 break-all">
            URL: {{ $signinUrl }}
        </p>
    </div>
</body>

</html>
