<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Manajemen Supplier
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola data supplier Anda
            </p>
        </div>

    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- Search --}}
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari kode, nama, alamat, atau telepon..." class="form-input w-full">
                </div>

                {{-- Per Page
                <div class="w-full sm:w-32">
                    <select wire:model.live="perPage" class="form-select w-full">
                        <option value="10">10 / halaman</option>
                        <option value="25">25 / halaman</option>
                        <option value="50">50 / halaman</option>
                        <option value="100">100 / halaman</option>
                    </select>
                </div> --}}

                <div class="w-full sm:w-32">
                    <button wire:click="create" class="btn btn-primary">
                        
                        Tambah Supplier
                    </button>
                </div>


            </div>
        </div>
    </div>

    {{-- Table --}}
    {{-- === Header Tabs === --}}

    <div>

        <div class="flex space-x-2  border-b border-gray-700">
            <button wire:click="toggleShowDeleted" class="px-4 py-2 font-medium text-sm rounded-t-lg transition
            {{ !$showDeletedOnly   
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-800 text-gray-400 hover:text-gray-200' }}">
                🔹 Data Aktif
            </button>
            <button wire:click="toggleShowDeleted" class="px-4 py-2 font-medium text-sm rounded-t-lg transition
            {{ $showDeletedOnly   
                ? 'bg-red-600 text-white' 
                : 'bg-gray-800 text-gray-400 hover:text-gray-200' }}">
                🗃️ Arsip
            </button>
        </div>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('code')"
                                class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-2">
                                    Kode
                                    @if($sortField === 'code')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                        @endif
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('name')"
                                class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-2">
                                    Nama Supplier
                                    @if($sortField === 'name')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                        @endif
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th wire:click="sortBy('created_at')"
                                class="cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center gap-2">
                                    Ditambahkan
                                    @if($sortField === 'created_at')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                        @endif
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->suppliers as $supplier)
                        <tr>
                            <td class="font-mono text-sm">
                                {{ $supplier->code }}
                            </td>
                            <td class="font-medium">
                                {{ $supplier->name }}
                                @if($supplier->deleted_at)
                                <span class="badge badge-danger ml-2">Terhapus</span>
                                @endif
                            </td>
                            <td class="text-sm">
                                {{ Str::limit($supplier->address ?? '-', 40) }}
                            </td>
                            <td class="text-sm">
                                {{ $supplier->phone ?? '-' }}
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $supplier->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    @if($supplier->deleted_at)
                                    {{-- Restore Button --}}
                                    <button wire:click="restore('{{ $supplier->id }}')"
                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                                        title="Pulihkan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>

                                    {{-- Force Delete Button --}}
                                    <button wire:click="forceDelete('{{ $supplier->id }}')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        title="Hapus Permanen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    @else
                                    {{-- Edit Button --}}
                                    <button wire:click="edit('{{ $supplier->id }}')"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button wire:click="confirmDelete('{{ $supplier->id }}')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data supplier</p>
                                    <p class="text-sm mt-1">
                                        @if($search)
                                        Coba ubah kata kunci pencarian Anda
                                        @else
                                        Klik tombol "Tambah Supplier" untuk mulai menambahkan data
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($this->suppliers->hasPages())
            <div class="card-body border-t border-gray-200 dark:border-gray-700">
                {{ $this->suppliers->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Modal Form --}}
    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal"></div>
    <div class="modal-container">
        <div class="modal-panel">
            {{-- Header --}}
            <div class="modal-header">
                <h3 class="text-xl font-semibold">
                    {{ $isEdit ? 'Edit Supplier' : 'Tambah Supplier' }}
                </h3>
                <button wire:click="closeModal" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                <form wire:submit="save" class="space-y-6">
                    {{-- Code (Read Only) --}}
                    @if($isEdit)
                    <div class="form-group">
                        <label class="form-label">Kode</label>
                        <input type="text" class="form-input bg-gray-100 dark:bg-gray-700" value="{{ $code }}" disabled>
                        <p class="text-xs text-gray-500 mt-1">Kode supplier dibuat otomatis oleh sistem</p>
                    </div>
                    @endif

                    {{-- Name --}}
                    <div class="form-group">
                        <label class="form-label">
                            Nama Supplier <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name" class="form-input @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama supplier" autofocus>
                        @error('name')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea wire:model="address" rows="3"
                            class="form-textarea @error('address') border-red-500 @enderror"
                            placeholder="Masukkan alamat lengkap supplier"></textarea>
                        @error('address')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" wire:model="phone"
                            class="form-input @error('phone') border-red-500 @enderror"
                            placeholder="Contoh: 08123456789 atau +62 821 2345 6789">
                        @error('phone')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" wire:click="closeModal" class="btn btn-outline">
                    Batal
                </button>
                <button type="button" wire:click="save" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $isEdit ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>