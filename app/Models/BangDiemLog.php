<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BangDiemLog extends Model
{
    use HasFactory;

    protected $table = 'qlsv_bangdiem_log';

    protected $fillable = [
        'bd_id', 'sv_id', 'user_id', 'ngaycapnhat', // thêm các cột khác của bảng log nếu có
    ];

    public function bangDiem()
    {
        return $this->belongsTo(BangDiem::class, 'bd_id', 'bd_id');
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sv_id', 'sv_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
