<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRN extends Model
{
    use HasFactory;
    
    protected $table = "GRN";

    protected $fillable = [
        'GRN_No',
        'supplier_id',
        'invoice_no',
        'date',
        'total_cost',
    
        
    ];


 public function Supplier(){
        return $this->belongsTo(User::class, 'supplier_id');
    }

 public function stock(){
    return $this->hasMany(Stock::class, 'GRN_No/CRN_No/Stock_adjustment', 'GRN_No');
 }

 public function getStaffMember(){

   $staff_id = $this->stock()->latest()->value('staff_id');
    return User::where('id',$staff_id)->value('name');
 }
    
}
