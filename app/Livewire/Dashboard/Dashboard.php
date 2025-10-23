<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\salesModels;
use App\Models\stockModels;
use App\Models\branchesModel;
use App\Models\cabangModel;
use App\Models\productsModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\notificationsModels;
use App\Models\produkModel;

class Dashboard extends Component
{

    public $totalProducts;
    public $totalBranches;
    public $todaySalesTotal;
    public $lowStockProducts = [];
    public $salesChart = [];
    public $notifications = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // 📦 Total Produk Aktif
        $this->totalProducts = produkModel::where('is_active', true)->count();

        // 🏢 Total Cabang
        $this->totalBranches = cabangModel::count();

        // 💰 Total Penjualan Hari Ini
        $this->todaySalesTotal = salesModels::whereDate('sale_date', Carbon::today())->sum('total_amount');

        // 🏬 Stok Menipis (stok < 10)
        $this->lowStockProducts = stockModels::with('toProduk')
            ->where('quantity', '<', 10)
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        // 📊 Penjualan 7 Hari Terakhir
        $this->salesChart = salesModels::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('sale_date', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 🔔 Notifikasi Terbaru
        $this->notifications = notificationsModels::orderByDesc('created_at')->limit(5)->get();
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard');
    }
}
