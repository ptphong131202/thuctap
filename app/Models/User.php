<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserPermission;
use App\Models\Permission;
use App\Notifications\PasswordReset;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type', 'status', 'username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $softDelete = true;

    /**
     * Lấy danh sách quyền cửa người dùng
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions', 'user_id', 'permission_id');
    }

    /**
     * Lấy danh sách id_permission của người dùng
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function userPermission()
    {
        $secondCache = 2;
        return \Cache::remember('users', $secondCache, function () {
            return $this->hasMany(UserPermission::class, 'user_id', 'user_id')->pluck('permission_id');
        });
    }

    /**
     * Là super admin
     * @return bool
     * @author ttdat
     * @version 1.0
     */
    public function isSuperAdmin()
    {
        return $this->user_id == __c('super_admin.user_id');
    }

    /**
     * Kiểm tra quyền hạn
     */
    public function hasPermission(string $permissions)
    {
        if ($permissions == \App\Enums\Permission::STUDENT) {
            return $this->userPermission()->contains($permissions);
        }
        $superAdmin = $this->user_id === __c('super_admin.user_id');
        $coquyen = false;
        foreach(explode(",", $permissions) as $permission) {
            if($this->userPermission()->contains($permission)){
                $coquyen = true;
            }
        }
        return $superAdmin || $coquyen;
    }

    /**
     * Lấy niên khóa
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    public function canBo()
    {
    	return $this->hasOne(CanBo::class, 'user_id', 'user_id');
    }

    public function sinhVien()
    {
        return $this->hasOne(SinhVien::class, 'user_id', 'user_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }
}
