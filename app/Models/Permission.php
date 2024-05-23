<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $primaryKey = 'permission_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'permission_id'
    ];

    /**
     * Không sử dụng timestamp
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Không tự động tăng ID
     * @var boolean
     */
    public $incrementing = false;

    /**
     * ID là chuỗi
     * @var string
     */
    public $keyType = 'string';
}
