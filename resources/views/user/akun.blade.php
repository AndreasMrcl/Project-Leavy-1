<!DOCTYPE html>
<html lang="en">

<head>
    <title>Akun</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen '>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-6 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex items-center">
                        <a href="{{ route('user-home') }}" class="p-2 -ml-2 text-gray-700 hover:text-black">
                            <span class="material-icons">arrow_back</span>
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Meja</h1>
                        </div>
                        <div class="w-10"></div>
                    </div>
                </div>
            </div>
            <div class="h-20"></div>

            {{-- BODY --}}
            <div class="p-4 space-y-4">
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4 border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-100 p-3 rounded-full">
                            <span class="material-icons text-red-700">table_restaurant</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Anda duduk di</p>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        </div>
                    </div>
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex items-center gap-2 text-gray-600">
                            <span class="material-icons text-base">store</span>
                            <span class="text-sm">{{ $user->store->store ?? $user->store->name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <span class="material-icons text-base">place</span>
                            <span class="text-sm">{{ $user->store->location }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full p-3 bg-gray-200 text-gray-800 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-300">
                        <span class="material-icons">logout</span>
                        Keluar / Pindah Meja
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
