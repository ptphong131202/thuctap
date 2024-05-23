<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuyetDinh extends Model
{
    use SoftDeletes, HasFactory, SoftDeletes;

    protected $table = 'qlsv_quyetdinh';

    protected $primaryKey = 'qd_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'qd_ma', 'qd_ten', 'qd_ngay', 'qd_loai'
    ];

    public function sinhVien()
    {
        return $this->belongsToMany(SinhVien::class, 'qlsv_sinhvien_quyetdinh', 'qd_id', 'sv_id');
    }

    public function sinhVienThemLop()
    {
        return $this->belongsToMany(SinhVien::class, 'qlsv_sinhvien_quyetdinh', 'qd_id', 'sv_id')->where('qd_loai', 0);
    }

    public function sinhVienTotNghiep()
    {
        return $this->belongsToMany(SinhVien::class, 'qlsv_sinhvien_quyetdinh', 'qd_id', 'sv_id')->where('qd_loai', 1);
    }

    public function sinhVienXoaTen()
    {
        return $this->belongsToMany(SinhVien::class, 'qlsv_sinhvien_quyetdinh', 'qd_id', 'sv_id')->where('qd_loai', 2);
    }

    public function lopHoc()
    {
        return $this->hasMany(LopHoc::class, 'qd_id', 'qd_id');
    }
}
