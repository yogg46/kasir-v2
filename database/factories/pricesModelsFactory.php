<?php

namespace Database\Factories;

use App\Models\pricesModels;
use App\Models\branchesModel;
use App\Models\productsModels;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\pricesModels>
 */
class pricesModelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = pricesModels::class;

    public function definition(): array
    {
        $branch = branchesModel::inRandomOrder()->first();

        return [
            'product_id'     => productsModels::inRandomOrder()->first()?->id,
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
