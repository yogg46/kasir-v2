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

        {{-- Grafik Penjualan (tanpa CDN) --}}
        <div x-data="{
            chart: null,
            labels: @js($salesChart->pluck('date')),
            data: @js($salesChart->pluck('total')),
            init() {
                import('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js').then(module => {
                    const Chart = module.Chart;
                    const ctx = this.$refs.canvas.getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: 'Total Penjualan (Rp)',
                                data: this.data,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.2)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (v) => 'Rp ' + v.toLocaleString('id-ID')
                                    }
                                }
                            }
                        }
                    });
                });
            }
        }" class="bg-gray-900 p-4 rounded-xl shadow-sm border border-gray-800">
            <h2 class="font-semibold mb-2">📈 Penjualan 7 Hari Terakhir</h2>
            <canvas x-ref="canvas" class="w-full h-52"></canvas>
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
