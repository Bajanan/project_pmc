<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\CompanyReturn;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyReturnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $all_company_returns = CompanyReturn::orderBy('id', 'desc')->get();
        return view('company-return.index', compact('all_company_returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $year = Carbon::now()->format('y');
        $current_date = Carbon::now()->format('Y-m-d');

        $latest_crnid = CompanyReturn::latest()->first();
        if ($latest_crnid) {
            $current_crnid = $latest_crnid->id;
        }
        else{
            $current_crnid = 0;
        }
        $new_crnid = $current_crnid + 1;

        $CRN_No = "CRN" . $year . str_pad($new_crnid, 4, '0', STR_PAD_LEFT);
        $suppliers = User::where('user_role', User::SUPPLIER)->orderBy('name', 'asc')->get();
        $batches = Batch::all();
        return view('company-return.create', compact('CRN_No', 'current_date', 'suppliers', 'batches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->overall_cost === null || $request->overall_cost == '0') {
            return back()->with('error', "Company Return Failed, no records added.");
        }
        else{
        $filteredRows = [];
        // Iterate through the submitted rows
        foreach ($request->input('product') as $index => $product) {
            // Check if all fields in the row are not null

                if (!empty($request->input('expiry_date')[$index])) {
                    // If all fields are not null, add the row to the filtered array
                    // dd($request->all());
                    $filteredRows[] = [
                        'product' => $product,
                        'batch' => $request->input('batch')[$index],
                        'expire_date' => $request->input('expiry_date')[$index],
                        'qty' => $request->input('qty')[$index],
                        'unit_cost' => $request->input('unit_cost')[$index],
                        'unit_retail' => $request->input('unit_retail')[$index],
                        'total_cost' => $request->input('total_cost')[$index],

                    ];
                }
            }

        }

        DB::transaction(function () use ($filteredRows, $request) {

            $CRN = CompanyReturn::create([
                'CRN_No' => $request->CRN_No,
                'supplier_id' => $request->supplier,
                'date' => $request->date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_cost' => $request->overall_cost
            ]);

            foreach ($filteredRows as $index2 => $data) {
                //get the current total units and minus the qty of the product

                $current_unit = 0;
                $current_units = Stock::where('batch_id', ($filteredRows[$index2]['batch']))->latest()->first();
                if ($current_units) {
                    $current_unit = $current_units->total_units;
                }
                $new_total_units = $current_unit - ($filteredRows[$index2]['qty']);

                Stock::create([
                    'product_id' => $filteredRows[$index2]['product'],
                    'GRN_No/CRN_No/Stock_adjustment' => $CRN->CRN_No,
                    'batch_id' =>  $filteredRows[$index2]['batch'],
                    'date' => $request->date,
                    'qty' => $filteredRows[$index2]['qty'],
                    'total_units' => $new_total_units,
                    'staff_id' => Auth::user()->id
                ]);
            }
        });
        return back()->with('status', "Company Return saved successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company_return = CompanyReturn::findOrFail($id);
       return view('company-return.view',compact('company_return'));
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

    public function filterProducts(Request $request)
    {
        //getting the products that have stocks only
        $products = [];
        $stocks = Stock::select('batch_id')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('stocks')
                    ->groupBy('batch_id');
            })
            ->where('total_units', '>', 0)
            ->get();

        foreach ($stocks as $stock) {
            $product = Batch::whereHas('Product', function($query) use ($request) {
                    $query->where('supplier_id', $request->Supplier);
                })
                ->where('id', $stock->batch_id)
                ->first();

            if ($product && !in_array($product->Product->id, array_column($products, 'id'))) {
                $products[] = [
                    'id' => $product->Product->id,
                    'product_name' => $product->Product->product_name
                ];
            }
        }

        return response()->json(['products' => $products]);
    }

    public function filterBatches(Request $request)
    {
        $batches = Batch::where('product_id', $request->Product)->get();
        $batch_result = [];

        foreach ($batches as $batch) {
            $stock_available_batches = Stock::where('batch_id', $batch->id)->latest()->first();
            if ($stock_available_batches && $stock_available_batches->total_units > 0) {
                $batch['stock'] = $stock_available_batches;
                $batch_result[] = $batch;
            }
        }

        return response()->json(['batches' => $batch_result]);
    }

    public function batchDetails(Request $request)
    {
        $details = Batch::findOrFail($request->Batch);
        return response()->json(['batch_details' => $details]);
    }

    public function productFilter(Request $request){




      return response()->json([
        "success"
      ]);
    }
}
