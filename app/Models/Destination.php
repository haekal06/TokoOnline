<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Destination extends Model
{
    protected $fillable = [
        'external_id',
        'label',
        'province_name',
        'city_name',
        'district_name',
        'subdistrict_name',
        'zip_code'
    ];
}
