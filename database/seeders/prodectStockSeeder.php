<?php

namespace Database\Seeders;

use App\Models\stockModels;
use App\Models\pricesModels;
use App\Models\branchesModel;
use App\Models\productsModels;
use App\Models\supliersModels;
use App\Models\warehosesModels;
use Illuminate\Database\Seeder;
use App\Models\categoriesModels;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class prodectStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🏢 Buat minimal 2 cabang manual
        $branches = collect([
            branchesModel::create([
                'code' => 'BRN-0000001',
                'name' => 'Cabang Utama Surakarta',
                'address' => 'Jl. Slamet Riyadi No.12, Surakarta',
                'phone' => '0271-123456',
                'is_head_office' => true,
            ]),
            branchesModel::create([
                'code' => 'BRN-0000002',
                'name' => 'Cabang Yogyakarta',
                'address' => 'Jl. Malioboro No.55, Yogyakarta',
                'phone' => '0274-654321',
                'is_head_office' => false,
            ]),
        ]);

        // 🏭 Buat gudang per cabang
        $branches->each(function ($branch) {
            warehosesModels::create([
                'branch_id' => $branch->id,
                'code'      => 'WH-' . str_pad(rand(1,9999999),7,'0',STR_PAD_LEFT),
                'name'      => "Gudang {$branch->name}",
                'address'   => $branch->address,
                'is_main'   => true,
            ]);
        });

        // 📦 Kategori & supplier
        $categories = categoriesModels::factory()->count(5)->create();
        $suppliers  = supliersModels::factory()->count(5)->create();

        // 🧰 Produk (50)
        $products = productsModels::factory()->count(50)->create();

        // 💰 Buat harga & stok
        foreach ($products as $product) {
            foreach ($branches as $branch) {
                $warehouse = $branch->toWarehouses()->first();

                pricesModels::factory()->create([
                    'product_id' => $product->id,
                    'branch_id'  => $branch->id,
                    'price'      => fake()->numberBetween(10000, 250000),
                    'purchase_price' => fake()->numberBetween(8000, 200000),
                ]);

                stockModels::factory()->create([
                    'product_id'   => $product->id,
                    'branch_id'    => $branch->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity'     => fake()->numberBetween(5, 200),
                ]);
            }
        }

        $this->command->info('✅ Data cabang, gudang, kategori, supplier, produk, harga, dan stok berhasil dibuat!');
    }
}
