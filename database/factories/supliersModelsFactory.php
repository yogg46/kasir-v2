<?php

namespace Database\Factories;

use App\Models\supliersModels;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\supliersModels>
 */
class supliersModelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = supliersModels::class;

    public function definition(): array
    {
        // contoh nama supplier realistis
        $prefix = fake()->randomElement(['PT', 'CV', 'UD']);
        $nama = fake()->unique()->company();

        return [
            // 'code'    => 'SUP-' . str_pad(fake()->unique()->numberBetween(1, 9999999), 7, '0', STR_PAD_LEFT),
            'name'    => "{$prefix} {$nama}",
            'address' => fake()->address(),
            'phone'   => fake()->numerify('08##########'),
        ];
    }
}
