<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    const  MEDICINE = "Medicine";
    const SURGICAL = "Surgical";
    const GROCERIES = "Groceries";
    
    }
