<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'pm25_threshold',
        'pm10_threshold',
        'co_threshold',
        'no2_threshold',
        'enable_alert',
        'alert_message',
    ];
}
