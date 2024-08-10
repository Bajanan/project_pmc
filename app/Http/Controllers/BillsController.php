<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Appointment;
use App\Models\Batch;
use App\Models\Clinic;
use App\Models\InvoiceService;
use App\Models\Service;
use App\Models\Stock;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BillsController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        Bill::whereDate('date', $yesterday)
            ->where('status', 'pending')
            ->update(['status' => Bill::CANCELLED]);

        Appointment::whereDate('date', '<=', $thirtyDaysAgo)
            ->where('status', 'Pending')
            ->update(['status' => Appointment::CANCELLED]);

        $invoices = Bill::select('invoice_no', 'id')
            ->whereDate('date', $today)
            ->where('status', 'pending')
            ->distinct()
            ->orderBy('id', 'desc')
            ->get();

        $invoice = Bill::whereDate('date', $today)
            ->where('status', 'paid')
            ->get();
        $total_sales = $invoice->sum('payable_amount');

        $countAppointmentsToday = Appointment::whereDate('date', $today)->count();

        $expiredStocks = DB::table('stocks')
            ->join('batch', 'stocks.batch_id', '=', 'batch.id')
            ->whereDate('batch.expire_date', '=', $today)
            ->select('stocks.*', 'stocks.total_units')
            ->whereRaw('stocks.id = (SELECT MAX(id) FROM stocks AS s WHERE s.batch_id = stocks.batch_id)')
            ->get();

        // Sum up the total_units of the latest stock record for each expired batch
        $totalExpiredUnits = 0;

        foreach ($expiredStocks as $stock) {
            // Add the total_units of the batch associated with the stock
            $totalExpiredUnits += $stock->total_units;
        }

        $pendingInvoicesCount = Bill::where('status', 'pending')->distinct()->count();

        return view('bills', [
            'invoices' => $invoices,
            'total_sales' => $total_sales,
            'countAppointmentsToday' => $countAppointmentsToday,
            'expiredProductsCount' => $totalExpiredUnits,
            'pendingInvoicesCount' => $pendingInvoicesCount,
        ]);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function getInvoiceDetails($invoice)
    {
        $bill = Bill::where('id', $invoice)->first();
        $patient_name = $bill->patient->name;
        $invoice_item = $bill->stock;
        $service_invoice = $bill->serviceInvoice;

        $patient_id = $bill->patient->id;
        $total_due_amount = Bill::where('patient_id', $patient_id)->sum('due_amount');

        return response()->json([
            'bill' => $bill,
            'patient_name' => $patient_name,
            'total_due' => $total_due_amount,
            'invoice_item' => $invoice_item,
            'service_invoice' => $service_invoice,
        ]);
    }

    public function cancelInvoice($invoiceNumber)
    {
        $bill = Bill::where('id', $invoiceNumber);
        if ($bill) {
            $bill->update([
                'status' => Bill::CANCELLED,
                'updated_at' => now()
            ]);
            $invoice_number = $bill->value('invoice_no');
            Stock::where('GRN_No/CRN_No/Stock_adjustment', $invoice_number)->delete();

            session()->flash('status', 'Invoice cancelled successfully');

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
    }

    public function checkout(Request $request)
     {

        //clinical details of the company
        $clinic = Clinic::first();
        $invoiceNumber = $request->input('invoiceno');
        $paidAmount = $request->input('paidAmount');
        $dueAmount = $request->input('dueAmount');
        $balanceAmount = $request->input('balanceAmount');
        $oldtotdueAmount = $request->input('totdueAmount');

        $totdueAmount = $oldtotdueAmount + $dueAmount;

        $bill = Bill::where('id', $invoiceNumber)->first();
        $SINV = "SINV";


    if ($bill) {
        // Update the status of the bill

        $bill->update([
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'status' => Bill::PAID,
            'updated_at' => now()
        ]);

        $path = 'uploads/1721746180.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $htmlContent = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <title>Invoice</title>
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
                           if (Str::contains($bill->invoice_no, $SINV)) {
                            $htmlContent .= '<table class="w-100 mt-1">
                            <tr class="header-border">
                            <th class="left"> Services</th><th class="right"> Amount</th></tr>';
                            $invoiceServices = InvoiceService::where('invoice_no', $bill->invoice_no)->get();
                            foreach($invoiceServices as $invoiceService){
                                $service = Service::where('id', $invoiceService->service_id)->first();
                            $htmlContent .= '<tr>
                           <td>'.$service->description.'</td>
                           <td class="right">'.number_format($invoiceService->total_amount, 2).'</td>
                           </tr>';
                           }
                           $htmlContent .= '</table><hr><table class="w-100">
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
                                        <p class="m-0"><label>Discount: </label></p>
                                    </td>
                                    <td class="right">
                                        <p class="m-0">'.number_format($bill->discount, 2).'%</p>
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
                        }else{

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
                    }

        return response($htmlContent, 200)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', 'inline');

    }else{
        return response()->json(['error' => 'Invoice not found'],404);
    }


    }


    public function printinvoice(Request $request)
     {

        $totdueAmount = $request->input('totdueAmount');

        $clinic = Clinic::first();
        $invoiceNumber = $request->input('invoiceno');

        $bill = Bill::where('id', $invoiceNumber)->first();
        $SINV = "SINV";


    if ($bill) {
        // Update the status of the bill

        $path = 'uploads/1721746180.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $htmlContent = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <title>Invoice</title>
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
                           if (Str::contains($bill->invoice_no, $SINV)) {
                            $htmlContent .= '<table class="w-100 mt-1">
                            <tr class="header-border">
                            <th class="left"> Services</th><th class="right"> Amount</th></tr>';
                            $invoiceServices = InvoiceService::where('invoice_no', $bill->invoice_no)->get();
                            foreach($invoiceServices as $invoiceService){
                                $service = Service::where('id', $invoiceService->service_id)->first();
                            $htmlContent .= '<tr>
                           <td>'.$service->description.'</td>
                           <td class="right">'.number_format($invoiceService->total_amount, 2).'</td>
                           </tr>';
                           }
                           $htmlContent .= '</table><hr><table class="w-100">
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
                                        <p class="m-0"><label>Discount: </label></p>
                                    </td>
                                    <td class="right">
                                        <p class="m-0">'.number_format($bill->discount, 2).'%</p>
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
                        }else{

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
                    }

        return response($htmlContent, 200)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', 'inline');

    }else{
        return response()->json(['error' => 'Invoice not found'],404);
    }


    }

    public function cancel_invoice(Request $request)
    {
        $invoiceId = $request->input('invoice_id');

        DB::beginTransaction();

        try {
            $invoice = Bill::find($invoiceId);

            if (!$invoice) {
                return redirect()->back()->with('status', 'Invoice not found.');
            }

            // Fetch all related stock records
            $invoiceId = $invoice->invoice_no;

            $SINV = "SINV";

            if (Str::contains($invoiceId, $SINV)) {
                // Update the invoice
                $invoice->status = 'cancelled';
                $invoice->paid_amount = 0;
                $invoice->payable_amount = 0;
                $invoice->due_amount = 0;
                $invoice->save();

                DB::commit();

                return redirect()->back()->with('success', 'Invoice canceled successfully.');
            }
            else {
            $stocks = Stock::where('GRN_No/CRN_No/Stock_adjustment', $invoiceId)->get();

            foreach ($stocks as $stock) {
                $batchId = $stock->batch_id;
                $qty = $stock->qty;

                // Get the latest record for the batch_id
                $latestStock = Stock::where('batch_id', $batchId)->orderBy('created_at', 'desc')->first();

                if ($latestStock) {
                    $newTotalUnits = $latestStock->total_units + $qty;

                    // Create a new stock record
                    Stock::create([
                        'GRN_No/CRN_No/Stock_adjustment' => "C".$invoiceId,
                        'batch_id' => $batchId,
                        'qty' => "-".$qty,
                        'total_units' => $newTotalUnits,
                        'staff_id' => Auth::user()->id
                    ]);
                }
            }

       // Update the invoice
       $invoice->status = 'cancelled';
       $invoice->paid_amount = 0;
       $invoice->payable_amount = 0;
       $invoice->due_amount = 0;
       $invoice->save();

       DB::commit();

       return redirect()->back()->with('status', 'Invoice canceled successfully.');

        }

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('status', 'An error occurred while canceling the invoice.');
    }

    }

    public function refundService(Request $request)
    {
    $invoiceId = $request->input('invoice_id');
    $serviceId = $request->input('service_id');
    $appointmentId = $request->input('appointment_id');


    $invoice = Bill::find($invoiceId);

    if ($invoice) {
        // Get the matching service invoice
        $serviceInvoice = $invoice->serviceInvoice()->where('service_id', $serviceId)->first();

        if ($serviceInvoice) {
            $totalAmount = $serviceInvoice->total_amount;

            // Update the invoice amounts
            $invoice->payable_amount -= $totalAmount;
            $invoice->paid_amount = max(0, $invoice->paid_amount - $totalAmount);
            $invoice->due_amount = max(0, $invoice->due_amount - $totalAmount);
            $invoice->save();

            // Optionally, you might want to delete or mark the service invoice as refunded
            $serviceInvoice->total_amount = 0;
            $serviceInvoice->save();

            $service = Service::find($serviceId);

            if ($service) {
                $serviceType = $service->service_type;

                // Update appointment table based on service type
                if ($appointmentId) {
                    $appointment = Appointment::find($appointmentId);

                    if ($appointment) {
                        if ($serviceType === 'Consultation') {
                            $appointment->doctor_payment = 0;
                        } elseif ($serviceType === 'Booking') {
                            $appointment->hospital_fee = 0;
                        }

                        $appointment->save();
                    }
                }
            }



            return redirect()->back()->with('status', 'Service refunded successfully.');

        } else {
            return redirect()->back()->with('status', 'Service not found in invoice.');
        }
    } else {
        return redirect()->back()->with('status', 'Invoice not found.');
    }
}

}
