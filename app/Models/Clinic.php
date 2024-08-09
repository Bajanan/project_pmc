<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_name',
        'email',
        'phone',
        'mobile',
        'bill_message',
        'clinic_address',
        // Add other fields here if needed
    ];
}
