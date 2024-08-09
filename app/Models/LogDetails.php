<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'logged_in',
        'logged_out'
    ];
}
