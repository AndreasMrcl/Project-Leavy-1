<!DOCTYPE html>
<html lang="en">

<head>
    <title>Meja Anda</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto min-h-screen'>

        {{-- NAVBAR --}}
        <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
            <div class="bg-white shadow-lg rounded-b-[22px] px-4 py-4 flex items-center gap-2">
                <a href="{{ route('user-home') }}" class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                    <span class="material-icons text-lg">arrow_back</span>
                </a>
                <div class="flex-1 text-center">
                    <h1 class="text-base font-semibold text-gray-900">Meja Anda</h1>
                </div>
                <div class="w-9"></div>
            </div>
        </div>

        <div class="h-20"></div>

        {{-- BODY --}}
        <div class="px-4 pb-8 space-y-3">

            {{-- Hero table card --}}
            <div class="relative overflow-hidden rounded-3xl p-5 text-white shadow-xl"
                 style="background: linear-gradient(140deg, #7F1D1D 0%, #B91C1C 100%);">
                <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white opacity-10"></div>
                <div class="absolute -bottom-12 -left-6 w-28 h-28 rounded-full bg-white opacity-5"></div>

                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-white bg-opacity-20 flex items-center justify-center">
                        <span class="material-icons text-3xl">table_restaurant</span>
                    </div>
                    <p class="text-[10px] uppercase tracking-widest font-bold opacity-80 mt-4">Anda sedang di</p>
                    <h2 class="text-4xl font-extrabold leading-tight mt-1">Meja {{ $user->name }}</h2>
                    <p class="text-xs opacity-80 mt-1">{{ $user->store->store ?? ($user->store->name ?? '') }}</p>
                </div>
            </div>

            {{-- Outlet info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Outlet</p>
                <div class="mt-2 space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <span class="material-icons text-gray-600 text-base">storefront</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-gray-400 font-semibold">Nama</p>
                            <p class="text-xs font-semibold text-gray-900 truncate">{{ $user->store->store ?? ($user->store->name ?? '-') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <span class="material-icons text-gray-600 text-base">place</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-gray-400 font-semibold">Alamat</p>
                            <p class="text-xs font-semibold text-gray-900">{{ $user->store->location ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action list --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <a href="{{ route('user-antrian') }}" class="flex items-center gap-3 px-3 py-3 active:bg-gray-50">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-800 flex items-center justify-center shrink-0">
                        <span class="material-icons text-lg">receipt_long</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900">Lihat Pesanan</p>
                        <p class="text-[10px] text-gray-400">Cek status & history</p>
                    </div>
                    <span class="material-icons text-gray-300">chevron_right</span>
                </a>
                <div class="border-t border-gray-100"></div>
                <a href="{{ route('user-home') }}" class="flex items-center gap-3 px-3 py-3 active:bg-gray-50">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-800 flex items-center justify-center shrink-0">
                        <span class="material-icons text-lg">restaurant_menu</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900">Kembali ke Menu</p>
                        <p class="text-[10px] text-gray-400">Tambah pesanan baru</p>
                    </div>
                    <span class="material-icons text-gray-300">chevron_right</span>
                </a>
            </div>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full p-3.5 bg-white border border-gray-200 text-red-800 rounded-2xl flex items-center justify-center gap-2 text-sm font-bold active:bg-gray-50">
                    <span class="material-icons text-base">logout</span>
                    Keluar &amp; Ganti Meja
                </button>
            </form>

            <p class="text-[10px] text-gray-400 text-center">
                Sesi otomatis berakhir setelah 4 jam tidak aktif
            </p>
        </div>
    </div>
</body>

</html>
