<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, SoftDeletes;

    protected $guard = 'cust';

    protected $table = 'customers';
    protected $primarykey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'user_type',
        'name', 
        'title', 
        'email', 
        'password',
        'mobile',
        'firstname',
        'lastname',
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
