<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeDaoTao;

class HeDaoTaoSeeder extends Seeder
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
                'hdt_ten' => 'Cao đẳng',
            ],
            [
                'hdt_ten' => 'Trung cấp',
            ],
        ];
        
        foreach ($samples as $sample) {
            $model = HeDaoTao::where('hdt_ten', $sample['hdt_ten'])->first();
            if (!$model) {
                $model = new HeDaoTao;
                $model->hdt_ten = $sample['hdt_ten'];
                $model->save();
            }
        }
    }
}
