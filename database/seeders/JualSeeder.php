<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\hargaModel;
use App\Models\cabangModel;
use App\Models\salesModels;
use App\Models\stockModels;
use App\Models\saleitemsModels;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = cabangModel::all();

        if ($branches->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada cabang yang tersedia. Jalankan prodectStockSeeder terlebih dahulu!');
            return;
        }

        // Ambil atau buat user kasir
        $cashiers = User::whereHas('toRole', function ($query) {
            $query->where('name', 'kasir');
        })->get();

        // Jika tidak ada kasir, buat dummy user
        if ($cashiers->isEmpty()) {
            $cashiers = collect([
                User::firstOrCreate(
                    ['email' => 'kasir1@example.com'],
                    [
                        'name' => 'Kasir 1',
                        'password' => bcrypt('password'),
                    ]
                ),
                User::firstOrCreate(
                    ['email' => 'kasir2@example.com'],
                    [
                        'name' => 'Kasir 2',
                        'password' => bcrypt('password'),
                    ]
                ),
            ]);
        }

        $paymentMethods = ['cash', 'qris'];
        $statuses = ['paid'];

        // Buat transaksi untuk 7 hari terakhir
        $transactionsCreated = 0;

        for ($day = 0; $day < 7; $day++) {
            // Random 5-15 transaksi per hari
            $transactionsPerDay = rand(5, 15);

            foreach (range(1, $transactionsPerDay) as $index) {
                $branch = $branches->random();
                $cashier = $cashiers->random();

                // Random waktu dalam hari tersebut (jam operasional 08:00 - 20:00)
                $saleDate = now()
                    ->subDays($day)
                    ->setHour(rand(8, 20))
                    ->setMinute(rand(0, 59))
                    ->setSecond(rand(0, 59))
                    ->format('Y-m-d H:i:s');

                // Ambil stok yang tersedia di cabang ini (quantity > 0)
                $warehouse = $branch->toGudang()->first();
                $warehouseId = $warehouse?->id;

                // Ambil stok yang tersedia di cabang ini (quantity > 0)
                $availableStocks = stockModels::where('warehouse_id', $warehouseId)
                    ->where('quantity', '>', 0)
                    ->with(['toProduk', 'toGudang'])
                    ->get();

                if ($availableStocks->isEmpty()) {
                    $this->command->warn("⚠️ Tidak ada stok tersedia untuk cabang: {$branch->name}");
                    continue;
                }

                // Pilih 1-5 produk secara acak
                $selectedStocks = $availableStocks->random(min(rand(1, 5), $availableStocks->count()));

                $subtotal = 0;
                $discountTotal = 0;
                $saleItems = [];

                foreach ($selectedStocks as $stock) {
                    $product = $stock->toProduk;

                    // Ambil harga yang tersedia untuk produk ini di cabang ini
                    $prices = hargaModel::where('product_id', $product->id)
                        ->where('branch_id', $branch->id)
                        ->get();

                    if ($prices->isEmpty()) {
                        continue;
                    }

                    // Pilih salah satu unit harga secara acak
                    $selectedPrice = $prices->random();

                    // Tentukan quantity yang dibeli (tidak melebihi stok)
                    // Untuk unit dengan qty > 1, sesuaikan dengan unit_qty
                    $maxQtyInUnits = floor($stock->quantity / $selectedPrice->unit_qty);

                    if ($maxQtyInUnits < 1) {
                        // Skip jika stok tidak cukup untuk 1 unit
                        continue;
                    }

                    // Random quantity pembelian (dalam unit yang dipilih)
                    $quantityInUnits = rand(1, min($maxQtyInUnits, 10));

                    // Total quantity dalam satuan pcs
                    $totalQuantity = $quantityInUnits * $selectedPrice->unit_qty;

                    // Hitung discount acak (0-20%)
                    $discountPercent = rand(0, 20);
                    $itemPrice = $selectedPrice->price;
                    $itemDiscount = ($itemPrice * $quantityInUnits * $discountPercent) / 100;
                    $itemSubtotal = ($itemPrice * $quantityInUnits) - $itemDiscount;

                    $subtotal += $itemSubtotal;
                    $discountTotal += $itemDiscount;

                    // Simpan data item untuk nanti
                    $saleItems[] = [
                        'stock' => $stock,
                        'product_id' => $product->id,
                        'unit_name' => $selectedPrice->unit_name,
                        'quantity' => $quantityInUnits,
                        'total_pcs' => $totalQuantity,
                        'price' => $itemPrice,
                        'discount' => $itemDiscount,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                // Skip jika tidak ada item yang valid
                if (empty($saleItems)) {
                    continue;
                }

                $totalAmount = $subtotal;
                $status = $statuses[array_rand($statuses)];

                // Buat transaksi sales
                $sale = salesModels::create([
                    'branch_id' => $branch->id,
                    'cashier_id' => $cashier->id,
                    'sale_date' => $saleDate,
                    'subtotal' => $subtotal,
                    'total_amount' => $totalAmount,
                    'discount_total' => $discountTotal,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => $status,
                    'notes' => rand(0, 1) ? null : 'Transaksi ' . fake()->sentence(3),
                ]);

                // Buat sale items dan kurangi stok (hanya untuk status completed)
                foreach ($saleItems as $item) {
                    saleitemsModels::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'unit_name' => $item['unit_name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Kurangi stok jika transaksi completed
                    if ($status === 'completed') {
                        $item['stock']->decrement('quantity', $item['total_pcs']);
                    }
                }

                $transactionsCreated++;
                $dayLabel = $day === 0 ? 'Hari ini' : "$day hari lalu";
                $this->command->info("✅ Sale #{$sale->invoice_number} dibuat untuk {$branch->name} ({$dayLabel}) dengan " . count($saleItems) . " item");
            }
        }

        $this->command->info("🎉 Seeder sales selesai! Total {$transactionsCreated} transaksi dalam 7 hari terakhir");
    }
}
