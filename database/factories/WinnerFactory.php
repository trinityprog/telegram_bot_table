<?php

namespace Database\Factories;

use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

class WinnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Winner::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phone' => $this->faker->phoneNumber(),
            'prize' => $this->faker->word(),
            'created_at' => now()->subDays(rand(1, 100)),
        ];
    }
}
