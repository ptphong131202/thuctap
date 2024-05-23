<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotThiDiem extends Model
{
    use HasFactory;

    protected $table = 'qlsv_dotthi_diem';

    protected $primaryKey = 'dt_bd_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'svd_first','svd_ghichu', 'svd_dieukien', 'svd_lan', 'svd_loai'
    ];

    public function SinhVien()
    {
        return $this->hasMany(SinhVien::class, 'sv_id', 'sv_id');
    }
    public function DotThiBangDiem()
    {
        return $this->hasMany(DotThiBangDiem::class, 'dt_bd_id', 'dt_bd_id');
    }

}
