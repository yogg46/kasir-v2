<?php

namespace Database\Factories;

use App\Models\categoriesModels;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\categoriesModels>
 */
class categoriesModelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = categoriesModels::class;
    public function definition(): array
    {
        $categories = [
            'Makanan & Minuman',
            'Elektronik',
            'Pakaian',
            'Peralatan Rumah Tangga',
            'Kesehatan & Kecantikan',
            'Otomotif',
            'ATK & Kantor',
            'Olahraga & Outdoor',
            'Mainan & Anak',
            'Pertanian'
        ];

        return [
            'name' => fake()->unique()->randomElement($categories),
            'description' => 'Kategori produk ' . fake()->sentence(3),
        ];
    }
}
