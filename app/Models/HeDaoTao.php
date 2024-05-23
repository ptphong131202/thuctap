<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeDaoTao extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_hedaotao';

    protected $primaryKey = 'hdt_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hdt_ten',
    ];

    /**
     * Lấy các lớp học thuộc hệ đào tạo
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function khoaDaoTao()
    {
        return $this->hasMany(KhoaDaoTao::class, 'hdt_id', 'hdt_id');
    }

    
    public function nganhNghe()
    {
        return $this->hasMany(NganhNghe::class, 'nn_id', 'hdt_id');
    }
}
