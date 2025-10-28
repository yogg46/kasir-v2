<div class="max-w-4xl mx-auto mt-6 space-y-6">

    {{-- notifikasi sederhana --}}
    {{-- <script>
        window.addEventListener('toast', e => alert(e.detail.msg));
    </script> --}}

    {{-- Jika belum buka shift --}}
    @if(!$activeShift)
    <div class="bg-grey-900 border rounded-xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-800 text-lg mb-4">Buka Shift Kasir</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600">Kas Awal</label>
                <input type="number" wire:model="initial_cash" class="mt-1 w-full border-gray-300 rounded-lg p-2"
                    placeholder="0">
                @error('initial_cash') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600">Catatan (opsional)</label>
                <textarea type="text" wire:model="notes" class="mt-1 w-full border-gray-300 rounded-lg p-2"
                    placeholder="Misal: shift pagi"></textarea>
            </div>
        </div>

        <button wire:click="openShift" class="mt-4 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
            Buka Shift
        </button>
    </div>
    @else
    {{-- Jika shift aktif --}}
    <div class="bg-grey-900 border border border-green-200 rounded-xl p-5 shadow-sm">
        <h2 class="font-semibold text-green-800 text-lg mb-3">Shift Aktif</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
            <div>
                <p class="text-gray-600">Mulai</p>
                <p class="font-medium">{{ optional($activeShift->shift_start)->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-600">Kas Awal</p>
                <p class="font-medium">Rp {{ number_format($activeShift->initial_cash,0,',','.') }}</p>
            </div>
            <div>
                <p class="text-gray-600">Masuk</p>
                <p class="font-medium text-green-700">Rp {{ number_format($activeShift->cash_in,0,',','.') }}</p>
            </div>
            <div>
                <p class="text-gray-600">Keluar</p>
                <p class="font-medium text-red-700">Rp {{ number_format($activeShift->cash_out,0,',','.') }}</p>
            </div>
        </div>

        {{-- form cash in/out --}}
        <div class="mt-4 flex flex-wrap gap-2">
            {{-- <input type="number" wire:model="cash_in" placeholder="Tambah Cash In"
                class="border-gray-300 rounded-lg p-2 w-36">
            <button wire:click="addCash('in')"
                class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-3 py-1 text-sm">+ In</button>

            <input type="number" wire:model="cash_out" placeholder="Tambah Cash Out"
                class="border-gray-300 rounded-lg p-2 w-36">
            <button wire:click="addCash('out')"
                class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1 text-sm">+ Out</button> --}}

            <div>
                <label class="block text-sm text-gray-600">Catatan (opsional)</label>
                <textarea type="text" wire:model="notes" class="mt-1 w-full  border-gray-900 rounded-lg p-2"
                    placeholder="Misal: shift pagi"></textarea>
            </div>

            <button wire:click="closeShift"
                class="bg-amber-500 hover:bg-amber-600 text-white rounded-lg px-4 py-1 text-sm ml-auto">
                Tutup Shift
            </button>
        </div>
    </div>
    @endif

    {{-- Riwayat shift --}}


    <div class="bg-gray-900 rounded-lg shadow-sm overflow-hidden px-4 py-4">
        <h3 class="font-semibold text-gray-800 mb-3">Riwayat Shift</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>

                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                            Open</th>
                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                            Close</th>
                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                            Kasir</th>
                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-right">
                            Awal</th>
                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-right">In
                        </th>
                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-right">Out
                        </th>
                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-right">
                            Akhir</th>
                        <th class="px-6 py-3  text-xs font-medium text-gray-900 uppercase tracking-wider text-center">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-200">
                    @forelse($shifts as $s)
                    <tr class="hover:bg-zinc-800 transition">
                        <td class="py-2">{{ optional($s->shift_start)->format('d M y H:i') }}</td>
                        <td class="py-2">{{ optional($s->shift_end)->format('d M y H:i') }}</td>
                        <td>{{ $s->toKasir->name ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($s->initial_cash,0,',','.') }}</td>
                        <td class="text-right">Rp {{ number_format($s->cash_in,0,',','.') }}</td>
                        <td class="text-right">Rp {{ number_format($s->cash_out,0,',','.') }}</td>
                        <td class="text-right">Rp {{ number_format($s->final_cash,0,',','.') }}</td>
                        <td class="text-center">
                            <span
                                class="px-2 py-1 text-xs rounded-lg
                                {{ $s->status === 'open' ? 'bg-green-100 text-gray-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ strtoupper($s->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">Belum ada data shift.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $shifts->links() }}</div>
        </div>
    </div>
</div>
