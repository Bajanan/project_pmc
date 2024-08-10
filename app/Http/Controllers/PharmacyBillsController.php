<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Bill;
use App\Models\Clinic;
use App\Models\Stock;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS2D;

class PharmacyBillsController extends Controller
{
    public function create(Request $request){

        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('n');
        $random_number = mt_rand(0001, 9999);;
        $invoice_no = "INV" . $year . $month . $random_number;

        $patients = User::where('user_role',User::PATIENT)->get();
        //$products = Product::with(['batch'])->get();
        //getting the products that have stocks only

        $products = [];
        $stocks = Stock::select('id', 'batch_id', 'created_at')
        ->whereIn('id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('stocks')
                ->groupBy('batch_id');
        })
        ->where('total_units', '>', 0)
        ->get();

        foreach($stocks as $stock){
            $product = Batch::where('id',$stock->batch_id)->first();

            if ($product) {
             $products[$product->Product->id] = $product->Product;
             }
         }
         $products = array_values($products);

         $patientId = $request->input('patient_id');

        return view('pharmacy-bill.index',compact('patients','products','invoice_no','patientId'));
    }

    public function filterBatches(Request $request)
    {
        $batches = Batch::where('product_id', $request->Product)->where('expire_date','>',Carbon::now()->format('Y-m-d'))->get();

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
        $price = Batch::where('id', $request->Batch)->value('retail_price');
        return response()->json(['price' => $price]);

    }

    public function store(Request $request){

    //   dd($request->all());
        $current_date = Carbon::now()->format('Y-m-d');

        if($request->overall_cost == '0.00'){
            return back()->with('error', "No product selected");
        }
        else{
            $filteredRows = [];
            // dd($request->all());
            // Iterate through the submitted rows
            foreach ($request->input('product') as $index => $product) {
                // Check if all fields in the row are not null
                if (!empty($request->input('batch')[$index])) {
                    // If all fields are not null, add the row to the filtered array
                    $filteredRows[] = [
                        'product' => $product,
                        'batch' => $request->input('batch')[$index],
                        'qty' => $request->input('qty')[$index],
                        'discount_rate' => $request->input('discount')[$index],
                        'payable_amount' => $request->input('payable_price')[$index],
                        'unit_price' => $request->input('retail_cost')[$index],
                    ];

                }
            }
        }


        DB::transaction(function () use ($request, $filteredRows,$current_date ) {

            $bill = Bill::create([

                'invoice_no' => $request->invoice_no,
                'patient_id' => $request->patient,
                'date' => $current_date,
                'total_invoice' => $request->overall_cost,
                //'discount' => $request->discount,
                'payable_amount' => $request->payable_amount,
                'paid_amount' => $request->paid_total_amount ?? 0,
                'due_amount' => $request->due_amount,
                //'status' => Bill::PENDING,
                'staff_id' => Auth::user()->id
            ]);

            if($request->paid_total_amount !== null){
                $bill->update([
                    'status' => Bill::PAID,
                    'paid_amount' => $request->paid_total_amount,
                ]);
            }else{
                $bill->update([
                    'status' => Bill::PENDING
                ]);
            }

            foreach ($filteredRows as $index2 => $data) {
                $total_units = Stock::where('batch_id',$filteredRows[$index2]['batch'])->latest()->value('total_units');
                Stock::create([

                    'GRN_No/CRN_No/Stock_adjustment' => $bill->invoice_no,
                    'batch_id' =>  $filteredRows[$index2]['batch'],
                    'qty' => $filteredRows[$index2]['qty'],
                    'total_units' => $total_units-$filteredRows[$index2]['qty'],
                    'staff_id' => Auth::user()->id,
                    'discount_rate' => $filteredRows[$index2]['discount_rate'],
                    'payable_amount' => $filteredRows[$index2]['payable_amount'],
                    'unit_price' =>  $filteredRows[$index2]['unit_price'],

                ]);
            }
            DB::commit();
        });

        if($request->paid_total_amount !== null){

            $checkoutRequest = new Request([
                'invoice_no' => $request->invoice_no,
                'paid_total_amount' => $request->paid_total_amount,
                'payable_amount' => $request->payable_amount,
                'dueAmount' => $request->due_amount,
                'totdueAmount' => $request->totdueAmount,
                // Add any other parameters required by the checkout function
            ]);
            return $this->checkout($checkoutRequest);
        }

        /* return back()->with('status', "Saved Successfully..!"); */

        $invoice_no = $request->invoice_no;
        /* $numeric_part = preg_replace('/\D/', '', $invoice_no); */

        $dns2d = new DNS2D();
        $qrCode = $dns2d->getBarcodeSVG($invoice_no, 'QRCODE');

        $htmlContent = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <title>'.$invoice_no.'</title>
                            <style>
                               body{
                                    width: 100%;
                                }
                               .mt-1{
                                    margin-top: 8px;
                               }

                                .b-small{
                                    font-size: 12px;
                                }
                                .w-100{
                                    width: 100%;
                                }
                                .invoice-header{
                                    padding: 20px;
                                    text-align: center;
                                }
                            </style>
                            <script>
                                window.onload = function(){
                                    window.print();
                                }
                            </script>
                        </head>
                        <body>
                            <div class="invoice-header">
                                <div class="qr">'. $qrCode .'</div>
                                <p>'. $invoice_no .'</p>
                            </div>
                        </body>
                        </html>';


                        return response($htmlContent, 200)
                        ->header('Content-Type', 'text/html')
                        ->header('Content-Disposition', 'inline');

    }


    public function billDueRecords(Request $request){

        $due_records = Bill::where('patient_id', $request->Patient)->where('due_amount','!=',0)->get();
        $due_sum =  $due_records->sum('due_amount');
        foreach($due_records as $record){
            $created_at = Carbon::parse($record->created_at)->format('Y-m-d h:i A');
            $record['create_at'] = $created_at;
        }
        return response()->json(['due_bills' =>$due_records, 'due_sum' =>$due_sum ]);

    }

    public function getCreditLimit(Request $request)
    {
        $creditlimit = User::where('id', $request->Patient)->first();
        return response()->json(['creditlimit' => $creditlimit]);
    }

    public function checkout(Request $checkoutRequest)
     {
    //clinical details of the company
    $clinic = Clinic::first();
    $invoiceNumber = $checkoutRequest->invoice_no;
    $paidAmount = $checkoutRequest->paid_total_amount;
    $balanceAmount = $checkoutRequest->paid_total_amount - $checkoutRequest->payable_amount;
    if($balanceAmount < 0){
        $balanceAmount = '0.00';
    }
    $dueAmount = $checkoutRequest->dueAmount;
    $oldtotdueAmount = $checkoutRequest->totdueAmount;

    $totdueAmount = $dueAmount + $oldtotdueAmount;

    $bill = Bill::where('invoice_no', $invoiceNumber)->first();

    if ($bill) {

        $path = 'uploads/1721746180.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $htmlContent = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <title>'.$bill->invoice_no.'</title>
                            <style>
                            body{
                                width: 100%%;
                                font-size: 14px;
                            }
                            #invoiceContent{
                                width: 100%;
                                height: 100%;
                            }
                            table{
                                border-collapse: collapse;
                            }
                            th{
                                font-weight: normal;
                            }
                            hr{
                                border: 0.5px solid #ccc;
                            }
                           .invoice-header{
                                text-align:center;
                           }
                           .mt-1{
                                margin-top: 8px;
                           }
                           .header-border th{
                                border-top: 1px dashed #ccc;
                                border-bottom: 1px dashed #ccc;
                           }
                           .headingh2{
                                font-size: 16px;
                                text-transform: uppercase;
                           }
                           .left{
                                text-align: left;
                           }
                           .right{
                                text-align: right;
                           }
                            .headingh4{
                                font-size: 16px;
                                text-align: center;
                            }
                            .b-small{
                                font-size: 12px;
                            }
                            .w-100{
                                width: 100%;
                            }
                            .m-0{
                                margin-bottom: 0px;
                                margin-top: 0px;
                            }
                            .logo{
                                width: 60px;
                                margin-bottom: 10px;
                            }

                        </style>
                        <script>
                            window.onload = function(){
                                window.print();
                            }
                        </script>
                        </head>
                        <body>
                        <div id="invoiceContent">
                            <div class="invoice-header">
                                <img src='.$logo.' class="logo"/>
                                <h2 class="headingh2 m-0">Ganesa<span class="b-small"> Meds</span></h2>
                                <h5 class="m-0 b-small">MODERN CLINIC</h5>
                                <p class="m-0">Specialist Consultation Centre<br>' . $clinic->clinic_address . '<br>' . $clinic->phone .' / '. $clinic->mobile . '</p>
                            </div>
                            <table class="w-100">
                                <tr>
                                    <td colspan="2">
                                        <p class="m-0">Invoice #<br>'.$bill->invoice_no.'</p>
                                    </td>
                                    <td class="right">
                                        <p class="date m-0">Date<br>'.$bill->created_at.'</p>
                                    </td>
                                </tr>
                            </table>
                            <p class="m-0"> <label>Customer: </label>'.$bill->patient->user_title.' '.$bill->patient->name.'</p>
                            </div>';


                            $htmlContent .= '<table class="w-100 mt-1">
                            <tr class="header-border">
                            <th class="left"> Item </th>
                            <th class="right"> Price </th>
                            <th class="right"> Amount </th></tr>';
                            $products = Stock::where('GRN_No/CRN_No/Stock_adjustment',$bill->invoice_no)->get();
                            foreach($products as $product){
                               $batch = Batch::where('id', $product->batch_id)->first();
                            $htmlContent .= '<tr>
                            <td class="left">'.$product->qty.' x '.$batch->Product->product_name. '<br><span class="b-small">Discount: '.$product->discount_rate.'%</span></td>
                            <td class="right">'.$product->unit_price.'</td>
                            <td class="right">'.$product->payable_amount.'</td></tr>';

                        }
                        $htmlContent .= '</table><hr>';
                        $htmlContent .= '<table class="w-100">
                                <tr>
                                    <td colspan="2">
                                        <p class="m-0"><label>Total: </label></p>
                                    </td>
                                    <td class="right">
                                        <p class="m-0">'.number_format($bill->total_invoice, 2).'</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="m-0"><b><label>Net Total: </label></b></p>
                                    </td>
                                    <td class="right">
                                        <p class="m-0"><b>'.number_format($bill->payable_amount, 2).'</b></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="m-0"><label>Paid: </label></p>
                                    </td>
                                    <td class="right">
                                        <p class="m-0">'.number_format($paidAmount, 2).'</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="m-0"><label>Balance: </label></p>
                                    </td>
                                    <td class="right">
                                        <p class="m-0">'.number_format($balanceAmount, 2).'</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="m-0"><label>Total Due: </label></p>
                                    </td>
                                    <td class="right">
                                        <p class="m-0">'.number_format($totdueAmount, 2).'</p>
                                    </td>
                                </tr>
                            </table>
                            <p class="headingh4">'.$clinic->bill_message.'<br><span class="b-small">Reg No: PHSRC/PGP/1239</span><br><span class="b-small">Powered By PMCStudio</span></p>
                            </div>
                        </body>
                        </html>';


                        return response($htmlContent, 200)
                        ->header('Content-Type', 'text/html')
                        ->header('Content-Disposition', 'inline');

    }else{
        return response()->json(['error' => 'Invoice not found'],404);
    }


    }

}
