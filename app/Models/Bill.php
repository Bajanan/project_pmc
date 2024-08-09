<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    const PENDING = "pending";
    const CANCELLED = "cancelled";
    const PAID = "paid";

    use HasFactory;
    protected $with = [
        'service'
    ];

    protected $fillable = [
        'invoice_no',
        'patient_id',
        'staff_id',
        'total_invoice',
        'payable_amount',
        'discount',
        'paid_amount',
        'due_amount',
        'date',
        'doctor_id',
        'status'

    ];

    public function service(){
       return $this->belongsToMany(Service::class,'invoice_service','invoice_no','service_id')->withPivot('qty');
    }

    public function patient(){
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function repayments(){
        return $this->hasMany(Repayments::class, 'invoice_no','invoice_no');
    }

    public function stock(){
        return $this->hasMany(Stock::class, 'GRN_No/CRN_No/Stock_adjustment','invoice_no');
    }

    public function serviceInvoice(){
        return $this->hasMany(InvoiceService::class, 'invoice_no','invoice_no');
    }


}
