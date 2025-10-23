<?php

namespace Database\Seeders;

use App\Models\stockModels;

use App\Models\cabangModel;
use App\Models\supliersModels;
use Illuminate\Database\Seeder;
use App\Models\gudangModel;
use App\Models\hargaModel;
use App\Models\kategoriModel;
use App\Models\produkModel;

class prodectStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🏢 Buat minimal 2 cabang manual
        $branches = collect([
            cabangModel::create([
                // 'code' => 'BRN-0000001',
                'name' => 'Cabang Utama Surakarta',
                'address' => 'Jl. Slamet Riyadi No.12, Surakarta',
                'phone' => '0271-123456',
                'is_head_office' => true,
            ]),
            cabangModel::create([
                // 'code' => 'BRN-0000002',
                'name' => 'Cabang Yogyakarta',
                'address' => 'Jl. Malioboro No.55, Yogyakarta',
                'phone' => '0274-654321',
                'is_head_office' => false,
            ]),
        ]);

        // 🏭 Buat gudang per cabang
        $branches->each(function ($branch) {
            gudangModel::create([
                'branch_id' => $branch->id,
                // 'code'      => 'WH-' . str_pad(rand(1,9999999),7,'0',STR_PAD_LEFT),
                'name'      => "Gudang {$branch->name}",
                'address'   => $branch->address,
                'is_main'   => true,
            ]);
        });

        // 📦 Kategori & supplier
        $categories = kategoriModel::factory()->count(5)->create();
        $suppliers  = supliersModels::factory()->count(5)->create();

        // 🧰 Produk (50)
        $products = produkModel::factory()->count(4)->create();

        // 💰 Buat harga & stok
        foreach ($products as $product) {
            foreach ($branches as $branch) {
                $warehouse = $branch->toGudang()->first();

                hargaModel::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                    'unit_name' => 'Pcs',
                    'unit_qty' => 1,
                    'price' => 5000,
                    'old_price' => null,
                    'purchase_price' => 3500,
                    'is_default' => true, // ini yang default
                    'valid_from' => now(),
                    'valid_until' => null,
                    'notes' => 'Harga satuan eceran'
                ]);

                // // Harga 2: Grosir (min 3 pcs)
                hargaModel::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                    'unit_name' => 'Grosir',
                    'unit_qty' => 3,
                    'price' => 4500,
                    'old_price' => 5000,
                    'purchase_price' => 3500,
                    'is_default' => false,
                    'valid_from' => now(),
                    'valid_until' => null,
                    'notes' => 'Harga grosir minimum 3 pcs'
                ]);

                // // Harga 3: Per Dus (12 pcs)
                hargaModel::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                    'unit_name' => 'Dus',
                    'unit_qty' => 12,
                    'price' => 4000,
                    'old_price' => 5000,
                    'purchase_price' => 3500,
                    'is_default' => false,
                    'valid_from' => now(),
                    'valid_until' => null,
                    'notes' => 'Harga per dus isi 12 pcs'
                ]);

                // // Harga 4: Per Karton (24 pcs) - Opsional
                hargaModel::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                    'unit_name' => 'Karton',
                    'unit_qty' => 24,
                    'price' => 3800,
                    'old_price' => 5000,
                    'purchase_price' => 3500,
                    'is_default' => false,
                    'valid_from' => now(),
                    'valid_until' => null,
                    'notes' => 'Harga per karton isi 24 pcs - Paling murah!'
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
