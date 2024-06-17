<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanBo extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_canbo';

    protected $primaryKey = 'cb_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'cb_ten', 'cb_ho', 'cb_ma', 'cb_gioitinh', 'cb_chucvu'
    ];

    /**
     * Lấy các lớp học thuộc hệ đào tạo
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function user()
    {
        return $this->hasMany(User::class, 'user_id', 'user_id');
    }

    /**
     * Một cán bộ có nhiều bản ghi điểm thông qua user_id.
     */
    public function bangDiemLogs()
    {
        return $this->hasManyThrough(BangDiemLog::class, User::class, 'user_id', 'user_id', 'user_id', 'user_id');
    }
}
