<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guard = 'admin';

    protected $table = 'admins';
    protected $primarykey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type',
        'user_level',
        'company_id',
        'department_id',
        'name', 
        'title', 
        'email', 
        'password',
        'mobile',
        'firstname',
        'lastname',
        'company',
        'city',
        'province',
        'address',
        'image',
        'email_verified_at',
        'is_verified',
        'active',
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

    //define superadmin role
    public function role($role) {
        $role = (array)$role;
        return in_array($this->role, $role);
    }
}
