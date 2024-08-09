<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'brand_name',
        'generic_name',
        'product_name',
        'category',
        'pack_size_id',
        'active_status',
        'barcode'
    ];

public function packSize(){
    return $this->belongsTo(PackSize::class, 'pack_size_id');
}

public function batch(){
    return $this->hasMany(Batch::class, 'product_id', 'id');
}

public function supplier(){
    return $this->belongsTo(User::class, 'supplier_id');
}


}
