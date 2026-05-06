<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Product</title>
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
            <div class='w-full bg-white rounded-xl h-fit mx-auto'>
                <div class="p-3 text-center">
                    <h1 class="font-extrabold text-3xl">Edit product</h1>
                </div>
                <div class="p-6">
                    <form class="space-y-3" method="post" action="{{ route('updateproduct', ['id' => $menu->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-2">
                        <div class="space-y-2">
                            <label class="font-semibold text-black">Nama produk:</label>
                            <input type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                id="name" name="name" value="{{ $menu->name }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="font-semibold text-black">Harga produk:</label>
                            <input type="number"
                                class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                id="price" name="price" value="{{ $menu->price }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="font-semibold text-black">Kategori:</label>
                            <select id="category" name="category_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full" required>
                                <option></option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}" {{ $cat->id == $menu->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        <div class="space-y-2">
                            <label class="font-semibold text-black">Deskripsi produk:</label>
                            <textarea class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full" id="description"
                                name="description" maxlength="200" required>{{ $menu->description }}</textarea>
                            <p class="text-gray-500 text-right"><span
                                    id="charCount">{{ strlen($menu->description) }}</span>/200 characters</p>
                        </div>
                        <div class="space-y-2">
                            <label class="font-semibold text-black">Gambar produk:</label>
                            <input type="file"
                                class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                id="img" name="img" value="{{ $menu->img }}">
                            @if ($menu->img)
                                <p class="text-xs text-gray-500">Saat ini: {{ basename($menu->img) }}. Kosongkan jika tidak ingin ganti.</p>
                            @endif
                        </div>

                        @php
                            $varietyLabels = collect($menu->varieties ?? [])
                                ->map(fn ($v) => str_replace('_', ' ', $v))
                                ->map(fn ($v) => ucwords($v))
                                ->implode(', ');
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" id="has_variety" name="has_variety" value="1"
                                    @checked($menu->has_variety)
                                    class="w-5 h-5 rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                                <span class="font-semibold text-black">Has Variety</span>
                                <span class="text-xs text-gray-500">(produk punya pilihan varian, mis. less sugar / extra shot)</span>
                            </label>

                            <div id="varietiesWrap" class="space-y-2 {{ $menu->has_variety ? '' : 'hidden' }}">
                                <label class="font-semibold text-black text-sm">Daftar variety:</label>
                                <input type="text" id="varieties" name="varieties"
                                    value="{{ $varietyLabels }}"
                                    {{ $menu->has_variety ? 'required' : '' }}
                                    class="bg-white border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    placeholder="Normal, Less Sugar, Extra Shot">
                                <p class="text-xs text-gray-500">Pisahkan dengan koma. Mengubah daftar variety akan menghapus resep bahan untuk variety yang dihapus.</p>
                            </div>
                        </div>

                        <button type="submit"
                            class="bg-blue-500 text-white p-4 w-full hover:text-black rounded-lg">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('description').addEventListener('input', function() {
            var maxLength = 200;
            var currentLength = this.value.length;

            document.getElementById('charCount').innerText = currentLength + '/' + maxLength;

            if (currentLength >= maxLength) {
                this.setAttribute('disabled', true);
            } else {
                this.removeAttribute('disabled');
            }
        });

        const hasVarietyCb = document.getElementById('has_variety');
        const varietiesWrap = document.getElementById('varietiesWrap');
        const varietiesInput = document.getElementById('varieties');
        hasVarietyCb.addEventListener('change', function () {
            if (this.checked) {
                varietiesWrap.classList.remove('hidden');
                varietiesInput.required = true;
            } else {
                varietiesWrap.classList.add('hidden');
                varietiesInput.required = false;
            }
        });
    </script>

    @include('sweetalert::alert')
</body>
</html>
