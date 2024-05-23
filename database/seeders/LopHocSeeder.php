<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHoc;
use App\Models\QuyetDinh;
use App\Models\KhoaDaoTao;
use App\Models\NienKhoa;

class LopHocSeeder extends Seeder
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
                'kdt_ma' => 'QTM6480209',
                'nk_ten' => '2020 - 2024',
                'qd' => [
                    'qd_ma' => '24',
                    'qd_ten' => 'Quyết định số 24',
                    'qd_loai' => 0,
                ],
                'lh_ma' => 'QTM202101',
                'lh_ten' => 'Quản trị mạng máy tính (202101)',
            ],
        ];

        foreach ($samples as $sample) {
            $lopHoc = LopHoc::where('lh_ma', $sample['lh_ma'])->first();
            if (!$lopHoc) {
                $qdModel = QuyetDinh::where('qd_ma', $sample['qd']['qd_ma'])->first();
                if (!$qdModel) {
                    $qdModel = new QuyetDinh;
                    $qdModel->qd_ma = $sample['qd']['qd_ma'];
                    $qdModel->qd_ten = $sample['qd']['qd_ten'];
                    $qdModel->qd_loai = $sample['qd']['qd_loai'];
                    $qdModel->qd_ngay = \Carbon\Carbon::now();
                    $qdModel->save();
                }

                $kdtModel = KhoaDaotao::where('kdt_ma', $sample['kdt_ma'])->first();
                if (!$kdtModel) {
                    continue;
                }

                $nkModel = NienKhoa::where('nk_ten', $sample['nk_ten'])->first();
                if (!$nkModel) {
                    continue;
                }

                $lopHoc = new LopHoc;
                $lopHoc->lh_ma = $sample['lh_ma'];
                $lopHoc->lh_ten = $sample['lh_ten'];
                $lopHoc->qd_id = $qdModel->qd_id;
                $lopHoc->kdt_id = $kdtModel->kdt_id;
                $lopHoc->nk_id = $nkModel->nk_id;
                $lopHoc->save();
            }
        }
    }
}
