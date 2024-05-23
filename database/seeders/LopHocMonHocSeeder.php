<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHoc;
use App\Models\MonHoc;
use App\Models\KhoaDaoTao;
use Illuminate\Support\Facades\DB;

class LopHocMonHocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $danhSachLopHoc = LopHoc::all();
        foreach ($danhSachLopHoc as $lopHoc) {
            $khoaDaoTao = $lopHoc->khoaDaoTao()->first();
            $danhSachMonHoc = DB::table('qlsv_khoadaotao_monhoc')->whereKdtId($khoaDaoTao->kdt_id)->get();
            foreach ($danhSachMonHoc as $monHoc) {
                $exists = DB::table('qlsv_lophoc_monhoc')
                    ->whereLhId($lopHoc->lh_id)
                    ->whereMhId($monHoc->mh_id)
                    ->whereLhMhIndex($monHoc->kdt_mh_index)
                    ->whereLhMhHocky($monHoc->kdt_mh_hocky)
                    ->exists();
                if (!$exists) {
                    DB::table('qlsv_lophoc_monhoc')->insert([
                        'lh_id' => $lopHoc->lh_id,
                        'mh_id' => $monHoc->mh_id,
                        'lh_mh_index' => $monHoc->kdt_mh_index,
                        'lh_mh_hocky' => $monHoc->kdt_mh_hocky
                    ]);
                }
            }

            $lopHoc->lh_hocky = $khoaDaoTao->kdt_hocky;
            $lopHoc->save();
        }
    }
}
