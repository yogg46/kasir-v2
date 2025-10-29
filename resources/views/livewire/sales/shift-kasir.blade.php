<div class="container mx-auto px-6 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Manajemen Shift Kasir</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola shift kasir dan laporan penjualan</p>
    </div>

    {{-- Jika belum buka shift --}}
    @if(!$activeShift)
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">🔓 Buka Shift Kasir</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="form-label">Kas Awal <span class="text-red-500">*</span></label>
                <input type="number" wire:model="initial_cash" class="form-input" placeholder="Masukkan kas awal"
                    min="0" step="1000">
                @error('initial_cash') <span class="form-error">{{ $message }}</span> @enderror
            </div>
        </div>

        <button wire:click="openShift" class="mt-4 btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
            </svg>
            Buka Shift
        </button>
    </div>
    @else
    {{-- Jika shift aktif --}}
    <div
        class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 border border-green-200 dark:border-gray-600 rounded-lg p-6 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                <span class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                Shift Aktif
            </h2>
            <button wire:click="closeShift"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold transition-all flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Tutup Shift
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Mulai</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ optional($activeShift->shift_start)->format('d M Y H:i') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Kas Awal</p>
                <p class="text-lg font-bold text-gray-800 dark:text-gray-200">
                    Rp {{ number_format($activeShift->initial_cash, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Kas Masuk</p>
                <p class="text-lg font-bold text-green-600">
                    Rp {{ number_format($activeShift->cash_in, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-700 rounded-lg p-4">
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Kas Keluar</p>
                <p class="text-lg font-bold text-red-600">
                    Rp {{ number_format($activeShift->cash_out, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="mt-4 bg-white dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Kas Akhir</p>
            <p class="text-2xl font-bold text-blue-600">
                Rp {{ number_format($activeShift->initial_cash + $activeShift->cash_in - $activeShift->cash_out, 0, ',',
                '.') }}
            </p>
        </div>
    </div>
    @endif

    {{-- Riwayat Shift --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">📋 Riwayat Shift</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tanggal Buka
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tanggal Tutup
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Kasir
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Kas Awal
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Masuk
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Keluar
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Kas Akhir
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($shifts as $s)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ optional($s->shift_start)->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $s->shift_end ? $s->shift_end->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $s->toKasir->name ?? '-' }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($s->initial_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600">
                            Rp {{ number_format($s->cash_in, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">
                            Rp {{ number_format($s->cash_out, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-blue-600">
                            Rp {{ number_format($s->final_cash, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($s->status === 'open')
                            <span class="badge badge-success">✓ Open</span>
                            @else
                            <span class="badge badge-gray">🔒 Closed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="viewDetail('{{ $s->id }}')"
                                class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 mr-3"
                                title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            @if($s->status === 'closed')
                            <button wire:click="printShift('{{ $s->id }}')" 
                                class="text-green-600 hover:text-green-900 dark:hover:text-green-400 mr-3"
                                title="Print Laporan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </button>

                            <button wire:click="confirmDelete('{{ $s->id }}')"
                                class="text-red-600 hover:text-red-900 dark:hover:text-red-400" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2">Belum ada data shift</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $shifts->links() }}
        </div>
    </div>

    {{-- Detail Modal --}}
    {{-- Detail Modal --}}
    @if ($showDetailModal && $viewShiftData)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" wire:click="closeDetailModal"></div>

        {{-- Modal Container --}}
        <div
            class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-6xl mx-4 sm:my-10 transform transition-all">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                    </svg>
                    Detail Shift Kasir
                </h3>
                <button wire:click="closeDetailModal" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-6 py-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                {{-- Info Shift --}}
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Kasir</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $viewShiftData->cashierName ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Shift Dibuka</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ optional($viewShiftData->shift_start)->format('d M Y H:i') ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Shift Ditutup</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ optional($viewShiftData->shift_end)->format('d M Y H:i') ?? 'Belum ditutup' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Status</p>
                            <p
                                class="font-semibold {{ $viewShiftData->status === 'closed' ? 'text-green-600' : 'text-amber-600' }}">
                                {{ ucfirst($viewShiftData->status ?? 'unknown') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Kas --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    @php
                    $cards = [
                    ['label' => 'Kas Awal', 'color' => 'blue', 'value' => $viewShiftData->initial_cash],
                    ['label' => 'Kas Masuk', 'color' => 'green', 'value' => $viewShiftData->cash_in],
                    ['label' => 'Kas Keluar', 'color' => 'red', 'value' => $viewShiftData->cash_out],
                    ['label' => 'Kas Akhir', 'color' => 'purple', 'value' => $viewShiftData->final_cash],
                    ];
                    @endphp
                    @foreach ($cards as $card)
                    <div
                        class="bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900 p-4 rounded-lg border border-{{ $card['color'] }}-200 dark:border-{{ $card['color'] }}-700">
                        <p class="text-xs text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-300 mb-1">{{
                            $card['label'] }}</p>
                        <p class="text-xl font-bold text-{{ $card['color'] }}-700 dark:text-{{ $card['color'] }}-200">
                            Rp {{ number_format($card['value'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>

                {{-- Pemasukan (Sales) --}}
                <div class="mb-6">
                    <div x-data="{ open: true }"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <button @click="open = !open"
                            class="w-full bg-green-50 dark:bg-green-900 px-4 py-3 flex items-center justify-between hover:bg-green-100 dark:hover:bg-green-800 transition">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-300 mr-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-semibold text-green-700 dark:text-green-200">
                                    💰 Pemasukan dari Penjualan ({{ count($viewSales) }} Transaksi)
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-lg font-bold text-green-700 dark:text-green-200">
                                    Rp {{ number_format(collect($viewSales)->sum('total_amount'), 0, ',', '.') }}
                                </span>
                                <svg class="w-5 h-5 text-green-600 dark:text-green-300 transition-transform"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <div x-show="open" x-collapse class="bg-white dark:bg-gray-800">
                            @if(count($viewSales) > 0)

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead
                                        class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Invoice</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Waktu</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Metode</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Subtotal</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Diskon</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Total</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700"
                                        x-data="{ openItems: {} }">
                                        @foreach($viewSales as $sale)
                                        {{-- Row Utama --}}
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3">
                                                <span
                                                    class="font-mono text-xs font-semibold text-blue-600 dark:text-blue-400">
                                                    {{ $sale['invoice_number'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($sale['sale_date'])->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($sale['payment_method'] === 'cash')
                                                <span class="badge badge-success">💵 Cash</span>
                                                @elseif($sale['payment_method'] === 'qris')
                                                <span class="badge badge-info">📱 QRIS</span>
                                                @else
                                                <span class="badge badge-gray">🏦 Transfer</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium">
                                                Rp {{ number_format($sale['subtotal'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-red-600">
                                                -Rp {{ number_format($sale['discount_total'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-green-600">
                                                Rp {{ number_format($sale['total_amount'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    @click="openItems['sale_{{ $sale['id'] }}'] = !openItems['sale_{{ $sale['id'] }}']"
                                                    class="text-blue-600 hover:text-blue-800 transition">
                                                    <svg class="w-4 h-4 inline transition-transform"
                                                        :class="{ 'rotate-180': openItems['sale_{{ $sale['id'] }}'] }"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Row Detail --}}
                                        <tr x-show="openItems['sale_{{ $sale['id'] }}']"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            style="display: none;">
                                            <td colspan="7" class="px-4 py-2 bg-gray-50 dark:bg-gray-900">
                                                <div class="text-xs">
                                                    <p class="font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        Item Detail:
                                                    </p>
                                                    <table class="w-full text-sm">
                                                        <thead>
                                                            <tr class="border-b border-gray-300 dark:border-gray-600">
                                                                <th
                                                                    class="text-left py-1 text-gray-600 dark:text-gray-400">
                                                                    Produk</th>
                                                                <th
                                                                    class="text-center py-1 text-gray-600 dark:text-gray-400">
                                                                    Qty</th>
                                                                <th
                                                                    class="text-right py-1 text-gray-600 dark:text-gray-400">
                                                                    Harga</th>
                                                                <th
                                                                    class="text-right py-1 text-gray-600 dark:text-gray-400">
                                                                    Diskon</th>
                                                                <th
                                                                    class="text-right py-1 text-gray-600 dark:text-gray-400">
                                                                    Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($sale['items'] as $item)
                                                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                                                <td class="py-1">{{ $item['product_name'] }}</td>
                                                                <td class="text-center py-1">{{ $item['quantity'] }} {{
                                                                    $item['unit_name'] }}</td>
                                                                <td class="text-right py-1">Rp {{
                                                                    number_format($item['price'], 0, ',', '.') }}</td>
                                                                <td class="text-right py-1 text-red-600">-Rp {{
                                                                    number_format($item['discount'], 0, ',', '.') }}
                                                                </td>
                                                                <td class="text-right py-1 font-semibold">Rp {{
                                                                    number_format($item['subtotal'], 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>


                                </table>
                            </div>
                            @else
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="mt-2">Tidak ada transaksi penjualan</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Pengeluaran (Expenses) --}}
                <div class="mb-6">
                    <div x-data="{ open: false }"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <button @click="open = !open"
                            class="w-full bg-red-50 dark:bg-red-900 px-4 py-3 flex items-center justify-between hover:bg-red-100 dark:hover:bg-red-800 transition">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-300 mr-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="font-semibold text-red-700 dark:text-red-200">
                                    💸 Pengeluaran ({{ count($viewExpenses) }} Item)
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-lg font-bold text-red-700 dark:text-red-200">
                                    Rp {{ number_format(collect($viewExpenses)->sum('amount'), 0, ',', '.') }}
                                </span>
                                <svg class="w-5 h-5 text-red-600 dark:text-red-300 transition-transform"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <div x-show="open" x-collapse class="bg-white dark:bg-gray-800">
                            @if(count($viewExpenses) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700 border-b">
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Waktu</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Keterangan</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Catatan</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($viewExpenses as $expense)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3">{{ $expense['date']->format('H:i') }}</td>
                                            <td class="px-4 py-3">{{ $expense['description'] }}</td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $expense['notes']
                                                ?? '-' }}</td>
                                            <td class="px-4 py-3 text-right font-bold text-red-600">
                                                Rp {{ number_format($expense['amount'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2">Belum ada pengeluaran</p>
                                <p class="text-xs text-gray-400 mt-1">(Fitur ini akan diimplementasikan)</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                <button wire:click="closeDetailModal" class="btn btn-outline">Tutup</button>
                @if ($viewShiftData->status === 'closed')
                <button wire:click="printShift('{{ $viewShiftData->id }}')" class="btn btn-success">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Laporan
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Alpine.js Collapse Plugin (pastikan di layout) --}}
@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('openPrintWindow', (url) => {
            window.open(url, '_blank');
        });
    });
</script>
@endpush
