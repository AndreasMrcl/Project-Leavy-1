<!DOCTYPE html>
<html lang="en">

<head>
    <title>Store Configuration</title>
    @include('layout.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div class="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-cogs text-slate-600"></i> Store Configuration
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Global settings for store operations</p>
                </div>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Section -->
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <form action="{{ route('updatestoreConfig') }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- TRANSACTION SETTINGS -->
                        <div>
                            <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-4 border-b pb-2">
                                <i class="fas fa-money-bill-wave mr-1"></i> Transaction Settings
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Currency</label>
                                    <select name="currency"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                                        <option value="IDR" {{ $config->currency == 'IDR' ? 'selected' : '' }}>IDR
                                            (Rupiah)</option>
                                        <option value="USD" {{ $config->currency == 'USD' ? 'selected' : '' }}>USD
                                            (Dollar)</option>
                                        <option value="MYR" {{ $config->currency == 'MYR' ? 'selected' : '' }}>MYR
                                            (Ringgit)</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Default currency for all transactions</p>
                                </div>
                            </div>
                        </div>

                        <!-- TAX & SERVICE -->
                        <div>
                            <h3 class="text-sm font-bold text-emerald-600 uppercase tracking-wider mb-4 border-b pb-2">
                                <i class="fas fa-percent mr-1"></i> Tax & Service Charge
                            </h3>

                            <!-- Master Switches -->
                            <div class="md:flex gap-6 mb-6 space-y-2 md:space-y-0">
                                <label
                                    class="inline-flex items-center cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                    <input type="checkbox" name="tax_active" value="1"
                                        {{ $config->tax_active ? 'checked' : '' }}
                                        class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Enable Tax (VAT)</span>
                                </label>
                                <label
                                    class="inline-flex items-center cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                    <input type="checkbox" name="service_active" value="1"
                                        {{ $config->service_active ? 'checked' : '' }}
                                        class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Enable Service Charge</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tax / VAT</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="tax_percent"
                                            value="{{ $config->tax_percent }}"
                                            class="w-full pr-10 rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                            required>
                                        <span class="absolute right-3 top-2.5 text-gray-500 font-medium">%</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Tax percentage automatically applied to each order.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Service Charge</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="service_percent"
                                            value="{{ $config->service_percent }}"
                                            class="w-full pr-10 rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500"
                                            required>
                                        <span class="absolute right-3 top-2.5 text-gray-500 font-medium">%</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Additional service fee per order.</p>
                                </div>
                            </div>
                        </div>

                        <!-- INVENTORY & ORDER -->
                        <div>
                            <h3 class="text-sm font-bold text-amber-600 uppercase tracking-wider mb-4 border-b pb-2">
                                <i class="fas fa-warehouse mr-1"></i> Inventory & Order
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Min. Stock Alert</label>
                                    <input type="number" name="min_stock_alert"
                                        value="{{ $config->min_stock_alert }}" min="0"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-amber-500"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Notification appears if ingredient stock ≤ this number.
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Auto Archive Order
                                        (Days)</label>
                                    <input type="number" name="auto_archive_days"
                                        value="{{ $config->auto_archive_days }}" min="1"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-amber-500"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Order will be automatically archived after X days.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- RECEIPT SETTINGS -->
                        <div>
                            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4 border-b pb-2">
                                <i class="fas fa-receipt mr-1"></i> Receipt Format
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Receipt Header</label>
                                    <input type="text" name="receipt_header"
                                        value="{{ $config->receipt_header }}"
                                        placeholder="Example: Welcome to our store"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Text that appears at the top of the receipt.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Receipt Footer</label>
                                    <input type="text" name="receipt_footer"
                                        value="{{ $config->receipt_footer }}"
                                        placeholder="Example: Thank you for your visit"
                                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Text that appears at the bottom of the receipt.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end border-t border-gray-100">
                            <button type="submit"
                                class="px-8 py-3 bg-slate-800 text-white font-bold rounded-lg shadow-lg hover:bg-slate-900 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                <i class="fas fa-save"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    @include('layout.loading')
</body>

</html>