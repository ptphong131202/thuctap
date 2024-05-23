<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NganhNghe extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'qlsv_nganhnghe';

    protected $primaryKey = 'nn_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nn_ten','nn_ma','hdt_id'
    ];

    /**
     * Lấy các lớp học thuộc hệ đào tạo
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function monHoc()
    {
        return $this->hasMany(MonHoc::class, 'nn_id', 'nn_id');
    }

    public function khoaDaoTao()
    {
        return $this->hasMany(KhoaDaoTao::class, 'nn_id', 'nn_id');
    }

    public function heDaoTao()
    {
    	return $this->hasOne(HeDaoTao::class, 'hdt_id', 'hdt_id');
    }
}
