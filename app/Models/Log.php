<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'qlsv_log';

    protected $primaryKey = 'log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_id', 'sv_id', 'type', 'status', 'token', 'created_at'
    ];


    public function sinhvien()
    {
        return $this->hasOne(SinhVien::class, 'sv_id', 'sv_id');
    }
}
