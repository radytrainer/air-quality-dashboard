<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirQualityData extends Model
{
    use HasFactory;
    protected $fillable = [
        "location_id",
        "location",
        "city",
        "country",
        "parameter",
        "value",
        "unit",
        "last_updated",
        "raw_data"
    ];
}
