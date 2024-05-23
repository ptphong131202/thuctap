<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DotXetTotNghiep extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_dotxettotnghiep';

    protected $primaryKey = 'dxtn_id';


    public function dotThi()
    {
        return $this->belongsToMany(DotThi::class, 'qlsv_dotthi_dotxettotnghiep', 'dxtn_id', 'dt_id');
    }

    public function DotThiDotXetTotNghiep()
    {
        return $this->hasMany(DotThiDotXetTotNghiep::class, 'qlsv_dotthi_dotxettotnghiep', 'dxtn_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dxtn_hdt_id', 'dxtn_ten', 'dxtn_tungay', 'dxtn_tuthang', 'dxtn_tunam', 'dxtn_denngay', 'dxtn_denthang', 'dxtn_dennam', 'dxtn_ghichu', 'dxtn_qd_trangthai', 'qd_id'
    ];
    public function quyetDinh()
    {
        return $this->belongsTo(QuyetDinh::class, 'qd_id', 'qd_id');
    }

}
