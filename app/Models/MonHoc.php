<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonHoc extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_monhoc';

    protected $primaryKey = 'mh_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hdt_id', 'nn_id', 'mh_ma', 'mh_ten', 'mh_ghichu', 'mh_sodonvihoctrinh', 'mh_giangvien', 'mh_sotiet', 'mh_tichluy', 'mh_loai'
    ];

    public function bangDiem()
    {
        return $this->hasMany(BangDiem::class, 'mh_id', 'mh_id');
    }

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

    public function bangDiemLogs()
    {
        return $this->hasManyThrough(BangDiemLog::class, 'mh_id', 'mh_id');
    }
}
