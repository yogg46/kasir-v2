<?php

namespace Database\Factories;

use App\Models\productsModels;
use App\Models\supliersModels;
use App\Models\categoriesModels;
use App\Models\kategoriModel;
use App\Models\produkModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\produkModel>
 */
class produkModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = produkModel::class;

    public function definition(): array
    {
        $category = kategoriModel::inRandomOrder()->first() ?? kategoriModel::factory()->create();
        // $supplier = supliersModels::inRandomOrder()->first() ?? supliersModels::factory()->create();

        $name = fake()->randomElement([
            'Kopi Bubuk Premium',
            'Minyak Goreng Sawit',
            'Sabun Cair Antibakteri',
            'Mouse Wireless',
            'Kemeja Katun Lengan Panjang',
            'Beras Super 5kg',
            'Shampoo Herbal 250ml',
            'Lampu LED 10W',
            'Pulpen Gel Hitam',
            'Sepatu Olahraga Ringan'
        ]);

        return [
            'category_id' => $category->id,
            // 'suplier_id'  => $supplier->id,
            // 'code'        => 'PRD-' . str_pad(fake()->unique()->numberBetween(1, 9999999), 7, '0', STR_PAD_LEFT),
            'name'        => $name,
            'description' => fake()->sentence(),
            'barcode'     => fake()->ean13(),
            'type'        => fake()->randomElement(['regular','umkm','seasonal']),
            'notes'       => fake()->optional()->sentence(),
            'is_active'   => true,
        ];
    }
}
