<div class="min-h-screen bg-gray-950 text-white flex flex-col">
    <!-- Header -->
    <div class="p-6 border-b border-gray-800 flex items-center justify-between bg-gray-900/60 backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="bg-teal-600 hover:bg-teal-700 p-3 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <button id="fullscreen-btn" class="bg-teal-500 p-3 rounded-xl hover:bg-teal-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </button>
        </div>

        <h1 class="text-xl font-bold text-teal-400 tracking-tight">Halaman Kasir</h1>

        <button wire:click="openCheckout"
            class="bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 p-3 rounded-xl transition font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Checkout
        </button>
    </div>

    <!-- Flash Messages -->
    <div class="p-4">
        @if (session()->has('success'))
        <div
            class="bg-green-600/20 text-green-400 border border-green-500 px-4 py-2 rounded-lg mb-3 text-sm animate-pulse">
            {{ session('success') }}
        </div>
        @endif
        @if (session()->has('error'))
        <div class="bg-red-600/20 text-red-400 border border-red-500 px-4 py-2 rounded-lg mb-3 text-sm animate-pulse">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Main Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 p-2 lg:p-6">
        <!-- Produk -->
        <div class="xl:col-span-2 flex flex-col overflow-hidden">
            <!-- Pencarian -->
            <div class="bg-gray-900/80 rounded-2xl p-5 mb-4 flex gap-4">
                <div class="relative flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, kode, atau barcode produk..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-10 pr-3 py-3 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="relative flex-1">
                    <input type="text" wire:model="barcode" placeholder="Scan barcode..." autofocus
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-10 pr-3 py-3 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" />
                    </svg>
                </div>
            </div>

            <!-- Kategori -->
            <div
                class="bg-gray-900/80 rounded-2xl p-4 flex gap-3 overflow-x-auto mb-4 scrollbar-thin scrollbar-thumb-gray-700">
                <button wire:click="setCategory('all')"
                    class="px-5 py-2.5 rounded-xl whitespace-nowrap font-medium transition {{ $activeCategory === 'all' ? 'bg-teal-600 text-white' : 'bg-gray-800 hover:bg-gray-700 text-gray-300' }}">
                    Semua
                </button>
                @foreach($categories as $category)
                <button wire:click="setCategory('{{ $category->id }}')"
                    class="px-5 py-2.5 rounded-xl whitespace-nowrap font-medium transition {{ $activeCategory == $category->id ? 'bg-teal-600 text-white' : 'bg-gray-800 hover:bg-gray-700 text-gray-300' }}">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Produk List -->
            <div class="bg-gray-900/80 rounded-2xl p-5 flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-800">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($products as $product)
                    @php
                    // Ambil harga default atau harga pertama
                    $defaultPrice = $product->toHarga->where('is_default', true)->first() ?? $product->toHarga->first();
                    $price = $defaultPrice?->price ?? 0;
                    $stock = $product->toStocks->first()?->quantity ?? 0;
                    $hasMultiplePrices = $product->toHarga->count() > 1;
                    @endphp
                    <div wire:click="addToCart('{{ $product->id }}')"
                        class="bg-gray-800 hover:bg-gray-750 border border-gray-700 hover:border-teal-500 hover:scale-105 hover:shadow-xl rounded-xl p-3 cursor-pointer transition-all duration-200 relative group">
                        {{-- <div class="grid grid-cols-2"> --}}

                            {{-- @if($hasMultiplePrices)
                            <div
                                class="absolute top-2 right-8 bg-teal-500 text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">
                                Multi Harga
                            </div>
                            @endif --}}

                            <h3 class="font-medium text-sm line-clamp-2 mb-2 group-hover:text-teal-400 transition">{{
                                $product->name }}</h3>

                            {{--
                        </div> --}}

                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-teal-400 font-bold text-sm">
                                    Rp {{ number_format($price, 0, ',', '.') }}
                                </span>
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full 
                                         {{ $stock > 100 ? 'bg-green-600/20 text-green-400' : ($stock > 10 ? 'bg-amber-400/20 text-amber-300' : 'bg-red-600/20 text-red-400') }}">
                                    {{ $stock }}
                                </span>


                            </div>

                            @if($hasMultiplePrices)
                            <div class="text-[10px] text-gray-400 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ $product->toHarga->count() }} pilihan harga
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty

                    <div class="col-span-full text-center text-gray-500 py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-lg">Tidak ada produk ditemukan</p>
                    </div>

                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-800">
                    <button wire:click="previousPage" @if($products->onFirstPage()) disabled @endif
                        class="px-4 py-2 rounded-xl bg-gray-800 text-gray-300 hover:bg-gray-700 disabled:opacity-50
                        disabled:cursor-not-allowed transition">
                        ← Sebelumnya
                    </button>

                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 text-sm">Hal {{ $products->currentPage() }} dari {{
                            $products->lastPage() }}</span>
                        <select wire:model.live="perPage"
                            class="bg-gray-800 text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 border-0">
                            @foreach([12,24,48,100] as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button wire:click="nextPage" @if(!$products->hasMorePages()) disabled @endif
                        class="px-4 py-2 rounded-xl bg-gray-800 text-gray-300 hover:bg-gray-700 disabled:opacity-50
                        disabled:cursor-not-allowed transition">
                        Selanjutnya →
                    </button>
                </div>
            </div>
        </div>

        <!-- Keranjang -->
        <div class="xl:col-span-1">
            <div class="flex flex-col bg-gray-900/80 rounded-2xl p-5 shadow-lg h-full">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-teal-400 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Keranjang
                        @if(count($cart) > 0)
                        <span class="bg-teal-600 text-white text-xs px-2 py-0.5 rounded-full">{{ count($cart) }}</span>
                        @endif
                    </h2>
                    <button wire:click="resetCart"
                        class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded-lg transition">
                        Reset
                    </button>
                </div>

                <div class="space-y-3 overflow-y-auto flex-1 scrollbar-thin scrollbar-thumb-gray-700">
                    @forelse($cart as $index => $item)
                    <div class="bg-gray-800 p-3 rounded-xl border border-gray-700 hover:border-teal-500/50 transition">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h3 class="font-medium text-sm">{{ $item['name'] }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs bg-teal-600/20 text-teal-400 px-2 py-0.5 rounded">
                                        {{ $item['tier_label'] }}
                                    </span>
                                    @if($item['unit_qty'] > 1)
                                    <span class="text-xs text-gray-400">
                                        ({{ $item['unit_qty'] }} item/unit)
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <button wire:click="removeFromCart({{ $index }})"
                                class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-teal-400 font-bold text-sm">Rp {{ number_format($item['price'], 0, ',',
                                    '.') }}</p>
                                <small class="text-gray-400">Total: Rp {{ number_format($item['price'] *
                                    $item['quantity'], 0, ',', '.') }}</small>
                            </div>

                            <div class="flex items-center gap-2">
                                <button wire:click="updateQuantity({{ $index }}, 'decrement')"
                                    class="bg-gray-700 w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="w-10 text-center font-semibold">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $index }}, 'increment')"
                                    class="bg-teal-600 w-8 h-8 rounded-lg flex items-center justify-center hover:bg-teal-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-16">
                        <svg class="w-20 h-20 mx-auto mb-4 text-gray-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p>Keranjang kosong</p>
                        <p class="text-sm text-gray-600 mt-1">Klik produk untuk menambah</p>
                    </div>
                    @endforelse
                </div>

                <div class="border-t border-gray-700 pt-4 mt-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total Item</span>
                        <span class="font-semibold">{{ array_sum(array_column($cart, 'quantity')) }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold">
                        <span>Total</span>
                        <span class="text-teal-400">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <button wire:click="openCheckout"
                        class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 py-3 rounded-xl font-semibold transition transform hover:scale-[1.02] shadow-lg">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Selection Modal -->
    @if($showPriceModal && $selectedProduct)
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50 transition">
        <div
            class="bg-gray-900/95 rounded-2xl shadow-2xl border border-gray-800 max-w-2xl  p-6 text-white transform transition-all duration-300 scale-100">

            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-teal-400 mb-1">Pilih Harga</h2>
                    <p class="text-gray-400 text-sm">{{ $selectedProduct->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">Stok: {{ $selectedProduct->toStocks->first()?->quantity ?? 0
                        }} item</p>
                </div>
                <button wire:click="closePriceModal" class="p-2 rounded-lg hover:bg-gray-800 transition">
                    <svg class="w-6 h-6 text-gray-400 hover:text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Price Options -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @foreach($availablePrices as $index => $priceOption)
                <button wire:click="addToCartWithPrice({{ $index }})" @if(!$priceOption['available']) disabled @endif
                    class="relative bg-gray-800 border-2 {{ $priceOption['is_default'] ? 'border-teal-500' : 'border-gray-700' }} hover:border-teal-500 rounded-xl p-5 text-left transition-all duration-200 hover:scale-105 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 group">

                    @if($priceOption['is_default'])
                    <div
                        class="absolute top-2 right-2 bg-teal-500 text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">
                        Default
                    </div>
                    @endif

                    @if(!$priceOption['available'])
                    <div class="absolute inset-0 bg-red-600/10 rounded-xl flex items-center justify-center">
                        <span class="bg-red-600 text-white text-xs px-3 py-1 rounded-full font-semibold">Stok Tidak
                            Cukup</span>
                    </div>
                    @endif

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-lg text-teal-400 group-hover:text-teal-300">{{
                                $priceOption['unit_name'] }}</h3>
                            @if($priceOption['unit_qty'] > 1)
                            <span class="text-xs bg-gray-700 px-2 py-1 rounded">{{ $priceOption['unit_qty'] }}
                                item</span>
                            @endif
                        </div>

                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-extrabold text-white">Rp {{ number_format($priceOption['price'],
                                0, ',', '.') }}</span>
                            @if($priceOption['old_price'] && $priceOption['old_price'] > $priceOption['price'])
                            <span class="text-sm text-gray-400 line-through">Rp {{
                                number_format($priceOption['old_price'], 0, ',', '.') }}</span>
                            @endif
                        </div>

                        @if($priceOption['unit_qty'] > 1)
                        <div class="text-xs text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Rp {{ number_format($priceOption['price'] / $priceOption['unit_qty'], 0, ',', '.') }} per
                            item
                        </div>
                        @endif

                        @if($priceOption['notes'])
                        <p class="text-xs text-gray-500 italic">{{ $priceOption['notes'] }}</p>
                        @endif

                        @if($priceOption['available'])
                        <div
                            class="text-xs text-teal-400 font-semibold flex items-center gap-1 mt-2 opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Klik untuk tambahkan
                        </div>
                        @endif
                    </div>
                </button>
                @endforeach
            </div>

            <!-- Info -->
            <div class="bg-blue-600/10 border border-blue-500/30 rounded-lg p-3 text-sm text-blue-300">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pilih harga yang sesuai. Quantity akan otomatis disesuaikan dengan jumlah minimum per
                        unit.</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Checkout Modal -->
    @if($showCheckout)
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50 transition">
        <div
            class="bg-gray-900/95 rounded-2xl shadow-2xl border border-gray-800 max-w-xl w-full p-8 text-white transform transition-all duration-300 scale-100">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-extrabold tracking-tight text-teal-400">Checkout</h2>
                <button wire:click="closeCheckout" class="p-2 rounded-lg hover:bg-gray-800 transition">
                    <svg class="w-6 h-6 text-gray-400 hover:text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-5 rounded-xl text-center shadow-lg">
                    <p class="text-sm text-blue-100 font-medium">Total Belanja</p>
                    <h2 class="text-3xl font-extrabold mt-1 text-white drop-shadow-sm">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </h2>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-500 p-5 rounded-xl text-center shadow-lg">
                    <p class="text-sm text-orange-100 font-medium">Kembalian</p>
                    <h2 class="text-3xl font-extrabold mt-1 text-white drop-shadow-sm">
                        Rp {{ number_format($change, 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            <!-- Form Input -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm text-gray-400 mb-1 block">Nama Customer</label>
                    <input type="text" wire:model="customerName" placeholder="Umum" autocomplete="off"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    @error('customerName') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-400 mb-1 block">Nominal Bayar</label>
                    <input type="text" wire:model.live="paymentAmount" inputmode="numeric" autocomplete="off"
                        onkeypress="return hanyaAngka(event)" placeholder="0"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    @error('paymentAmount') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <label class="text-sm text-gray-400 mb-1 block">Metode Pembayaran</label>
                <select wire:model="paymentMethod" wire:change="updatePaymentMethod()"
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">Pilih metode pembayaran</option>
                    <option value="cash">Tunai</option>
                    <option value="qris">QRIS</option>

                </select>
                @error('paymentMethod') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Quick Payment Buttons -->
            <div class="grid grid-cols-3 gap-3 mb-6">
                @foreach ([50000, 100000, 150000, 200000, 500000, 1000000] as $val)
                <button type="button" wire:click="setPaymentAmount({{ $val }})"
                    class="bg-gray-800 border border-gray-700 hover:border-teal-500 hover:bg-gray-750 rounded-xl py-3 text-sm font-semibold transition duration-200 focus:ring-2 focus:ring-teal-500">
                    {{ number_format($val / 1000, 0) }}K
                </button>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Tombol Batal -->
                <button wire:click="closeCheckout"
                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 font-semibold py-3 rounded-xl transition duration-300 transform hover:scale-[1.02] shadow-md">
                    Batal
                </button>

                <!-- Tombol Bayar Sekarang -->
                <div class="relative">
                    <button wire:click="processPayment" wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 font-semibold py-3 rounded-xl transition duration-300 transform hover:scale-[1.02] shadow-md flex items-center justify-center">
                        <span wire:loading.remove wire:target="processPayment">Bayar Sekarang</span>
                        <span wire:loading wire:target="processPayment" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>

                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.getElementById('fullscreen-btn')?.addEventListener('click', () => {
        if (!document.fullscreenElement) document.documentElement.requestFullscreen();
        else document.exitFullscreen();
    });
    function hanyaAngka(e){const k=e.which||e.keyCode;if(k>31&&(k<48||k>57)){e.preventDefault();return false;}return true;}
</script>
@endpush