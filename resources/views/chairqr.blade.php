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
            class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg shadow-sm hover:bg-gray-300 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button onclick="window.print()"
            class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-10 max-w-xl w-full text-center space-y-6 border border-gray-200">
        <div>
            <p class="text-sm text-gray-500 uppercase tracking-wider font-bold">Scan to order</p>
            <h1 class="text-4xl font-extrabold text-gray-900 mt-2">{{ $chair->name }}</h1>
        </div>

        <div class="flex justify-center">
            <div class="p-4 bg-white border-4 border-gray-900 rounded-xl">
                {!! QrCode::size(320)->margin(0)->generate($signinUrl) !!}
            </div>
        </div>

        <div class="text-gray-600 space-y-1">
            <p class="font-bold">How to order:</p>
            <ol class="text-sm text-left inline-block">
                <li>1. Open your phone camera</li>
                <li>2. Point it at the QR above</li>
                <li>3. Tap the link that appears</li>
                <li>4. Pick a menu &amp; pay</li>
            </ol>
        </div>

        <p class="no-print text-xs text-gray-400 break-all">
            URL: {{ $signinUrl }}
        </p>
    </div>
</body>

</html>
