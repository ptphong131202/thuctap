<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DotThi extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_dotthi';

    protected $primaryKey = 'dt_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dt_hdt_id', 'dt_ten', 'dt_loai', 'dt_tungay', 'dt_tuthang', 'dt_tunam', 'dt_denngay', 'dt_denthang', 'dt_dennam', 'dt_ghichu', 'dt_qd_trangthai', 'qd_id', 'dt_so_ke_hoach'
    ];


    public function dotXetTotNghiep()
    {
        return $this->belongsToMany(DotXetTotNghiep::class, 'qlsv_dotthi_dotxettotnghiep', 'dt_id', 'dxtn_id');
    }

    public function dotThiBangDiem()
    {
        return $this->hasMany(DotThiBangDiem::class, 'dt_id', 'dt_id');
    }

    public function quyetDinh()
    {
        return $this->belongsTo(QuyetDinh::class, 'qd_id', 'qd_id');
    }

}
