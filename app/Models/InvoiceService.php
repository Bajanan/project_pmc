<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceService extends Model
{
    use HasFactory;

    protected $table="invoice_service";

    protected $fillable = [
        'invoice_no',
        'service_id',
        'qty',
        'total_amount'
    ];

    public function service(){
        return $this->hasMany(Service::class, 'id','service_id');
    }



}
