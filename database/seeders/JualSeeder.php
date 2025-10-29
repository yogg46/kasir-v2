<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\hargaModel;
use App\Models\cabangModel;
use App\Models\salesModels;
use App\Models\stockModels;
use App\Models\saleitemsModels;
use App\Models\shiftKasirModel;

class JualSeeder extends Seeder
{
    public function run(): void
    {
        $branches = cabangModel::all();

        if ($branches->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada cabang yang tersedia. Jalankan prodectStockSeeder terlebih dahulu!');
            return;
        }

        // Ambil user dengan role kasir
        $cashiers = User::whereHas('toRole', fn($q) => $q->where('name', 'kasir'))->get();

        // Jika belum ada, buat dummy kasir
        if ($cashiers->isEmpty()) {
            $cashiers = collect([
                User::firstOrCreate(
                    ['email' => 'kasir1@example.com'],
                    ['name' => 'Kasir 1', 'password' => bcrypt('password')]
                ),
                User::firstOrCreate(
                    ['email' => 'kasir2@example.com'],
                    ['name' => 'Kasir 2', 'password' => bcrypt('password')]
                ),
            ]);
        }

        $paymentMethods = ['cash', 'qris'];
        $statuses = ['paid'];

        $transactionsCreated = 0;

        // Buat transaksi untuk 7 hari terakhir
        for ($day = 0; $day < 7; $day++) {
            $transactionsPerDay = rand(5, 15);

            foreach (range(1, $transactionsPerDay) as $index) {
                $branch = $branches->random();
                $cashier = $cashiers->random();

                // Tentukan waktu transaksi dalam jam kerja (08:00–20:00)
                $saleDate = now()
                    ->subDays($day)
                    ->setHour(rand(8, 20))
                    ->setMinute(rand(0, 59))
                    ->setSecond(rand(0, 59));

                // Pastikan shift kasir untuk hari itu ada (atau buat)
                $shiftStart = $saleDate->copy()->startOfDay()->addHours(8);
                $shiftEnd   = $saleDate->copy()->startOfDay()->addHours(20);

                $shift = shiftKasirModel::firstOrCreate(
                    [
                        'cashier_id' => $cashier->id,
                        'branch_id' => $branch->id,
                        'shift_start' => $shiftStart,
                    ],
                    [
                        'shift_end' => $shiftEnd,
                        'initial_cash' => rand(100000, 300000),
                        'cash_in' => 0,
                        'cash_out' => 0,
                        'final_cash' => 0,
                        'status' => 'open',
                    ]
                );

                // Ambil stok yang tersedia di cabang
                $warehouse = $branch->toGudang()->first();
                if (!$warehouse) continue;

                $availableStocks = stockModels::where('warehouse_id', $warehouse->id)
                    ->where('quantity', '>', 0)
                    ->with(['toProduk'])
                    ->get();

                if ($availableStocks->isEmpty()) {
                    $this->command->warn("⚠️ Tidak ada stok tersedia untuk cabang {$branch->name}");
                    continue;
                }

                // Pilih produk acak
                $selectedStocks = $availableStocks->random(min(rand(1, 5), $availableStocks->count()));
                $subtotal = 0;
                $discountTotal = 0;
                $saleItems = [];

                foreach ($selectedStocks as $stock) {
                    $product = $stock->toProduk;
                    if (!$product) continue;

                    $prices = hargaModel::where('product_id', $product->id)
                        ->where('branch_id', $branch->id)
                        ->get();

                    if ($prices->isEmpty()) continue;

                    $selectedPrice = $prices->random();
                    $maxQtyInUnits = floor($stock->quantity / $selectedPrice->unit_qty);

                    if ($maxQtyInUnits < 1) continue;

                    $quantityInUnits = rand(1, min($maxQtyInUnits, 10));
                    $totalQuantity = $quantityInUnits * $selectedPrice->unit_qty;

                    $discountPercent = rand(0, 20);
                    $itemPrice = $selectedPrice->price;
                    $itemDiscount = ($itemPrice * $quantityInUnits * $discountPercent) / 100;
                    $itemSubtotal = ($itemPrice * $quantityInUnits) - $itemDiscount;

                    $subtotal += $itemSubtotal;
                    $discountTotal += $itemDiscount;

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

                if (empty($saleItems)) continue;

                $status = $statuses[array_rand($statuses)];

                // Buat transaksi penjualan
                $sale = salesModels::create([
                    'branch_id' => $branch->id,
                    'cashier_id' => $cashier->id,
                    'sale_date' => $saleDate,
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal,
                    'discount_total' => $discountTotal,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => $status,
                    'notes' => rand(0, 1) ? null : 'Transaksi ' . fake()->sentence(3),
                ]);

                // Tambahkan item dan kurangi stok
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

                    if ($status === 'paid') {
                        $item['stock']->decrement('quantity', $item['total_pcs']);
                    }
                }

                $transactionsCreated++;

                $sif = shiftKasirModel::all();

                foreach ($sif as $shif) {
                    $shif->update([
                        'cash_in' => $shif->totalSales ?? 0,
                        'final_cash' => $shif->initial_cash + $shif->totalSales ?? 0,
                        'status'=>'closed',
                    ]);
                }
                $dayLabel = $day === 0 ? 'Hari ini' : "$day hari lalu";
                $this->command->info("✅ Sale #{$sale->invoice_number} dibuat untuk {$branch->name} oleh {$cashier->name} ({$dayLabel})");
            }
        }

        $this->command->info("🎉 Seeder sales selesai! Total {$transactionsCreated} transaksi dalam 7 hari terakhir");
    }
}
