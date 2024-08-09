<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockAdjustment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $all = StockAdjustment::orderBy('id', 'desc')->get();
        return view('stock-adjustment.index', compact('all'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $year = Carbon::now()->format('y');
        $current_date = Carbon::now()->format('Y-m-d');

        $latest_said = StockAdjustment::latest()->first();
        if ($latest_said) {
            $current_said = $latest_said->id;
        }
        else{
            $current_said = 0;
        }
        $new_said = $current_said + 1;

        $SA_No = "SA" . $year . str_pad($new_said, 4, '0', STR_PAD_LEFT);

        $products = Product::all();
        return view('stock-adjustment.create', compact('SA_No', 'current_date', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if ($request->overall_cost === null || $request->overall_cost == '0') {
            return back()->with('error', "Stock Adjustment Failed, no records added.");
        }
        else{
        $filteredRows = [];

            // Iterate through the submitted rows
            foreach ($request->input('product') as $index => $product) {
                // Check if all fields in the row are not null
                if (!empty($request->input('expire_date')[$index])) {
                    // If all fields are not null, add the row to the filtered array


                    $filteredRows[] = [
                        'product' => $product,
                        'batch' => $request->input('batch')[$index],
                        'expire_date' => $request->input('expire_date')[$index],
                        'qty' => $request->input('qty')[$index],
                        'unit_cost' => $request->input('unit_cost')[$index],
                        'unit_retail' => $request->input('unit_retail')[$index],
                        'total_cost' => $request->input('total_cost')[$index],

                    ];
                }
            }
        }
        // dd($filteredRows);

        DB::transaction(function () use ($filteredRows,$request) {

            $stock_adjustment = StockAdjustment::create([
                'SA_No' => $request->SA_No,
                'date' => $request->date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_cost' => $request->overall_cost
            ]);

            foreach($filteredRows as $index2=>$data){

            $current_unit = 0;
            $current_units = Stock::where('batch_id', ($filteredRows[$index2]['batch']))->latest()->first();
            if ($current_units) {
                $current_unit = $current_units->total_units;
            }
            $new_total_units = $current_unit + ($filteredRows[$index2]['qty']);

            Stock::create([

                'GRN_No/CRN_No/Stock_adjustment' => $stock_adjustment->SA_No,
                'batch_id' =>  $filteredRows[$index2]['batch'],
                'date' => $request->date,
                'qty' => $filteredRows[$index2]['qty'],
                'total_units' =>  $new_total_units,
                'staff_id' => Auth::user()->id
            ]);
        }
    });
    return back()->with('status', "Stock Adjustment saved successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock_adjustment = StockAdjustment::findOrFail($id);
        return view('stock-adjustment.view',compact('stock_adjustment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function filterBatches(Request $request)
    {

        $batches = Batch::where('product_id', $request->Product)->where('expire_date','>',Carbon::now()->format('Y-m-d'))->get();
        $batch_result = [];

        foreach($batches as $batch){
            $stock_available_batches = Stock::where('batch_id', $batch->id)->latest()->first();
            $total_units = $stock_available_batches ? $stock_available_batches->total_units : 0;

            $batch_result[] = [
                'id' => $batch->id,
                'batch_name' => $batch->batch_name,
                'total_units' => $total_units
            ];
        }

        return response()->json(['batches' => $batch_result]);
    }

    public function batchDetails(Request $request)
    {
        $details = Batch::findOrFail($request->Batch);
        return response()->json(['batch_details' => $details]);
    }
}
