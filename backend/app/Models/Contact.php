<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'purpose_of_contact',
        'organisation',
        'message',
    ];
}
