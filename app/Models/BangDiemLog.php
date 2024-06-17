<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BangDiemLog extends Model
{
    use HasFactory;

    protected $table = 'qlsv_bangdiem_log';
    protected $primaryKey = 'bd_log_id';
    public $incrementing = false; // Nếu bd_id không tự động tăng
    protected $keyType = 'string'; // Nếu bd_id không phải kiểu integer

    protected $fillable = [
        'bd_log_id ',
        'lh_id',
        'mh_id',
        'bd_id',
        'sv_id',
        'user_id',
        'svd_dulop',
        'svd_second_hocky',
        'svd_first',
        'svd_second',
        'svd_final',
        'svd_total',
        'svd_ghichu',
        'svd_third',
        'svd_third_hocky',
        'svd_exam_first',
        'svd_exam_second',
        'svd_exam_third',
    ];

    public function bangDiem()
    {
        return $this->belongsTo(BangDiem::class, 'bd_id', 'bd_id');
    }
    // Định nghĩa mối quan hệ belongsTo với model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Một người dùng có thể thuộc về một cán bộ.
     */
    public function canBo()
    {
        return $this->hasOne(CanBo::class, 'user_id', 'user_id');
    }
}

