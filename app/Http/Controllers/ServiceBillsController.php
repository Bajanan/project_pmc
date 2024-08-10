<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Bill;
use App\Models\Clinic;
use App\Models\Appointment;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\InvoiceService;
use App\Models\Product;
use App\Models\Service;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS2D;

class ServiceBillsController extends Controller
{
    public function create(Request $request){
        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('n');
        $random_number = mt_rand(0001, 9999);
        $invoice_no = "SINV" . $year . $month . $random_number;

        $patients = User::where('user_role', User::PATIENT)->get();
        $doctors = User::where('user_role', User::DOCTOR)->get();

        $service_types = Service::all();
        $products = Product::where('category', 'Surgical')->get();

        $patientId = $request->input('patient_id');
        $appointmentId = $request->input('appointment_id');
        $doctorName = $request->input('doctor_name');

        return view('service-bill.index',compact('patients','doctors','service_types','products','invoice_no','patientId','appointmentId','doctorName'));
    }

    public function store(Request $request){


        //dd($request->all());

        $current_date = Carbon::now()->format('Y-m-d');

        if($request->total_cost == '0.00'){
            return back()->with('error', "No service selected");
        }
        else{
            $filteredRows = [];
            $services = [];

            foreach ($request->input('product') as $index => $product) {

                if (!empty($request->input('batch')[$index])) {

                    $filteredRows[] = [
                        'product' => $product,
                        'batch' => $request->input('batch')[$index],
                        'qty' => $request->input('product_qty')[$index],

                    ];

                }
            }
        }

        foreach($request->input('service') as $index => $service){

            if (!empty($request->input('qty')[$index])) {

                $services[] = [
                    'service' => $service,
                    'service_qty' => $request->input('qty')[$index],
                    'service_amount' => $request->input('service_price')[$index],
                ];

            }

        }

        DB::transaction(function () use ($request, $filteredRows,$current_date,$services ) {

            $bill = Bill::create([

                'invoice_no' => $request->invoice_no,
                'patient_id' => $request->patient,
                'doctor_id' => $request->doctor,
                'date' => $current_date,
                'total_invoice' => $request->total_cost,
                'discount' => $request->discount_amount,
                'payable_amount' => $request->payable_amount,
                'paid_amount' => $request->paid_amount ?? 0,
                'due_amount' => $request->due_amount,
                'staff_id' => Auth::user()->id,
                // 'status' => Bill::PENDING
            ]);

            if($request->paid_amount !== null){
                $bill->update([
                    'status' => Bill::PAID,
                    'paid_amount' => $request->paid_amount,
                ]);
            }else{
                $bill->update([
                    'status' => Bill::PENDING
                ]);
            }

          if($filteredRows){
            foreach ($filteredRows as $index2 => $data) {

               $total_units = Stock::where('batch_id',$filteredRows[$index2]['batch'])->latest()->value('total_units');

                Stock::create([

                    'GRN_No/CRN_No/Stock_adjustment' => $bill->invoice_no,
                    'batch_id' =>  $filteredRows[$index2]['batch'],
                    'qty' => $filteredRows[$index2]['qty'],
                    'total_units' => $total_units-$filteredRows[$index2]['qty'],
                    'staff_id' => Auth::user()->id
                ]);
            }

        }

        $service_price_doctor = 0;
        $hospital_fee = 0;

        foreach ($services as $index3 => $serviceData) {

           $serviceId =  $serviceData['service'];
           $qty = $serviceData['service_qty'];
           $service_amount = $serviceData['service_amount'];

           $service = Service::find($serviceId);

           if ($service) {
            $serviceType = $service->service_type;

            // Set the price based on the service type
            if ($serviceType === 'Consultation') {
                $service_price_doctor +=  $qty * ($service->unit_price);
            } elseif ($serviceType === 'Booking') {
                $hospital_fee += $qty * ($service->unit_price);
            }

           //dd($service_amount);

           $bill->service()->attach($serviceId, [
                'qty' => $qty,
                'invoice_no' => $bill->invoice_no,
                'total_amount' => $service_amount
            ]);
        }

        }

        if ($request->has('doctor')) {
            $appointmentId = $request->doctor; // Assuming doctor value contains appointment ID
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $appointment->update([
                    'status' => Appointment::COMPLETED,
                    'doctor_payment' => $service_price_doctor ?? 0,
                    'hospital_fee' => $hospital_fee  ?? 0,
                ]);
            }
        }

        DB::commit();

        });

        if($request->paid_amount !== null){

            $checkoutRequest = new Request([
                'invoice_no' => $request->invoice_no,
                'paid_total_amount' => $request->paid_amount,
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


    public function servicePrice(Request $request){

        $service_price = Service::where('id',$request->Service)->value('unit_price');

        return response()->json(["price" => $service_price ]);
    }

    public function filterBatches(Request $request){

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

    public function billDueRecords(Request $request){

        $due_records = Bill::where('patient_id', $request->Patient)->where('due_amount','!=',0)->get();
        $due_sum =  $due_records->sum('due_amount');
        foreach($due_records as $record){
            $created_at = Carbon::parse($record->created_at)->format('Y-m-d h:i A');
            $record['create_at'] = $created_at;
        }
        return response()->json(['due_bills' =>$due_records, 'due_sum' =>$due_sum ]);

    }

    public function getAppointments(Request $request)
    {
        $appointments = Appointment::where('patient_id', $request->Patient)->where('status','Pending')->get();
        return response()->json(['appointments' => $appointments]);
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
                                        <p class="m-0">'.number_format($bill->discount, 2).'</p>
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
