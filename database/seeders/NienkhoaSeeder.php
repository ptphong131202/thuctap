<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NienKhoa;

class NienkhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $samples = [
            [
                'nk_ten' => '2020 - 2024'
            ],
            [
                'nk_ten' => '2021 - 2025'
            ],
            [
                'nk_ten' => '2022 - 2026'
            ]
        ];
        foreach ($samples as $sample) {
            $model = NienKhoa::where('nk_ten', $sample['nk_ten'])->first();
            if (!$model) {
                $model = new NienKhoa;
                $model->nk_ten = $sample['nk_ten'];
                $model->save();
            }
        }
    }
}
