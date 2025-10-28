<div>
    <div class="container mx-auto px-6 py-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                    Manajemen {{ $mode === 'branch' ? 'Cabang Toko' : 'Gudang' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Kelola data {{ $mode === 'branch' ? 'cabang toko' : 'gudang' }} dan informasi terkait
                </p>
            </div>

            {{-- Mode Toggle --}}
            <div class="flex gap-2">
                <button wire:click="setMode('branch')"
                    class="px-4 py-2 rounded-lg font-semibold transition-all {{ $mode === 'branch' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600' }}">
                    🏢 Cabang Toko
                </button>
                <button wire:click="setMode('warehouse')"
                    class="px-4 py-2 rounded-lg font-semibold transition-all {{ $mode === 'warehouse' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600' }}">
                    📦 Gudang
                </button>
            </div>
        </div>

        {{-- Search --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari {{ $mode === 'branch' ? 'cabang' : 'gudang' }} (nama, kode, alamat)..."
                    class="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                {{ $editingId ? 'Edit' : 'Tambah' }} {{ $mode === 'branch' ? 'Cabang' : 'Gudang' }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div class="form-group">
                    <label class="form-label">
                        Nama {{ $mode === 'branch' ? 'Cabang' : 'Gudang' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="name" class="form-input"
                        placeholder="Masukkan nama {{ $mode === 'branch' ? 'cabang' : 'gudang' }}">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Toko (untuk gudang) --}}
                @if($mode === 'warehouse')
                <div class="form-group">
                    <label class="form-label">Cabang Toko <span class="text-red-500">*</span></label>
                    <select wire:model="branch_id" class="form-select">
                        <option value="">Pilih Cabang</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">
                            {{ $branch->name }} {{ $branch->is_head_office ? '(Pusat)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('branch_id') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Telepon (untuk cabang) --}}
                @if($mode === 'branch')
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" wire:model="phone" class="form-input" placeholder="Contoh: 0812-3456-7890">
                    @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                @endif

                {{-- Alamat --}}
                <div class="form-group {{ $mode === 'branch' ? 'md:col-span-2' : '' }}">
                    <label class="form-label">Alamat</label>
                    <input type="text" wire:model="address" class="form-input" placeholder="Masukkan alamat lengkap">
                    @error('address') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Checkbox Options --}}
                <div class="md:col-span-2 space-y-2">
                    @if($mode === 'branch')
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_head_office"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Set sebagai Cabang Pusat
                        </span>
                    </label>
                    @else
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_main"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Set sebagai Gudang Utama (untuk cabang ini)
                        </span>
                    </label>
                    @endif
                </div>
            </div>

            {{-- Buttons --}}
            <div class="mt-6 flex gap-3">
                @if ($editingId)
                <button wire:click="update" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update
                </button>
                <button wire:click="resetForm" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                @else
                <button wire:click="create" class="btn btn-primary">
                    Tambah {{ $mode === 'branch' ? 'Cabang' : 'Gudang' }}
                </button>
                @endif
            </div>
        </div>

        {{-- === Header Tabs === --}}
        <div class="flex space-x-2  border-b border-gray-700">
            <button wire:click="toggleTrashed" class="px-4 py-2 font-medium text-sm rounded-t-lg transition
            {{ !$showTrashed   
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-800 text-gray-400 hover:text-gray-200' }}">
                🔹 Data Aktif
            </button>
            <button wire:click="toggleTrashed" class="px-4 py-2 font-medium text-sm rounded-t-lg transition
            {{ $showTrashed   
                ? 'bg-red-600 text-white' 
                : 'bg-gray-800 text-gray-400 hover:text-gray-200' }}">
                🗃️ Arsip
            </button>
        </div>
        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kode
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama
                            </th>
                            @if($mode === 'warehouse')
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Cabang Toko
                            </th>
                            @else
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Telepon
                            </th>
                            @endif
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Alamat
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $item->code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $item->name }}
                                </div>
                            </td>
                            @if($mode === 'warehouse')
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->toCabang?->name ?? '-' }}
                            </td>
                            @else
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->phone ?? '-' }}
                            </td>
                            @endif
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->address ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($mode === 'branch')
                                @if($item->is_head_office)
                                <span class="badge badge-info">🏛️ Pusat</span>
                                @else
                                <span class="badge badge-gray">📍 Cabang</span>
                                @endif
                                @else
                                @if($item->is_main)
                                <span class="badge badge-success">⭐ Utama</span>
                                @else
                                <span class="badge badge-gray">📦 Gudang</span>
                                @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if ($showTrashed)
                                <button wire:click="restore('{{ $item->id }}')"
                                    class="text-green-600 hover:text-green-900 dark:hover:text-green-400 mr-3"
                                    title="Pulihkan">
                                    ♻️
                                </button>
                                <button wire:click="confirmForceDelete('{{ $item->id }}')"
                                    class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                    title="Hapus Permanen">
                                    ❌
                                </button>
                                @else
                                <button wire:click="edit('{{ $item->id }}')"
                                    class="text-yellow-600 hover:text-yellow-900 mr-3" title="Edit">
                                    ✏️
                                </button>
                                <button wire:click="confirmDelete('{{ $item->id }}')"
                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                    🗑️
                                </button>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="mt-2">Tidak ada data {{ $mode === 'branch' ? 'cabang' : 'gudang' }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>