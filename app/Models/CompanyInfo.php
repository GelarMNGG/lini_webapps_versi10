<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $table = 'company_info';
    protected $primarykey = 'id';

    protected $fillable = [
        'name',
        'rek',
        'phone',
        'mobile',
        'email',
        'url',
        'brief',
        'keywords',
        'slogan',
        'address',
        'map',
        'logo',
        'year',
    ];

}
