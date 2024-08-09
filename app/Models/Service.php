<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_type',
        'description',
        'unit_price'
    ];

    public function bill(){
       return $this->belongsToMany(Bill::class,'invoice_service','service_id','invoice_no');
    }

   
}
