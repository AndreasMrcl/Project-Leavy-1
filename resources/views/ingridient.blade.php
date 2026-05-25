<!DOCTYPE html>
<html lang="en">

<head>
    <title>Recipe Builder</title>
    @include('layout.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            @php
                $menusPayload = $menus->map(function ($menu) {
                    $varieties = $menu->has_variety ? ($menu->varieties ?? ['normal']) : ['normal'];
                    $rowsByVariety = [];
                    foreach ($varieties as $v) {
                        $rowsByVariety[$v] = [];
                    }
                    foreach ($menu->invents as $inv) {
                        $v = $inv->pivot->variety ?? 'normal';
                        if (! isset($rowsByVariety[$v])) {
                            continue;
                        }
                        $rowsByVariety[$v][] = [
                            'invent_id' => (string) $inv->id,
                            'name' => $inv->name,
                            'unit' => $inv->unit,
                            'qty' => (float) $inv->pivot->quantity_used,
                        ];
                    }
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'has_variety' => (bool) $menu->has_variety,
                        'varieties' => array_values($varieties),
                        'has_recipe' => $menu->invents->isNotEmpty(),
                        'recipe' => $rowsByVariety,
                    ];
                })->keyBy('id');
            @endphp

            <!-- Header Section -->
            <div class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i> Recipe
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Select a product and manage its recipe ingredients</p>
                </div>
                <a href="{{ route('product') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg shadow-sm hover:bg-gray-300 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            @if ($menus->isEmpty())
                <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center opacity-70">
                            <div
                                class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-4 border border-red-100">
                                <i class="fas fa-inbox text-4xl text-red-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No Products Available</h3>
                            <p class="text-sm text-gray-500 mt-1 mb-6">Please add a <strong>Product</strong> before
                                creating a recipe.</p>
                            <a href="{{ route('product') }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 hover:scale-105 transition font-bold text-sm">
                                <i class="fas fa-plus"></i> Go to Product Page
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <form id="ingredientForm" method="post" action="">
                    @csrf
                    @method('put')

                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                        <!-- LEFT: Select Product -->
                        <div class="lg:col-span-3 bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-700">Select Product</h3>
                                <input type="text" id="menuSearch" placeholder="Search product..."
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm p-2 border text-sm focus:ring-2 focus:ring-red-500">
                            </div>

                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Already has a recipe
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> No recipe yet
                                </span>
                            </div>

                            <div id="menuGrid"
                                class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 max-h-[60vh] overflow-y-auto pr-1">
                                @foreach ($menus as $menu)
                                    @php $hasRecipe = $menu->invents->isNotEmpty(); @endphp
                                    <button type="button"
                                        class="menuCard relative bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:border-red-400 active:scale-95 transition p-2 text-left"
                                        data-id="{{ $menu->id }}"
                                        data-name="{{ $menu->name }}"
                                        data-name-lower="{{ strtolower($menu->name) }}">
                                        <div class="aspect-square bg-gray-50 rounded-lg overflow-hidden mb-2 relative">
                                            @if ($menu->img)
                                                <img src="{{ asset('storage/img/' . basename($menu->img)) }}"
                                                    alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <i class="fas fa-utensils text-3xl"></i>
                                                </div>
                                            @endif
                                            <span class="absolute top-1.5 right-1.5 w-3 h-3 rounded-full ring-2 ring-white {{ $hasRecipe ? 'bg-green-500' : 'bg-gray-300' }}"
                                                title="{{ $hasRecipe ? 'Already has a recipe' : 'No recipe yet' }}"></span>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $menu->name }}</div>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            @if ($menu->has_variety)
                                                <span class="text-[10px] font-semibold uppercase text-purple-600 bg-purple-50 inline-block px-1.5 py-0.5 rounded-full">variety</span>
                                            @endif
                                            <span class="text-[10px] font-semibold uppercase {{ $hasRecipe ? 'text-green-700 bg-green-50' : 'text-gray-500 bg-gray-100' }} inline-block px-1.5 py-0.5 rounded-full">
                                                {{ $hasRecipe ? '✓ Recipe' : 'No Recipe' }}
                                            </span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- RIGHT: Composition -->
                        <div class="lg:col-span-2 bg-white rounded-xl shadow-md border border-gray-100 p-5 space-y-4">

                            <!-- Empty state -->
                            <div id="emptyState" class="text-center py-10 text-gray-400">
                                <i class="fas fa-bowl-food text-4xl block mb-3"></i>
                                <p class="text-sm">Select a product to view or create its recipe.</p>
                            </div>

                            <!-- Composition area -->
                            <div id="compositionArea" class="hidden space-y-4">
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded">
                                    <p class="text-xs text-red-700">Recipe for:</p>
                                    <p class="font-semibold text-red-800 text-sm" id="compProductName">-</p>
                                </div>

                                <!-- Variety tabs -->
                                <div id="varietyTabs" class="hidden border-b border-gray-200">
                                    <div id="varietyTabsList" class="flex gap-2 -mb-px overflow-x-auto"></div>
                                </div>

                                <!-- Ingredient list -->
                                <div id="varietyPanels" class="space-y-3"></div>

                                <button type="button" id="openModalBtn"
                                    class="w-full py-3 border-2 border-dashed border-gray-300 text-gray-600 rounded-lg hover:bg-red-50 hover:border-red-400 hover:text-red-600 transition font-semibold flex items-center justify-center gap-2">
                                    <i class="fas fa-plus"></i> Add Ingredient
                                </button>

                                <div class="border-t pt-3 space-y-2">
                                    <button type="submit" id="submitBtn"
                                        class="w-full py-3 bg-red-500 text-white font-bold rounded-lg shadow-md hover:bg-red-600 transition flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                        <i class="fas fa-save"></i> <span id="submitLabel">Save Recipe</span>
                                    </button>
                                    <button type="button" id="deleteBtn"
                                        class="hidden w-full py-2.5 bg-white border border-red-300 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition items-center justify-center gap-2">
                                        <i class="fas fa-trash"></i> Delete Recipe
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Hidden form khusus delete -->
                <form id="deleteForm" method="post" action="" class="hidden">
                    @csrf
                    @method('delete')
                </form>
            @endif
        </div>
    </main>

    @include('modal.addRecipe')

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script>
        window.menusData = @json($menusPayload);
        window.upsertUrlTemplate = "{{ route('upsertingridient', ['id' => '__ID__']) }}";
        window.deleteUrlTemplate = "{{ route('delingridient', ['id' => '__ID__']) }}";
    </script>

    <script src="{{ asset('modal/recipe.js') }}"></script>

    @include('sweetalert::alert')
</body>

</html>
