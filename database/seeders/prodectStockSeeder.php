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
    public function run(): void
    {
        // 🏢 Buat minimal 2 cabang manual
        $branches = collect([
            cabangModel::create([
                'name' => 'Cabang Utama Surakarta',
                'address' => 'Jl. Slamet Riyadi No.12, Surakarta',
                'phone' => '0271-123456',
                'is_head_office' => true,
            ]),
            cabangModel::create([
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
                'name'      => "Gudang {$branch->name}",
                'address'   => $branch->address,
                'is_main'   => true,
            ]);
        });

        // 📦 Kategori & supplier
        $categories = kategoriModel::factory()->count(5)->create();
        $suppliers  = supliersModels::factory()->count(5)->create();

        // 🧰 Produk
        $products = produkModel::factory()->count(100)->create();

        foreach ($products as $product) {
            foreach ($branches as $branch) {
                $warehouse = $branch->toGudang()->first();

                // 🔢 Tentukan jumlah harga (1–4)
                $priceCount = rand(1, 4);

                // Template semua jenis harga
                $priceTemplates = [
                    [
                        'unit_name' => 'Pcs',
                        'unit_qty' => 1,
                        'price' => rand(40000, 70000),
                        'notes' => 'Harga satuan eceran',
                        'is_default' => true,
                    ],
                    [
                        'unit_name' => 'Grosir',
                        'unit_qty' => 3,
                        'price' => rand(35000, 60000),
                        'notes' => 'Harga grosir minimum 3 pcs',
                        'is_default' => false,
                    ],
                    [
                        'unit_name' => 'Dus',
                        'unit_qty' => 12,
                        'price' => rand(30000, 55000),
                        'notes' => 'Harga per dus isi 12 pcs',
                        'is_default' => false,
                    ],
                    [
                        'unit_name' => 'Karton',
                        'unit_qty' => 24,
                        'price' => rand(28000, 52000),
                        'notes' => 'Harga per karton isi 24 pcs',
                        'is_default' => false,
                    ],
                ];

                // Selalu sertakan harga default (Pcs)
                $selected = collect([$priceTemplates[0]]);

                // Kalau butuh lebih dari 1 harga, tambahkan acak dari sisanya
                if ($priceCount > 1) {
                    $extra = collect($priceTemplates)
                        ->skip(1)
                        ->shuffle()
                        ->take($priceCount - 1);
                    $selected = $selected->merge($extra);
                }

                // Simpan harga ke DB
                foreach ($selected as $data) {
                    hargaModel::create([
                        'product_id'      => $product->id,
                        'branch_id'       => $branch->id,
                        'unit_name'       => $data['unit_name'],
                        'unit_qty'        => $data['unit_qty'],
                        'price'           => $data['price'],
                        'old_price'       => null,
                        'purchase_price'  => rand(25000, 35000),
                        'is_default'      => $data['is_default'],
                        'valid_from'      => now(),
                        'valid_until'     => null,
                        'notes'           => $data['notes'],
                    ]);
                }

                // 📦 Tambahkan stok acak
                stockModels::factory()->create([
                    'product_id'   => $product->id,
                    // 'branch_id'    => $branch->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity'     => fake()->numberBetween(5, 800),
                ]);
            }
        }

        $this->command->info('✅ Data cabang, gudang, kategori, supplier, produk, harga, dan stok berhasil dibuat dengan variasi harga acak (selalu 1 default)!');
    }
}
