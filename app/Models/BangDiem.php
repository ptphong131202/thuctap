<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BangDiem extends Model
{
    use HasFactory;

    protected $table = 'qlsv_bangdiem';

    protected $primaryKey = 'bd_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lh_id', 'kdt_hocky', 'mh_id', 'userid', 'bd_id', 'bd_tungay', 'bd_denngay', 'bd_giangvien', 'bd_type'
    ];

    /**
     * Lấy các lớp học thuộc khóa đào tạo
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function lopHoc()
    {
        return $this->hasOne(LopHoc::class, 'lh_id', 'lh_id');
    }
}
