<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotXetTotNghiepSinhVien extends Model
{
    use HasFactory;

    protected $table = 'qlsv_dotxettotnghiep_sinhvien';

    protected $primaryKey = 'sv_id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'sv_id',
        'dxtn_id',
        'dt_id',
        'svxtn_dattn',
        'svxtn_ghichu',
    ];

}
