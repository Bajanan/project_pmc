<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
       
        'SA_No',
        'date',
        'reason',
        'notes',
        'total_cost'
    ];

    public function stock(){
        return $this->hasMany(Stock::class, 'GRN_No/CRN_No/Stock_adjustment', 'SA_No');
     }

     public function getStaffMember(){

        $staff_id = $this->stock()->latest()->value('staff_id');
         return User::where('id',$staff_id)->value('name');
      }
}
