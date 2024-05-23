<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhoaDaoTao extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_khoadaotao';

    protected $primaryKey = 'kdt_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hdt_id', 'nn_id', 'kdt_ten', 'kdt_ma', 'kdt_hocky', 'kdt_ghichu', 'kdt_khoa', 'kdt_he'
    ];


    /**
     * Lấy các môn học
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function monHoc()
    {
        return $this->belongsToMany(MonHoc::class, 'qlsv_khoadaotao_monhoc', 'kdt_id', 'mh_id')->withPivot(['kdt_mh_hocky', 'kdt_mh_index', 'kdt_mh_apdung']);
    }

    /**
     * Lấy hệ đào tạo
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function heDaoTao()
    {
    	return $this->hasOne(HeDaoTao::class, 'hdt_id', 'hdt_id');
    }

    public function nganhNghe()
    {
    	return $this->hasOne(NganhNghe::class, 'nn_id', 'nn_id');
    }

    /**
     * Lấy các lớp học thuộc khóa đào tạo
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function lopHoc()
    {
        return $this->hasMany(LopHoc::class, 'kdt_id', 'kdt_id');
    }
}
