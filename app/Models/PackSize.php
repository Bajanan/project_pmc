<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackSize extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'pack_size',
        'pack_size_value'
        
    ];
}
