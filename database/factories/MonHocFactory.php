<?php

namespace Database\Factories;

use App\Models\MonHoc;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonHocFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MonHoc::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'mh_ma' => $this->faker->regexify('[A-Z]{5}[0-4]{3}'),
            'mh_ten' => $this->faker->name(),
            'mh_sodonvihoctrinh' => $this->faker->randomNumber(2, true),
            'mh_giangvien' => $this->faker->name(),
        ];
    }
}
