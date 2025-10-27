<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 ">
        <div>
            <h1 class="text-3xl font-bold  ">Manajemen Produk</h1>
            <p class="text-gray-600 mt-1">Kelola produk, harga multi-level, dan diskon</p>
        </div>
        <button wire:click="create"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Produk
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-800/80 rounded-xl shadow-md p-4 mb-6 backdrop-blur">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari produk (nama, kode, barcode)..."
                    class="w-full px-4 py-2 border bg-gray-900/60 border-gray-700 text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <select wire:model.live="filterCategory"
                    class="w-full px-4 py-2 border bg-gray-900/60 border-gray-700 text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterType"
                    class="w-full px-4 py-2 border bg-gray-900/60 border-gray-700 text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="regular">Regular</option>
                    <option value="umkm">UMKM</option>
                    <option value="seasonal">Musiman</option>
                </select>
            </div>
            <div>
                <select wire:model.live="filterStatus"
                    class="w-full px-4 py-2 border bg-gray-900/60 border-gray-700 text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-gray-900 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Kode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                            Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                            Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Harga
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">Stok
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-900 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-zinc-800 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                            {{ $product->code }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-white">{{ $product->name }}</div>
                            @if($product->barcode)
                            <div class="text-xs text-gray-500">{{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->toKategori->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $product->type === 'umkm' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $product->type === 'seasonal' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $product->type === 'regular' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($product->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            @php
                            $defaultPrice = $product->toHarga->where('is_default', true)->first();
                            @endphp
                            @if($defaultPrice)
                            <div class="font-semibold">Rp {{ number_format($defaultPrice->price, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">{{ $defaultPrice->unit_name }}</div>
                            @else
                            <span class="text-gray-400">Belum diset</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                            $totalStock = $product->toStocks->sum('quantity');
                            @endphp
                            <span class="font-semibold {{ $totalStock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $totalStock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="view('{{ $product->id }}')"
                                class="text-blue-600 hover:text-blue-900 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <button wire:click="edit('{{ $product->id }}')"
                                class="text-yellow-600 hover:text-yellow-900 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="delete('{{ $product->id }}')"
                                wire:confirm="Yakin ingin menghapus produk ini?"
                                class="text-red-600 hover:text-red-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-2">Tidak ada produk ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div
            class="px-6 py-4 border-t border-gray-800 bg-[#0e1525] text-gray-300 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Per Page Selector --}}
            <div class="flex items-center gap-2">
                <label for="perPage" class="text-sm">Show</label>
                <select wire:model.live="perPage" id="perPage"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-md px-2 py-1 focus:ring focus:ring-blue-500/40">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-sm">entries</span>
            </div>

            {{-- Info dan Pagination --}}
            <div class="flex items-center justify-between gap-4 w-full sm:w-auto">
                <div class="text-sm">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }}
                    results
                </div>

                <div>
                    {{ $products->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 bg-gray-900/80 backdrop-blur-sm"
        aria-modal="true" role="dialog">
        {{-- Panel --}}
        <div x-transition x-show="show" @click.stop
            class="bg-gray-800 text-gray-100 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[80vh] flex flex-col relative overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold">
                    @if($modalMode === 'create') Tambah Produk Baru
                    @elseif($modalMode === 'edit') Edit Produk
                    @else Detail Produk
                    @endif
                </h3>
                <button wire:click="closeModal" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Tabs --}}
            @if (!$modalMode === 'create')

            <div class="border-b border-gray-700 bg-gray-900/60  ">
                <nav class="flex space-x-2 px-6">
                    <button wire:click="$set('activeTab', 'info'); $set('newPrice.branch_id', null)" class="py-3 px-5 text-sm font-medium rounded-t-lg transition
        {{ $activeTab === 'info'
            ? 'bg-blue-600 text-white'
            : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/50' }}">
                        📦 Info Produk
                    </button>

                    <button wire:click="$set('activeTab', 'prices'); $set('newPrice.branch_id', 'all')" class="py-3 px-5 text-sm font-medium rounded-t-lg transition
        {{ $activeTab === 'prices'
            ? 'bg-blue-600 text-white'
            : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/50' }}" @if($modalMode==='create' ) disabled @endif>
                        💰 Harga Multi-Level
                    </button>

                    <button wire:click="$set('activeTab', 'discounts'); $set('newPrice.branch_id', null)" class="py-3 px-5 text-sm font-medium rounded-t-lg transition
        {{ $activeTab === 'discounts'
            ? 'bg-blue-600 text-white'
            : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700/50' }}" @if($modalMode==='create' ) disabled @endif>
                        🏷️ Diskon
                    </button>

                </nav>
            </div>
            @endif


            {{-- Body --}}
            <div class="p-6 pb-8 max-h-[80vh] overflow-y-auto bg-gray-900/80">


                {{-- === TAB INFO === --}}
                @if($activeTab === 'info')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Kategori</label>
                        <select wire:model="category_id"
                            class="w-full px-4 py-2 border bg-gray-900/60 border-gray-700 text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Nama Produk</label>
                        <input type="text" wire:model="name" class="w-full border rounded-lg p-2"
                            placeholder="Nama produk...">
                        @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Deskripsi</label>
                        <textarea wire:model="description" class="w-full border rounded-lg p-2" rows="3"></textarea>
                        @error('description')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Barcode</label>
                            <input type="text" wire:model="barcode" class="w-full border rounded-lg p-2">
                            @error('barcode')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Tipe Produk</label>
                            <select wire:model="type"
                                class="w-full px-4 py-2 border bg-gray-900/60 border-gray-700 text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="regular">Regular</option>
                                <option value="umkm">UMKM</option>
                                <option value="seasonal">Musiman</option>
                            </select>
                            @error('type')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Status</label>
                        <select wire:model="is_active"
                            class="w-full px-4 py-2 border bg-gray-900/60 border-gray-700 text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Status --</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                        @error('is_active')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Catatan</label>
                        <textarea wire:model="notes" class="w-full border rounded-lg p-2" rows="2"></textarea>
                        @error('notes')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- === TAB HARGA === --}}
                @elseif($activeTab === 'prices')
                <div class="space-y-4">
                    @if($modalMode !== 'view')
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold mb-2">Tambah Harga Baru</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-sm">Cabang</label>
                                <select wire:model="newPrice.branch_id"
                                    class="w-full bg-gray-900/60 border-gray-700 text-gray-100 border rounded-lg p-2">
                                    <option value="">-- Pilih Cabang --</option>
                                    <option value="all">Semua Cabang</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('newPrice.branch_id')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm">Satuan</label>
                                <input type="text" wire:model="newPrice.unit_name" class="w-full border rounded-lg p-2">
                                @error('newPrice.unit_name')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm">Qty</label>
                                <input type="number" wire:model="newPrice.unit_qty"
                                    class="w-full border rounded-lg p-2">
                                @error('newPrice.unit_qty')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm">Harga Beli</label>
                                <input type="number" wire:model="newPrice.purchase_price"
                                    class="w-full border rounded-lg p-2">
                                @error('newPrice.purchase_price')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm">Harga Jual</label>
                                <input type="number" wire:model="newPrice.price" class="w-full border rounded-lg p-2">
                                @error('newPrice.price')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-end">
                                <button wire:click="addPrice"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <table class="w-full border rounded-lg text-sm">
                        <thead>
                            <tr>
                                <th class="p-2 text-left">Cabang</th>
                                <th class="p-2 text-left">Satuan</th>
                                <th class="p-2 text-center">Qty</th>
                                <th class="p-2 text-right">Beli</th>
                                <th class="p-2 text-right">Jual</th>
                                <th class="p-2 text-center">Default</th>
                                <th class="p-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prices as $index => $price)
                            <tr class="border-t">
                                <td class="p-2">{{ $price['branch_name'] ?? '-' }}</td>
                                <td class="p-2">{{ $price['unit_name'] }}</td>
                                <td class="p-2 text-center">{{ $price['unit_qty'] }}</td>
                                <td class="p-2 text-right">{{ number_format($price['purchase_price'], 0, ',', '.') }}
                                </td>
                                <td class="p-2 text-right">{{ number_format($price['price'], 0, ',', '.') }}</td>
                                <td class="p-2 text-center">
                                    @if($price['is_default'])
                                    ✅
                                    @else
                                    <button wire:click="setDefaultPrice({{ $index }}, '{{ $price['id'] ?? '' }}')"
                                        class="text-blue-500 hover:underline">Set</button>
                                    @endif
                                </td>
                                <td class="p-2 text-center">
                                    <button wire:click="deletePrice({{ $index }}, '{{ $price['id'] ?? '' }}')"
                                        class="text-red-500 hover:underline">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center p-3 text-gray-500">Belum ada harga</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- === TAB DISKON === --}}
                @elseif($activeTab === 'discounts')
                <div class="space-y-4">
                    @if($modalMode !== 'view')
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold mb-2">Tambah Diskon</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-sm">Cabang</label>
                                <select wire:model="newDiscount.branch_id"
                                    class="w-full bg-gray-900/60 border-gray-700 text-gray-100 border rounded-lg p-2">
                                    <option value="">Semua Cabang</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('newDiscount.branch_id')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm">Tipe</label>
                                <select wire:model="newDiscount.type"
                                    class="w-full bg-gray-900/60 border-gray-700 text-gray-100 border rounded-lg p-2">
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="item">Per Item</option>
                                    <option value="transaction">Transaksi</option>
                                </select>
                                @error('newDiscount.type')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm">% Diskon</label>
                                <input type="number" wire:model="newDiscount.discount_percent"
                                    class="w-full border rounded-lg p-2">
                                @error('newDiscount.discount_percent')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm">Nominal (Rp)</label>
                                <input type="number" wire:model="newDiscount.discount_amount"
                                    class="w-full border rounded-lg p-2">
                                @error('newDiscount.discount_amount')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-end">
                                <button wire:click="addDiscount"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <table class="w-full border border-gray-700 rounded-lg text-sm">
                        <thead>
                            <tr>
                                <th class="p-2 text-left">Cabang</th>
                                <th class="p-2 text-left">Tipe</th>
                                <th class="p-2 text-right">% Diskon</th>
                                <th class="p-2 text-right">Nominal</th>
                                <th class="p-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discounts as $index => $discount)
                            <tr class="border-t">
                                <td class="p-2">{{ $discount['branch_name'] ?? '-' }}</td>
                                <td class="p-2 capitalize">{{ $discount['type'] }}</td>
                                <td class="p-2 text-right">{{ $discount['discount_percent'] ?? '-' }}</td>
                                <td class="p-2 text-right">{{ number_format($discount['discount_amount'], 0, ',', '.')
                                    }}</td>
                                <td class="p-2 text-center">
                                    <button wire:click="deleteDiscount({{ $index }}, '{{ $discount['id'] ?? '' }}')"
                                        class="text-red-500 hover:underline">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center p-3 text-gray-500">Belum ada diskon</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 bg-gray-800 px-6 py-4 border-t border-gray-700">
                <button wire:click="closeModal"
                    class="px-5 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">
                    Tutup
                </button>

                @if($modalMode !== 'view')
                <div class="relative">
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif





    {{-- Notifikasi Toast --}}
    <div x-data="{ show: false, message: '', type: 'success' }" x-on:notify.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type ?? 'success';
        setTimeout(() => show = false, 3000);
    " x-show="show" x-transition.opacity.duration.500ms class="fixed top-4 right-4 z-50">
        <div :class="{
            'bg-green-500': type === 'success',
            'bg-red-500': type === 'error',
            'bg-yellow-500': type === 'warning',
        }" class="text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[250px]">
            <!-- Icon dinamis -->
            <template x-if="type === 'success'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </template>

            <template x-if="type === 'error'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>

            <template x-if="type === 'warning'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M12 5a7 7 0 100 14a7 7 0 000-14z" />
                </svg>
            </template>

            <span class="font-medium" x-text="message"></span>
        </div>
    </div>

</div>

{{-- Alpine.js & Livewire Scripts (pastikan sudah di layout) --}}
@push('scripts')
<script>
    // window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Tes berhasil!', type: 'success' } }))

    // Auto focus first input when modal opens
    
</script>
@endpush