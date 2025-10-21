<div class="min-h-screen bg-gray-950 text-white flex flex-col">
    <!-- Header -->
    <div class="p-6 border-b border-gray-800 flex items-center justify-between bg-gray-900/60 backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <!-- Back -->
            <a href="{{ route('dashboard') }}" class="bg-teal-600 hover:bg-teal-700 p-3 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <!-- Fullscreen -->
            <button id="fullscreen-btn" class="bg-teal-500 p-3 rounded-xl hover:bg-teal-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </button>
        </div>

        <h1 class="text-xl font-bold text-teal-400 tracking-tight">Halaman Kasir</h1>

        <!-- Checkout -->
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
        <div class="bg-green-600/20 text-green-400 border border-green-500 px-4 py-2 rounded-lg mb-3 text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if (session()->has('error'))
        <div class="bg-red-600/20 text-red-400 border border-red-500 px-4 py-2 rounded-lg mb-3 text-sm">
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
                    <input type="text" wire:model.live="search" placeholder="Cari nama, kode, atau barcode produk..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-10 pr-3 py-3 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="relative flex-1">
                    <input type="text" wire:model="barcode" placeholder="Scan barcode..."
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
                <button wire:click="setCategory({{ $category->id }})"
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
                    $price = $product->toPrices->first()?->price ?? 0;
                    $stock = $product->toStocks->first()?->quantity ?? 0;
                    @endphp
                    <div wire:click="addToCart('{{ $product->id }}')"
                        class="bg-gray-800 hover:bg-gray-750 border border-gray-700 hover:border-teal-500 hover:scale-105 hover:shadow-lg rounded-xl p-3 cursor-pointer transition-transform duration-300">
                        <h3 class="font-medium text-sm line-clamp-2 mb-1">{{ $product->name }}</h3>
                        <div class="flex justify-between items-center">
                            <span class="text-teal-400 font-bold text-sm">
                                Rp {{ number_format($price, 0, ',', '.') }}
                            </span>
                            <span class="text-gray-400 text-xs">({{ $stock }})</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-5 text-center text-gray-500 py-8">Tidak ada produk ditemukan</div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-6">
                    <button wire:click="previousPage" @if($products->onFirstPage()) disabled @endif
                        class="px-4 py-2 rounded-xl bg-gray-800 text-gray-300 hover:bg-gray-700 disabled:opacity-50
                        disabled:cursor-not-allowed transition">
                        Sebelumnya
                    </button>

                    <div class="flex items-center gap-3">
                        <span class="text-gray-400">Halaman {{ $products->currentPage() }} dari {{ $products->lastPage()
                            }}</span>
                        <select wire:model.live="perPage"
                            class="bg-gray-800 text-white rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-500">
                            @foreach([8,12,24,48,100,200] as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button wire:click="nextPage" @if(!$products->hasMorePages()) disabled @endif
                        class="px-4 py-2 rounded-xl bg-gray-800 text-gray-300 hover:bg-gray-700 disabled:opacity-50
                        disabled:cursor-not-allowed transition">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>

        <!-- Keranjang -->
        <div class="xl:col-span-1">
            <div class="flex flex-col bg-gray-900/80 rounded-2xl p-5 shadow-lg h-full">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-teal-400">Keranjang</h2>
                    <button wire:click="resetCart"
                        class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded-lg transition">
                        Reset
                    </button>
                </div>

                <div class="space-y-3 overflow-y-auto flex-1 scrollbar-thin scrollbar-thumb-gray-700">
                    @forelse($cart as $index => $item)
                    <div class="bg-gray-800 p-3 rounded-xl border border-gray-700 flex gap-3">
                        {{-- <div class="w-16 h-16 rounded-lg bg-white overflow-hidden flex-shrink-0">
                            <img src="{{ (isset($item['image']) && Storage::disk('public')->exists($item['image'])) ? asset('storage/' . $item['image']) : asset('images/default-product.png') }}"
                                class="object-cover w-full h-full" />
                        </div> --}}
                        <div class="flex-1">
                            <h3 class="font-medium text-sm">{{ $item['name'] }}</h3>
                            <p class="text-teal-400 font-bold text-sm">Rp {{ number_format($item['price'], 0, ',', '.')
                                }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <small class="text-gray-400">Subtotal: Rp {{ number_format($item['price'] *
                                    $item['quantity'], 0, ',', '.') }}</small>
                                <div class="flex items-center gap-1">
                                    <button wire:click="updateQuantity({{ $index }}, 'decrement')"
                                        class="bg-gray-700 w-7 h-7 rounded-md flex items-center justify-center hover:bg-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-7 text-center">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $index }}, 'increment')"
                                        class="bg-teal-600 w-7 h-7 rounded-md flex items-center justify-center hover:bg-teal-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-10">Keranjang kosong</div>
                    @endforelse
                </div>

                <div class="border-t border-gray-700 pt-3 mt-3">
                    <div class="flex justify-between text-lg font-semibold mb-3">
                        <span>Total</span>
                        <span class="text-teal-400">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <button wire:click="openCheckout"
                        class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 py-3 rounded-xl font-semibold transition transform hover:scale-[1.02]">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                    <label class="text-sm text-gray-400">Nama Customer</label>
                    <input type="text" wire:model="customerName" placeholder="Umum" autocomplete="off"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 mt-1 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    @error('customerName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-400">Nominal Bayar</label>
                    <input id="paymentAmountInput" type="text" wire:model.live="paymentAmount" inputmode="numeric"
                        wire:key="paymentAmount-{{ $paymentAmount }}" autocomplete="off"
                        onkeypress="return hanyaAngka(event)" placeholder="0"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 mt-1 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    @error('paymentAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <label class="text-sm text-gray-400">Metode Pembayaran</label>
                <select wire:model="paymentMethod"
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 mt-1 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">Pilih metode pembayaran</option>
                    <option value="cash">Tunai</option>
                    <option value="qris">QRIS</option>

                </select>
                @error('paymentMethod') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Quick Payment Buttons -->
            <div class="grid grid-cols-3 gap-3 mb-6">
                @foreach ([50000, 100000, 150000, 200000, 500000, 1000000] as $val)
                <button type="button" wire:click="setPaymentAmount({{ $val }})"
                    class="bg-gray-800 border border-gray-700 hover:border-teal-500 hover:bg-gray-750 rounded-xl py-3 text-sm font-semibold transition duration-200 focus:ring-2 focus:ring-teal-500">
                    {{ number_format($val, 0, ',', '.') }}
                </button>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-4">
                <button wire:click="closeCheckout"
                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 font-semibold py-3 rounded-xl transition duration-300 transform hover:scale-[1.02] shadow-md">
                    Batal
                </button>
                <button wire:click="processPayment"
                    class="bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 font-semibold py-3 rounded-xl transition duration-300 transform hover:scale-[1.02] shadow-md">
                    Bayar
                </button>
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