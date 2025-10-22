<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $table = 'business_profile';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'logo',
    ];
}
