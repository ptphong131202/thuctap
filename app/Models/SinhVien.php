<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SinhVien extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_sinhvien';

    protected $primaryKey = 'sv_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','sv_ma', 'sv_ten', 'sv_ho', 'sv_gioitinh', 'sv_dantoc', 'sv_diachi', 'sv_ngaysinh','sv_email', 'sv_trinhdo', 'sv_ghichu',
        'sv_ngaynghi', 'sv_sdt',
    ];

    /**
     * Lấy user
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function user()
    {
    	return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    public function sinhVienBangDiem()
    {
        return $this->belongsToMany(BangDiem::class, 'qlsv_sinhvien_diem', 'sv_id', 'bd_id');
    }

    public function sinhVienDotThiBangDiem()
    {
        return $this->belongsToMany(DotThiBangDiem::class, 'qlsv_dotthi_diem', 'sv_id', 'dt_bd_id');
    }

    public function quyetDinhThemLop()
    {
    	return $this->belongsToMany(QuyetDinh::class, 'qlsv_sinhvien_quyetdinh', 'sv_id', 'qd_id')->where('qd_loai', 0);
    }

    public function quyetDinhTotNghiep()
    {
    	return $this->belongsToMany(QuyetDinh::class, 'qlsv_sinhvien_quyetdinh', 'sv_id', 'qd_id')->where('qd_loai', 1)->withPivot(['qd_hocky', 'sv_id', 'qd_id']);
    }

    public function quyetDinhXoaTen()
    {
    	return $this->belongsToMany(QuyetDinh::class, 'qlsv_sinhvien_quyetdinh', 'sv_id', 'qd_id')->where('qd_loai', 2)->withPivot(['qd_hocky', 'sv_id', 'qd_id']);
    }
    /**
     * Lấy các lớp
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function lopHoc()
    {
        return $this->belongsToMany(LopHoc::class, 'qlsv_sinhvien_lophoc', 'sv_id', 'lh_id');
    }

    public function Log()
    {
        return $this->hasMany(Log::class, 'sv_id', 'sv_id');
    }
}
