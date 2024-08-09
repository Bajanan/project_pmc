<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Repayments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepaymentsController extends Controller
{
   public function store(Request $request){
    // dd($request->all());
    // if($request->paid_amount > $request->balance_amount){

    //     return back()->with('error',"thedfadfa");
    // }

   /*  $request->merge([
        'staff_id' => Auth::user()->id,
        'date' => Carbon::now()->format('Y-m-d')
    ]);
    DB::transaction(function ()use($request) {
        Repayments::create($request->all());

        Bill::where('invoice_no', $request->invoice_no)->update(['due_amount' => DB::raw('due_amount -'.$request->paid_amount)]);
    });

    return back(); */

   }

   public function payAll(Request $request){
        // Fetch all bills with due amount
        $bills = Bill::where('patient_id', $request->patient_id)
                 ->where('due_amount', '>', 0)
                 ->where('status', 'paid')
                 ->orderBy('id', 'asc')
                 ->get();

        $total_due = $request->total_due;
        $remaining_paid_amount = $request->paid_amount;
        $paid_amount = 0;

        // Loop through each bill and pay dues until total due is covered
        foreach($bills as $bill){

            if($remaining_paid_amount <= 0) break;

            $due_amount = $bill->due_amount;

            $new_paid_amount = min($remaining_paid_amount, $due_amount);

            $bill->update([
                'due_amount' => max(0, $due_amount - $new_paid_amount), // Ensure due_amount never goes negative
                'paid_amount' => $bill->paid_amount + $new_paid_amount
            ]);

            $total_due -= $new_paid_amount;
            $remaining_paid_amount -= $new_paid_amount;
            $paid_amount += $new_paid_amount;


        }

        // Record repayment for the total paid amount
        Repayments::create([
            'staff_id' => Auth::user()->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'invoice_no' => $request->patient_id, // or any other identifier you prefer
            'paid_amount' => $paid_amount
        ]);

        return back()->with('success', 'Dues paid successfully.');
    }


}
