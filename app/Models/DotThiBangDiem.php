<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotThiBangDiem extends Model
{
    use HasFactory;

    protected $table = 'qlsv_dotthi_bangdiem';

    protected $primaryKey = 'dt_bd_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lh_id',  'mh_id', 'userid','dt_id', 'dt_bd_id', 'dt_bd_tungay', 'dt_bd_denngay'
    ];
}
