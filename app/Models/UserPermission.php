<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserPermission extends Model
{
    protected $table = 'users_permissions';

    protected $primaryKey = ['user_id', 'permission_id'];
    
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'permission_id'
    ];

    // /**
    //  * Set the keys for a save update query.
    //  *
    //  * @param  \Illuminate\Database\Eloquent\Builder  $query
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */
    // protected function setKeysForSaveQuery(Builder $query)
    // {
    //     $keys = $this->getKeyName();
    //     if(!is_array($keys)){
    //         return parent::setKeysForSaveQuery($query);
    //     }

    //     foreach($keys as $keyName){
    //         $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
    //     }

    //     return $query;
    // }

    // /**
    //  * Get the primary key value for a save query.
    //  *
    //  * @param mixed $keyName
    //  * @return mixed
    //  */
    // protected function getKeyForSaveQuery($keyName = null)
    // {
    //     if(is_null($keyName)){
    //         $keyName = $this->getKeyName();
    //     }

    //     if (isset($this->original[$keyName])) {
    //         return $this->original[$keyName];
    //     }

    //     return $this->getAttribute($keyName);
    // }
}
