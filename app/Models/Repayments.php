<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayments extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'invoice_no',
        'paid_amount',
        'date',
    ];

    public function patient(){
        return $this->belongsTo(User::class, 'invoice_no');
    }
}
