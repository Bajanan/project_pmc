<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = "batch";

    // protected $with = [
    //     'stock'
    // ];

    protected $fillable = [
        'batch_name',
        'total_cost_price',
        'total_retail_price',
        'cost_price',
        'retail_price',
        'expire_date',
        'product_id'
    ];

    protected $with = [
        'Product'
    ];

    public function Product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stock(){
        return $this->hasMany(Stock::class, 'batch_id','id');

    }
}
