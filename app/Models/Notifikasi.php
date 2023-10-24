<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';

    protected $fillable = [
        'receiver_id',
        'receiver_type',
        'publisher_id',
        'publisher_type',
        'desc',
        'status',
        'date'
    ];
}
