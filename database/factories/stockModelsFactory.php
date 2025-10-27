<?php

namespace Database\Factories;

use App\Models\stockModels;
use App\Models\branchesModel;
use App\Models\cabangModel;
use App\Models\productsModels;
use App\Models\produkModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\stockModels>
 */
class stockModelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = stockModels::class;

    public function definition(): array
    {
        $branch = cabangModel::inRandomOrder()->first();
        $warehouse = $branch?->toGudang()?->inRandomOrder()?->first();

        return [
            'product_id'   => produkModel::inRandomOrder()->first()?->id,
            'warehouse_id' => $warehouse?->id,
            // 'branch_id'    => $branch?->id,
            'quantity'     => fake()->numberBetween(10, 300),
        ];
    }
}
