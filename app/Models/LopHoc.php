<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LopHoc extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_lophoc';

    protected $primaryKey = 'lh_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lh_ma', 'lh_ten', 'kdt_id', 'nk_id', 'lh_ghichu', 'qd_id', 'lh_nienche'
    ];

    /**
     * Lấy khóa đào tạo
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function khoaDaoTao()
    {
    	return $this->hasOne(KhoaDaoTao::class, 'kdt_id', 'kdt_id');
    }

    /**
     * Lấy niên khóa
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function nienKhoa()
    {
    	return $this->hasOne(NienKhoa::class, 'nk_id', 'nk_id');
    }

    /**
     * Lấy quyết đinh
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function quyetDinh()
    {
    	return $this->hasOne(QuyetDinh::class, 'qd_id', 'qd_id');
    }

    /**
     * Lấy các lớp
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function sinhVien()
    {
        return $this->belongsToMany(SinhVien::class, 'qlsv_sinhvien_lophoc', 'lh_id', 'sv_id');
    }

    public function bangDiem()
    {
        return $this->hasMany(BangDiem::class, 'lh_id', 'lh_id');
    }

    public function dotxettotnghiep_sinhvien()
    {
        return $this->hasMany(DotXetTotNghiepSinhVien::class, 'lh_id', 'lh_id');
    }


    /**
     * Lấy các môn học
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function monHoc()
    {
        return $this->belongsToMany(MonHoc::class, 'qlsv_lophoc_monhoc', 'lh_id', 'mh_id')->withPivot(['lh_mh_hocky', 'lh_mh_index']);
    }

    public function bangDiemLogs()
    {
        return $this->hasManyThrough(BangDiemLog::class, 'lh_id', 'lh_id');
    }

}
