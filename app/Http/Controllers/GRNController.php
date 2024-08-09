<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\GRN;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\PackSize;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GRNController extends Controller
{
    public function index()
    {
        $all = GRN::orderBy('id', 'desc')->get();
        return view('grn.index', compact('all'));
    }

    public function create()
    {

        //creating GRN no (ex:year+random 4 digit no)
        $year = Carbon::now()->format('y');
        $current_date = Carbon::now()->format('Y-m-d');

        $latest_grnid = GRN::latest()->first();
        if ($latest_grnid) {
            $current_grnid = $latest_grnid->id;
        }
        else{
            $current_grnid = 0;
        }
        $new_grnid = $current_grnid + 1;
        $GRN_No = "GRN" . $year . str_pad($new_grnid, 4, '0', STR_PAD_LEFT);

        $suppliers = User::where('user_role', User::SUPPLIER)->orderBy('name', 'asc')->get();

        return view('grn.create', compact('suppliers', 'GRN_No', 'current_date'));
    }

    public function show(string $id)
    {

        $grn = GRN::find($id);
        return view('grn.view', compact('grn'));
    }

    public function store(Request $request)
    {
        if ($request->overall_cost === null || $request->overall_cost == '0') {
            return back()->with('error', "GRN Failed, no records added.");
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
                    'new_batch' => $request->input('new_batch')[$index] ?? null,
                    'batch' => $request->input('batch')[$index] ?? null,
                    'expire_date' => $request->input('expire_date')[$index],
                    'pack_value' => $request->input('pack_value')[$index],
                    'pack_qty' => $request->input('pack_qty')[$index],
                    'FOC' => $request->input('FOC')[$index],
                    'unit_cost' => $request->input('unit_cost')[$index],
                    'unit_retail' => $request->input('unit_retail')[$index],
                    'total_cost' => $request->input('total_cost')[$index],
                    'total_units' => $request->input('total_units')[$index]
                ];
            }
        }
    }


        DB::transaction(function () use ($request, $filteredRows) {

            $GRN = GRN::create([
                'GRN_No' => $request->GRN_No,
                'supplier_id' => $request->supplier,
                'invoice_no' => $request->invoice_no,
                'date' => $request->date,
                'total_cost' => $request->overall_cost
            ]);
            foreach ($filteredRows as $index2 => $data) {

                if(isset($filteredRows[$index2]['new_batch']) && !empty($filteredRows[$index2]['new_batch'])) {
                    $batch_name =  $filteredRows[$index2]['new_batch'];
                    $batch_id = Batch::where('batch_name', $batch_name)->value('id');
                }
                else {
                    $batch_id =  $filteredRows[$index2]['batch'];
                }

                $batch_update = Batch::where('id',$batch_id)->update([
                     'cost_price' => $filteredRows[$index2]['unit_cost'] / $filteredRows[$index2]['pack_value'],
                     'retail_price' => $filteredRows[$index2]['unit_retail'] / $filteredRows[$index2]['pack_value'],
                     'total_cost_price' => $filteredRows[$index2]['unit_cost'],
                     'total_retail_price' => $filteredRows[$index2]['unit_retail'],
                     'expire_date' => $filteredRows[$index2]['expire_date']
                ]);

                $current_units = 0;
                $latest_stock = Stock::where('batch_id', $batch_id)->latest()->first();
                if ($latest_stock) {
                    $current_units = $latest_stock->total_units;
                }
                $new_total_units = $current_units + ($filteredRows[$index2]['total_units']);

                Stock::create([

                    'GRN_No/CRN_No/Stock_adjustment' => $GRN->GRN_No,
                    'product_id' => $filteredRows[$index2]['product'],
                    'batch_id' =>   $batch_id,
                    'qty' => $filteredRows[$index2]['total_units'],
                    'pack_qty' => $filteredRows[$index2]['pack_qty'],
                    'total_grn_cost' => $filteredRows[$index2]['total_cost'],
                    'free_qty' => $filteredRows[$index2]['FOC'],
                    'total_units' => $new_total_units,
                    'staff_id' => Auth::user()->id
                ]);
            }
        });

        return back()->with('status', "GRN saved successfully");
    }

    public function filterProducts(Request $request)
    {

        $products = Product::where('supplier_id', $request->Supplier)->get();
        return response()->json(['products' => $products]);
    }

    public function getPackSize(Request $request)
    {
        $packsize = PackSize::where('id', $request->id)->get();
        return response()->json(['packsize' => $packsize]);
    }


    public function filterBatches(Request $request)
    {
        $batches = Batch::where('product_id', $request->Product)->where('expire_date','>',Carbon::now()->format('Y-m-d'))->get();
        return response()->json(['batches' => $batches]);
    }


    public function productUnits(Request $request)
    {
        $product = Product::findOrFail($request->Product);
        $pack_size_value = $product->packSize->pack_size_value;
        return response()->json(['unit_value' =>  $pack_size_value]);
    }

    public function batchNameStore(Request $request){

        Batch::create([
            'batch_name'=>$request->Batch,
            'product_id'=>$request->Product
        ]);
        return response()->json([
            "batch"=>$request->Batch
        ]);
    }

    public function batchDetails(Request $request)
    {
        $details = Batch::findOrFail($request->Batch);
        return response()->json(['batch_details' => $details]);
    }

}
