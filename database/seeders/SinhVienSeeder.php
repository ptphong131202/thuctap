<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SinhVien;
use App\Models\User;
use App\Models\LopHoc;
use Illuminate\Container\Container;
use Faker\Generator;

class SinhVienSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lhId = LopHoc::inRandomOrder()->first()->lh_id;
        if ($lhId) {
            for ($i = 0; $i <= 10; $i++) {
                $sinhVienData = [
                    'sv_ma' => $this->faker->regexify('[A-Z]{5}[0-4]{3}'),
                    'sv_ten' => $this->faker->firstNameMale(),
                    'sv_ho' => $this->faker->lastName(),
                    'sv_gioitinh' => 1,
                    'sv_dantoc' => 'Kinh',
                    'sv_diachi' => $this->faker->sentence(3, true),
                    'sv_ngaysinh' => $this->faker->dateTimeBetween('-20 year', '-18 year'),
                ];
    
                $userData = [
                    'username' => $sinhVienData['sv_ma'],
                    'email' => $this->faker->email(),
                    'name' => $sinhVienData['sv_ho'] . ' ' . $sinhVienData['sv_ten'],
                    'password' => bcrypt('12345678'),
                    'type' => 2,
                    'status' => 1,
                ];
    
                $user = new User;
                $user->fill($userData);
                $user->save();
                
                $model = new SinhVien;
                $model->fill($sinhVienData);
                $model->user_id = $user->user_id;
                $model->save();
    
                $model->lopHoc()->sync($lhId);
            }
        }
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }
}
