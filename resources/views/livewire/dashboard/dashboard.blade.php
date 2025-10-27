<div>
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">📊 Dashboard Store</h1>
            <button wire:click="loadData" wire:loading.attr="disabled"
                class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <span wire:loading.remove>🔄 Refresh</span>
                <span wire:loading>⏳ Loading...</span>
            </button>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-900 border border-gray-800 p-4 rounded-xl text-center shadow-sm">
                <p class="text-gray-400">Total Produk Aktif</p>
                <h2 class="text-2xl font-bold text-blue-500">{{ $totalProducts }}</h2>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-4 rounded-xl text-center shadow-sm">
                <p class="text-gray-400">Total Cabang</p>
                <h2 class="text-2xl font-bold text-green-500">{{ $totalBranches }}</h2>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-4 rounded-xl text-center shadow-sm">
                <p class="text-gray-400">Penjualan Hari Ini</p>
                <h2 class="text-2xl font-bold text-amber-500">Rp {{ number_format($todaySalesTotal,0,',','.') }}</h2>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-4 rounded-xl text-center shadow-sm">
                <p class="text-gray-400">Stok Menipis</p>
                <h2 class="text-2xl font-bold text-red-500">{{ count($lowStockProducts) }}</h2>
            </div>
        </div>

        {{-- Grafik Penjualan --}}
        <div class="bg-gray-900 p-4 rounded-xl shadow-sm border border-gray-800">
            <h2 class="font-semibold mb-2">📈 Penjualan 7 Hari Terakhir</h2>
            <div style="position: relative; height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="bg-gray-900 p-4 rounded-xl shadow-sm border border-gray-800">
            <h2 class="font-semibold mb-2 text-red-500">⚠️ Stok Menipis</h2>
            <table class="w-full text-sm">
                <thead class="text-gray-500 border-b border-gray-700">
                    <tr>
                        <th class="py-2 text-left">Produk</th>
                        <th class="py-2 text-center">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockProducts as $s)
                    <tr class="border-b border-gray-800">
                        <td class="py-2">{{ $s->toProduk->name ?? '-' }}</td>
                        <td class="py-2 text-center">{{ $s->quantity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-gray-500 py-2">Tidak ada stok menipis</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Notifikasi --}}
        <div class="bg-gray-900 p-4 rounded-xl shadow-sm border border-gray-800">
            <h2 class="font-semibold mb-2">🔔 Notifikasi Terbaru</h2>
            <ul>
                @forelse($notifications as $n)
                <li class="border-b border-gray-800 py-2">
                    <strong>{{ $n->title }}</strong>
                    <div class="text-xs text-gray-500">{{ $n->created_at->diffForHumans() }}</div>
                    <div class="text-sm">{{ $n->message }}</div>
                </li>
                @empty
                <li class="text-gray-400 text-sm py-2">Tidak ada notifikasi</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>

@push('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script> --}}
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     const ctx = document.getElementById('salesChart');
    //     if (ctx) {
    //         new Chart(ctx, {
    //             type: 'line',
    //             data: {
    //                 labels: @json($salesChart->pluck('date')),
    //                 datasets: [{
    //                     label: 'Total Penjualan (Rp)',
    //                     data: @json($salesChart->pluck('total')),
    //                     borderColor: '#3b82f6',
    //                     backgroundColor: 'rgba(59,130,246,0.2)',
    //                     tension: 0.4,
    //                     fill: true
    //                 }]
    //             },
    //             options: {
    //                 responsive: true,
    //                 maintainAspectRatio: false,
    //                 plugins: {
    //                     legend: {
    //                         display: true,
    //                         labels: { color: '#9ca3af' }
    //                     }
    //                 },
    //                 scales: {
    //                     y: {
    //                         beginAtZero: true,
    //                         ticks: {
    //                             color: '#9ca3af',
    //                             callback: (v) => 'Rp ' + v.toLocaleString('id-ID')
    //                         },
    //                         grid: { color: '#374151' }
    //                     },
    //                     x: {
    //                         ticks: { color: '#9ca3af' },
    //                         grid: { color: '#374151' }
    //                     }
    //                 }
    //             }
    //         });
    //     }
    // });

    document.addEventListener('livewire:navigated', () => {
     const ctx = document.getElementById('salesChart');
    if (ctx) {
        // console.log('salesChart rendering');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($salesChart->pluck('date')),
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: @json($salesChart->pluck('total')),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: '#9ca3af' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#9ca3af',
                            callback: (v) => 'Rp ' + v.toLocaleString('id-ID')
                        },
                        grid: { color: '#374151' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { color: '#374151' }
                    }
                }
            }
        });
    }
}, { once: true });
</script>
@endpush
