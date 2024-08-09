<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [

        'GRN_No/CRN_No/Stock_adjustment',
        'batch_id',
        'qty',
        'free_qty',
        'staff_id',
        'total_units',
        'pack_qty',
        'total_grn_cost',
        'payable_amount',
        'discount_rate',
        'unit_price'
    ];
protected $with = [
    'batch'
];

    public function batch(){
        return $this->belongsTo(Batch::class , 'batch_id');
    }

    public function staff(){
        return $this->belongsTo(User::class , 'staff_id');
    }





}
