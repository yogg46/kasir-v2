<div>
    <div class="p-6 text-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold text-teal-400">Manajemen User</h1>
            <input type="text" wire:model.live="search" placeholder="Cari user..."
                class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
        </div>

        <!-- Form -->
        <div class="bg-gray-900 rounded-xl p-5 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-400">Nama</label>
                    <input type="text" wire:model="name"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                {{-- <div>
                    <label class="text-sm text-gray-400">Email</label>
                    <input type="email" wire:model="email"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div> --}}

                <div>
                    <label class="text-sm text-gray-400">Username</label>
                    <input type="text" wire:model="username"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1">
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-400">Password @if($editingId)<span
                            class="text-gray-500 text-xs">(kosongkan jika tidak diubah)</span>@endif</label>
                    <input type="password" wire:model="password"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-sm text-gray-400">Role</label>
                    <select wire:model="role_id"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->role }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-sm text-gray-400">Cabang (Opsional)</label>
                    <select wire:model="branch_id"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 mt-1">
                        <option value="">Pilih Cabang</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                @if($editingId)
                <button wire:click="update"
                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold">Update</button>
                <button wire:click="resetForm"
                    class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg font-semibold">Batal</button>
                @else
                <button wire:click="create"
                    class="bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-lg font-semibold">Tambah</button>
                @endif
            </div>
        </div>

        <!-- Table -->
        {{-- === Header Tabs === --}}
        <div class="flex space-x-2  border-b border-gray-700">
            <button wire:click="toggleTrashed" class="px-4 py-2 font-medium text-sm rounded-t-lg transition
            {{ !$showTrashed   
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-800 text-gray-400 hover:text-gray-200' }}">
                🔹 Data Aktif
            </button>
            <button  wire:click="toggleTrashed" class="px-4 py-2 font-medium text-sm rounded-t-lg transition
            {{ $showTrashed   
                ? 'bg-red-600 text-white' 
                : 'bg-gray-800 text-gray-400 hover:text-gray-200' }}">
                🗃️ Arsip
            </button>
        </div>
        <div class="bg-gray-900 rounded-xl p-4">
          
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="py-2 text-left">Nama</th>
                        {{-- <th class="py-2 text-left">Email</th> --}}
                        <th class="py-2 text-left">Username</th>
                        <th class="py-2 text-left">Role</th>
                        <th class="py-2 text-left">Cabang</th>
                        <th class="py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr class="border-b border-gray-800 hover:bg-gray-800">
                        <td class="py-2">{{ $u->name }}</td>
                        {{-- <td class="py-2 text-gray-400">{{ $u->email }}</td> --}}
                        <td class="py-2">{{ $u->username }}</td>
                        <td class="py-2 text-teal-400">{{ $u->toRole?->role ?? '-' }}</td>
                        <td class="py-2 text-gray-400">{{ $u->toCabang?->name ?? '-' }}</td>
                        <td class="py-2 flex gap-2">
                            @if ($showTrashed)
                            <button wire:click="restore('{{ $u->id }}')"
                                class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded-lg text-xs">Pulihkan</button>
                            <button wire:click="confirmForceDelete('{{ $u->id }}')"
                                class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg text-xs">Hapus Permanen</button>
                            @else
                            <button wire:click="edit('{{ $u->id }}')"
                                class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-lg text-xs">Edit</button>
                            <button wire:click="confirmDelete('{{ $u->id }}')"
                                class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg text-xs">Hapus</button>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">Tidak ada user ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $users->links() }}</div>
        </div>
    </div>

</div>