<?php

namespace Database\Factories;

use App\Models\pricesModels;
use App\Models\branchesModel;
use App\Models\cabangModel;
use App\Models\hargaModel;
use App\Models\productsModels;
use App\Models\produkModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\hargaModel>
 */
class hargaModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = hargaModel::class;

    public function definition(): array
    {
        $branch = cabangModel::inRandomOrder()->first();

        return [
            'product_id'     => produkModel::inRandomOrder()->first()?->id,
            'branch_id'      => $branch?->id,
            'unit_name'      => fake()->randomElement(['pcs', 'box', 'pack']),
            'unit_qty'       => fake()->numberBetween(1, 10),
            'price'          => fake()->numberBetween(10000, 250000),
            'old_price'      => null,
            'purchase_price' => fake()->numberBetween(8000, 200000),
            'is_default'     => true,
            'valid_from'     => now()->subDays(rand(1, 30)),
            'valid_until'    => now()->addDays(rand(60, 180)),
            'notes'          => fake()->optional()->sentence(),
        ];
    }
}
