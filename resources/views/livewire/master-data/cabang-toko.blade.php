<div>

    <div class="p-6 text-gray-200">
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-2">
                <button wire:click="setMode('branch')"
                    class="px-4 py-2 rounded-lg font-semibold {{ $mode === 'branch' ? 'bg-teal-600' : 'bg-gray-700 hover:bg-gray-600' }}">
                    Toko
                </button>
                <button wire:click="setMode('warehouse')"
                    class="px-4 py-2 rounded-lg font-semibold {{ $mode === 'warehouse' ? 'bg-teal-600' : 'bg-gray-700 hover:bg-gray-600' }}">
                    Gudang
                </button>
            </div>

            <div class="relative">
                <input type="text" wire:model.live="search" placeholder="Cari {{ $mode }}..."
                    class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500" />
            </div>
        </div>

        <!-- Form -->
        <div class="bg-gray-900 rounded-xl p-4 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-400">Nama {{ ucfirst($mode) }}</label>
                    <input type="text" wire:model="name"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-teal-500" />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @if($mode === 'warehouse')
                <div>
                    <label class="text-sm text-gray-400">Toko Pemilik</label>
                    <select wire:model="branch_id"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-teal-500">
                        <option value="">Pilih Toko</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
                <div>
                    <label class="text-sm text-gray-400">No. Telepon</label>
                    <input type="text" wire:model="phone"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-teal-500" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="text-sm text-gray-400">Alamat</label>
                    <input type="text" wire:model="address"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-teal-500" />
                </div>
                <div>
                    <label class="text-sm text-gray-400">Keterangan</label>
                    <input type="text" wire:model="description"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-teal-500" />
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                @if ($editingId)
                <button wire:click="update"
                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold text-white">Update</button>
                <button wire:click="resetForm"
                    class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg font-semibold text-white">Batal</button>
                @else
                <button wire:click="create"
                    class="bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-lg font-semibold text-white">Tambah</button>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="bg-gray-900 rounded-xl p-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="text-left py-2 px-2">Nama</th>
                        @if($mode === 'warehouse')
                        <th class="text-left py-2 px-2">Toko</th>
                        @else
                        <th class="text-left py-2 px-2">Telepon</th>

                        @endif
                        <th class="text-left py-2 px-2">Alamat</th>
                        <th class="text-left py-2 px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="border-b border-gray-800 hover:bg-gray-800">
                        <td class="py-2 px-2 font-medium">{{ $item->name }}</td>
                        @if($mode === 'warehouse')
                        <td class="py-2 px-2 text-gray-400">{{ $item->toCabang?->name ?? '-' }}</td>
                        @else
                        <td class="py-2 px-2">{{ $item->phone }}</td>

                        @endif
                        <td class="py-2 px-2">{{ $item->address }}</td>
                        <td class="py-2 px-2 flex gap-2">
                            <button wire:click="edit('{{ $item->id }}')"
                                class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg text-xs">Edit</button>
                            <button wire:click="delete('{{ $item->id }}')"
                                class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg text-xs">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada data {{ $mode }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $items->links() }}</div>
        </div>
    </div>

</div>