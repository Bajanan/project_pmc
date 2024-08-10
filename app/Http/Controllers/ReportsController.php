<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Batch;
use App\Models\Bill;
use App\Models\Repayments;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\BrandName;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportsController extends Controller
{
    public function createAppointments()
    {
        $doctors =  User::where('user_role', User::DOCTOR)->get();
        return view('reports.appointments', compact('doctors'));
    }

    public function appointments(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $selected_doctor = $request->doctor;
        $selected_shift = $request->shift;

        $appointments = Appointment::where('doctor_id', $selected_doctor)
        ->where('shift', $selected_shift)
        ->whereBetween('date', [$start_date, $end_date])->get();

        $totalDoctorPayment = $appointments->sum('doctor_payment');
        $totalHospitalFee = $appointments->sum('hospital_fee');

        $doctors =  User::where('user_role', User::DOCTOR)->orderBy('name', 'asc')->get();

        return view('reports.appointments', compact('doctors', 'appointments', 'totalDoctorPayment', 'totalHospitalFee', 'start_date', 'end_date'));
    }

    public function sales(Request $request)
    {
        $start_date = $request->input('start_date', date('Y-m-d'));
        $end_date = $request->input('end_date', date('Y-m-d'));

        $invoices = Bill::whereBetween('date', [$start_date, $end_date])
                ->where('status', 'paid')
                ->get();
        $total_sales = $invoices->sum('payable_amount');
        $total_due = $invoices->sum('due_amount');

        return view('reports.sales', compact('invoices', 'start_date', 'end_date', 'total_sales', 'total_due'));
    }

    public function salesInvoice($id){

        $invoice = Bill::where('id',$id)->first();
        return view('reports.invoice',compact('invoice'));
    }

    public function itemSales(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $item_sales = Stock::query();
        if ($request->filled('category')) {
            $category = $request->category;
            $item_sales->whereHas('batch.Product', function ($query) use ($request, $category) {
                $query->where('category', $category);
            });
        } else {
            $category = null;
        }

        $item_sales->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date])
            ->where(function ($query) {
                $query->where('GRN_No/CRN_No/Stock_adjustment', 'like', '%SINV%')
                    ->orWhere('GRN_No/CRN_No/Stock_adjustment', 'like', '%INV%');
            });

        $item_sales = $item_sales->get();

        return view('reports.itemsale', compact('item_sales', 'start_date', 'end_date', 'category'));
    }

    public function repayments()
    {
        $repayments = Repayments::with('patient')->get();
        return view('reports.repayment', compact('repayments'));
    }

    public function dues(Request $request)
    {

        $dues = Bill::where('due_amount', '>', 0)
                ->select('patient_id', DB::raw('SUM(due_amount) as total_due_amount'))
                ->groupBy('patient_id')
                ->get();

        $total_dues = $dues->sum('total_due_amount');

        return view('reports.due', compact('dues', 'total_dues'));
    }

    public function create_stock()
    {

        $suppliers = User::where('user_role', User::SUPPLIER)->orderBy('name', 'asc')->get();
        return view('reports.stock', compact('suppliers'));
    }

    public function stocks(Request $request)
    {

        $selected_Supplier = $request->supplier == 'All' ? 'All' : User::where('id', $request->supplier)->value('name');
    $suppliers = User::where('user_role', User::SUPPLIER)->orderBy('name', 'asc')->get();

    $short_expiry = $request->expiry_range;
    $selected_supplier_all = $request->supplier;

    // Base query for stocks
    $stocksQuery = Stock::distinct()->select('batch_id')->with('batch.Product');

    if ($request->supplier != 'All') {
        $stocksQuery->whereHas('batch.Product', function ($query) use ($request) {
            $query->where('supplier_id', $request->supplier);
        });
    }

    if ($short_expiry == '3months') {
        $stocksQuery->whereHas('batch', function ($query) {
            $query->whereBetween('expire_date', [Carbon::now(), Carbon::now()->addMonths(3)]);
        });
    } elseif ($short_expiry == '6months') {
        $stocksQuery->whereHas('batch', function ($query) {
            $query->whereBetween('expire_date', [Carbon::now(), Carbon::now()->addMonths(6)]);
        });
    } elseif ($short_expiry == 'expired') {
        $stocksQuery->whereHas('batch', function ($query) {
            $query->where('expire_date', '<=', Carbon::now()->format('Y-m-d'));
        });
    }

    $stocks = $stocksQuery->get();

    $totalUnitsSum = 0;
    foreach ($stocks as $stock) {
        $total_units = Stock::where('batch_id', $stock->batch_id)->latest()->value('total_units');
        $stock['total_units'] = $total_units;
        $totalUnitsSum += $total_units;
    }

    return view('reports.stock', compact('stocks', 'suppliers', 'selected_Supplier', 'totalUnitsSum', 'short_expiry'));

    }

    public function createStockMoving()
    {

        $products = Product::orderBy('product_name', 'asc')->get();
        return view('reports.moving', compact('products'));
    }

    public function filterBatch(Request $request)
    {

        $batches = Batch::where('product_id', $request->Product)->get();
        return response()->json([
            "batches" => $batches
        ]);
    }

    public function stockMoving(Request $request)
    {
        //    dd($request->all());
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $selected_product = Product::where('id',$request->product)->value('product_name');
        $selected_batch = Batch::where('id',$request->batch)->value('batch_name');

        $products = Product::orderBy('product_name', 'asc')->get();
        //common query
        $stocks = Stock::whereHas('batch', function ($query) use ($request) {
            $query->where('product_id', $request->product);
        });

        if ($request->batch != "All" && $request->filled('product')) {
            $stocks->where('batch_id', $request->batch);
        } elseif ($request->filled('start_date') && $request->filled('product') && $request->filled('batch')) (
            $stocks->whereBetween('created_at', [$start_date, $end_date])
        );

        $stocks = $stocks->get();
        $opening_bal = $stocks->value('total_units');


        return view('reports.moving', compact('stocks', 'products', 'opening_bal','start_date','end_date','selected_product','selected_batch'));
    }

    public function createPurchaseOrder()
    {
        $suppliers = user::where('user_role',User::SUPPLIER)->orderBy('name', 'asc')->get();
        return view('reports.purchase-order', compact('suppliers'));
    }

    public function purchaseOrder(Request $request)
    {
        $selected_supplier_id = $request->supplier;
        $selected_brand = $request->brand;

        $suppliers = User::where('user_role', User::SUPPLIER)->orderBy('name', 'asc')->get();

        $supplierss = User::where('id', $selected_supplier_id)->get();

        // Define the months to check
        $previousMonth = Carbon::now()->subMonth()->month;
        $previousBeforeMonth = Carbon::now()->subMonth(2)->month;
        $previousBefore2Month = Carbon::now()->subMonth(3)->month;

        // Initialize results array
        $results = [];

        // Query to get all relevant products
        $productsQuery = Product::where('supplier_id', $selected_supplier_id);
        if ($selected_brand != "All") {
            $productsQuery->where('brand_name', $selected_brand);
        }

        // Retrieve products
        $products = $productsQuery->with('batch.stock')->get();

        foreach ($products as $product) {
            $productName = $product->product_name;
            $productId = $product->id;

                $totalSums = 0;

                // Retrieve distinct batch IDs
                $distinctBatchIds = Stock::join('batch', 'stocks.batch_id', '=', 'batch.id')
                ->where('batch.product_id', $productId)
                ->groupBy('stocks.batch_id')
                ->pluck('stocks.batch_id');

                // Loop through each distinct batch ID
                foreach ($distinctBatchIds as $batchId) {
                    // Calculate the sum of total_units for the current batch ID
                    $totalSums += Stock::where('batch_id', $batchId)->latest()->value('total_units');
                }

            // Sales calculations for the last three months
            $previousMonthSales = Stock::join('batch', 'stocks.batch_id', '=', 'batch.id')
                ->where('batch.product_id', $productId)
                ->where(function ($query) {
                    $query->where('GRN_No/CRN_No/Stock_adjustment', 'like', '%INV%')
                        ->orWhere('GRN_No/CRN_No/Stock_adjustment', 'like', '%SINV%');
                })
                ->whereMonth('stocks.created_at', $previousMonth)
                ->sum('stocks.qty');

            $previousBeforeMonthSales = Stock::join('batch', 'stocks.batch_id', '=', 'batch.id')
                ->where('batch.product_id', $productId)
                ->where(function ($query) {
                    $query->where('GRN_No/CRN_No/Stock_adjustment', 'like', '%INV%')
                        ->orWhere('GRN_No/CRN_No/Stock_adjustment', 'like', '%SINV%');
                })
                ->whereMonth('stocks.created_at', $previousBeforeMonth)
                ->sum('stocks.qty');

            $previousBefore2MonthSales = Stock::join('batch', 'stocks.batch_id', '=', 'batch.id')
                ->where('batch.product_id', $productId)
                ->where(function ($query) {
                    $query->where('GRN_No/CRN_No/Stock_adjustment', 'like', '%INV%')
                        ->orWhere('GRN_No/CRN_No/Stock_adjustment', 'like', '%SINV%');
                })
                ->whereMonth('stocks.created_at', $previousBefore2Month)
                ->sum('stocks.qty');

            // Add data to results array
            $results[] = [
                'product_name' => $productName,
                'current_available_qty' => $totalSums,
                'previous_month_sales' => $previousMonthSales ?? 0,
                'previous_before_month_sales' => $previousBeforeMonthSales ?? 0,
                'previous_before_2_month_sales' => $previousBefore2MonthSales ?? 0,
            ];
        }

        return view('reports.purchase-order', compact('results', 'supplierss', 'suppliers', 'selected_brand'));
    }

    public function filterBrands(Request $request){
        $supplier = $request->Supplier;
        $brands = BrandName::where('supplier_id', $supplier)->orderBy('brand_name', 'asc')->get();
        return response()->json(
             $brands
        );
    }
}
