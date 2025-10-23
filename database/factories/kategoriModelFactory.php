<?php

namespace Database\Factories;

use App\Models\categoriesModels;
use App\Models\kategoriModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\kategoriModel>
 */
class kategoriModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = kategoriModel::class;
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
