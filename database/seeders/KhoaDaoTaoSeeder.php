<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KhoaDaotao;
use App\Models\MonHoc;
use App\Models\HeDaoTao;

class KhoaDaoTaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $samples = $this->getSamplesData();
        foreach ($samples as $sample) {
            $model = KhoaDaotao::where('kdt_ma', $sample['kdt_ma'])->first();
            $heDaoTao = HeDaoTao::where('hdt_ten', $sample['hdt_ten'])->first();
            if (!$model) {
                if (!$heDaoTao) {
                    continue;
                }
                $model = new KhoaDaotao;
                $model->hdt_id = $heDaoTao->hdt_id;
                $model->kdt_ma = $sample['kdt_ma'];
                $model->kdt_ten = $sample['kdt_ten'];
                $model->kdt_khoa = $sample['kdt_khoa'];
                $model->kdt_he = $sample['kdt_he'];
                $model->kdt_hocky = $sample['kdt_hocky'];
                $model->save();

                $danhSachMonHoc = $sample['mon_hoc'];
                $syncData = [];
                foreach ($danhSachMonHoc as $monHoc) {
                    $mhModel = MonHoc::where('mh_ma', $monHoc['mh_ma'])->first();
                    if (!$mhModel) {
                        $mhModel = new MonHoc;
                        $mhModel->mh_ma = $monHoc['mh_ma'];
                        $mhModel->mh_ten = $monHoc['mh_ten'];
                        $mhModel->mh_tichluy = $monHoc['mh_tichluy'];
                        $mhModel->mh_sodonvihoctrinh = rand(2, 6);
                        $mhModel->mh_sotiet = rand(3, 9);
                        $mhModel->save();
                    }
                    $syncData[] = [
                        'mh_id' => $mhModel->mh_id,
                        'kdt_mh_hocky' => $monHoc['kdt_mh_hocky'],
                        'kdt_mh_index' => $monHoc['kdt_mh_index'],
                    ];
                }
                $model->monHoc()->sync($syncData);
            }
        }
    }

    protected function getSamplesData()
    {
        return [
            [
                'hdt_ten' => 'Cao đẳng',
                'kdt_id' => 1,
                'kdt_ma' => 'QTM6480209',
                'kdt_ten' => 'Quản trị mạng máy tính',
                'kdt_khoa' => 'Khóa 55',
                'kdt_he' => 'Chính quy',
                'kdt_hocky' => 2,
                'mon_hoc' => [
                    [
                        'mh_ma' => 'CT',
                        'mh_ten' => 'Chính trị',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 1,
                        'kdt_mh_index' => 1,
                    ],
                    [
                        'mh_ma' => 'PL',
                        'mh_ten' => 'Pháp luật',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 1,
                        'kdt_mh_index' => 2,
                    ],
                    [
                        'mh_ma' => 'GDTC',
                        'mh_ten' => 'Giáo dục thể chất',
                        'mh_tichluy' => 0,
                        'kdt_mh_hocky' => 1,
                        'kdt_mh_index' => 3,
                    ],
                    [
                        'mh_ma' => 'GDQP',
                        'mh_ten' => 'Giáo dục quốc phòng - An ninh',
                        'mh_tichluy' => 0,
                        'kdt_mh_hocky' => 1,
                        'kdt_mh_index' => 4,
                    ],
                    [
                        'mh_ma' => 'TH',
                        'mh_ten' => 'Tin học',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 1,
                        'kdt_mh_index' => 5,
                    ],
                    [
                        'mh_ma' => 'ENG',
                        'mh_ten' => 'Ngoại ngữ (Anh văn)',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 1,
                        'kdt_mh_index' => 6,
                    ],
                    [
                        'mh_ma' => 'NNLCCPP',
                        'mh_ten' => 'Ngôn ngữ lập trình C++',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 2,
                        'kdt_mh_index' => 1,
                    ],
                    [
                        'mh_ma' => 'CCDLVGT',
                        'mh_ten' => 'Cấu trúc dữ liệu và giải thuật',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 2,
                        'kdt_mh_index' => 2,
                    ],
                    [
                        'mh_ma' => 'MMTVATM',
                        'mh_ten' => 'Mạng máy tính và An toàn mạng',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 2,
                        'kdt_mh_index' => 3,
                    ],
                    [
                        'mh_ma' => 'CSDL',
                        'mh_ten' => 'Cơ sở dữ liệu',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 2,
                        'kdt_mh_index' => 4,
                    ],
                    [
                        'mh_ma' => 'HQTCSDL',
                        'mh_ten' => 'Hệ quản trị cơ sở dữ liệu (SQL Server)',
                        'mh_tichluy' => 1,
                        'kdt_mh_hocky' => 2,
                        'kdt_mh_index' => 5,
                    ],
                ]
            ]
        ];
    }
}
