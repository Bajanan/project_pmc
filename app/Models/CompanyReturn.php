<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'CRN_No',
        'supplier_id',
        'date',
        'total_cost',
        'reason',
        'notes'

    ];

    public function Supplier(){
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function stock(){
        return $this->hasMany(Stock::class, 'GRN_No/CRN_No/Stock_adjustment', 'CRN_No');
     }

     public function getStaffMember(){

        $staff_id = $this->stock()->latest()->value('staff_id');
         return User::where('id',$staff_id)->value('name');
      }

}
