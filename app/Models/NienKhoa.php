<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NienKhoa extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_nienkhoa';

    protected $primaryKey = 'nk_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nk_ten'
    ];

    /**
     * Lấy các lớp học thuộc niên khóa
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function lopHoc()
    {
        return $this->hasMany(LopHoc::class, 'nk_id', 'nk_id');
    }
}
