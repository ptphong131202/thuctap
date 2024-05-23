<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DotThiDotXetTotNghiep extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_dotthi_dotxettotnghiep';

    protected $primaryKey = 'dxtn_id';


    public function DotXetTotNghiep()
    {
        return $this->belongsTo(DotXetTotNghiep::class, 'qlsv_dotxettotnghiep', 'dxtn_id');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dxtn_ten', 'dxtn_tungay', 'dxtn_tuthang', 'dxtn_tunam', 'dxtn_denngay', 'dxtn_denthang', 'dxtn_dennam', 'dxtn_ghichu'
    ];

}
