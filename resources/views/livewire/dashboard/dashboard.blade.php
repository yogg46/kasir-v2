<div>
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">📊 Dashboard Store</h1>
            <button wire:click="loadData" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                🔄 Refresh
            </button>
        </div>

        {{-- Ringkasan Kartu --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Total Produk Aktif</p>
                <h2 class="text-2xl font-bold text-blue-600">{{ $totalProducts }}</h2>
            </div>
            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Total Cabang</p>
                <h2 class="text-2xl font-bold text-green-600">{{ $totalBranches }}</h2>
            </div>
            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Penjualan Hari Ini</p>
                <h2 class="text-2xl font-bold text-amber-600">Rp {{ number_format($todaySalesTotal,0,',','.') }}</h2>
            </div>
            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Stok Menipis</p>
                <h2 class="text-2xl font-bold text-red-600">{{ count($lowStockProducts) }}</h2>
            </div>
        </div>

        {{-- Grafik Penjualan --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="font-semibold mb-2">📈 Penjualan 7 Hari Terakhir</h2>
            <canvas id="salesChart" class="w-full h-40"></canvas>
        </div>

        {{-- Stok Menipis --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="font-semibold mb-2 text-red-600">⚠️ Stok Menipis</h2>
            <table class="w-full text-sm">
                <thead class="text-gray-500 border-b">
                    <tr>
                        <th>Produk</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockProducts as $s)
                    <tr class="border-b">
                        <td class="py-1">{{ $s->toProduct->name ?? '-' }}</td>
                        <td class="py-1 text-center">{{ $s->quantity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-gray-400 py-2">Tidak ada stok menipis</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Notifikasi --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="font-semibold mb-2">🔔 Notifikasi Terbaru</h2>
            <ul>
                @forelse($notifications as $n)
                <li class="border-b py-1">
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', () => {
    const ctx = document.getElementById('salesChart');
    const labels = @json($salesChart->pluck('date'));
    const data = @json($salesChart->pluck('total'));
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Penjualan',
                data: data,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.1)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
    </script>
    @endpush

</div>