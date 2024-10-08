@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-3">
                    <h3 class="headingH3">Service Billing</h3>
                </div>
				<div class="col-lg-9 d-flex justify-content-end pe-5">
					<!-- <button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Staff</span></button> -->
                    @include('layouts.profile')
                </div>
			</div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                     {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Page content -->

            <div class="px-4 pt-4">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-card">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="checkout-form" action="{{ route('service-bill.store') }}" method="post">
                                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Invoice No</p>
                                                    <input type="hidden" name="invoice_no" value="{{ $invoice_no }}"><p class="headingH3" >{{ $invoice_no }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-7">
                                                <div class="pb-3">
                                                    <div class="d-flex align-items-center mt-3">
                                                        <select data-placeholder="Select the patient" name="patient" class="SelExample form-control filter-select2 form-select" id="patient" onchange="duePayments(),loadCreditLimit();" required>
                                                        <option value="">Select the patient</option>
                                                         @foreach($patients as $patient)
                                                         <option value="{{ $patient->id }}" {{ $patientId == $patient->id ? 'selected' : '' }}>{{ $patient->name }}-{{ $patient->contact_number }}-{{ $patient->address }}</option>
                                                         @endforeach
                                                        </select>
                                                        <a data-bs-toggle="modal" data-bs-target="#addModal"><div class="add-btn">+</div></a>
                                                        </div>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('company_name') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            @if( $appointmentId !== null)
                                            <div class="col-lg-5">
                                                <div class="pb-3">
                                                    <div class="d-flex align-items-center mt-1">
                                                        <input name="doctor" type="hidden" class="form-text" value="{{ $appointmentId }}" readonly/>
                                                        <input type="text" class="form-text" value="Appt. with - {{ $doctorName }}" readonly/>
                                                    </div>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('company_name') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="table2-card" id="editableTable">
                                                <table class="table-bordered table2">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style='width: 50%;'>Service</th>
                                                    <th>Qty</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody id="TBody">
                                                <tr id="TRow" class="hide">
                                                    <td>
                                                    <span class="table-remove" onclick="BtnDel(this),overallCostminus(this);"><i class="fa fa-trash trash-icon"></i></span>
                                                    </td>
                                                    <td>
                                                        <select name="service[]" data-placeholder="Select service" class="service  form-control form-select"  onchange="servicePrice(this);">
                                                        <option value="">Select service</option>
                                                         @foreach($service_types as $service_type)
                                                         <option value="{{ $service_type->id }}">{{ $service_type->description }}</option>
                                                         @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" pattern="[0-9]*" name="qty[]" class="qty form-text2" placeholder="" onchange="serviceTotCost(this),overallCost(this);"/></td>
                                                    <td><input type="hidden" class="unit_amount"/><input type="text" value="" class="amount form-text2 total_cost" placeholder="" name="service_price[]" readonly/></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <button type="button" onclick="BtnAdd()" class="table-add btn a2-bg"><i class="fa fa-plus me-2"></i>Add Service</button>
                                        </div>

                                        <button type="button" onclick="BtnShow()" class="btn a1-border mt-4" id="surgicalBtn">Surgical</button>

                                        <div class="table2-card surgicalTable d-none">
                                                <table class="table-bordered table2">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style='width: 30%;'>Item Description</th>
                                                    <th style='width: 20%;'>Batch</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody id="TBody2">
                                                <tr id="TRow2" class="hide">
                                                    <td>
                                                    <span class="table-remove" onclick="BtnDel2(this),overallCostminus(this);"><i class="fa fa-trash trash-icon"></i></span>
                                                    </td>
                                                    <td>
                                                        <select name="product[]" data-placeholder="Select the product" class="product form-control form-select" onchange="filterBatch(this);" >
                                                        <option value="">Select the product</option>
                                                         @foreach($products as $product)
                                                         <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                         @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="batch[]" data-placeholder="Select batch" class="batch  form-control form-select" onchange="batchDetails(this)" >
                                                            <option value="">Select batch</option>
                                                           </select>
                                                    </td>
                                                    <td><input value="0" class="retail_cost form-text2" placeholder="" readonly/></td>
                                                    <td><input type="text" pattern="[0-9]*" value="1" name="product_qty[]" class="product_qty form-text2" placeholder="" oninput="checkqty(this)" onchange="totalCost(this), overallCost(this);"/></td>
                                                    <td><input value="0" class="total_cost form-text2" placeholder="" readonly/></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <button type="button" onclick="BtnAdd2()" class="table-add btn a2-bg"><i class="fa fa-plus me-2"></i>Add Item</button>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-10 text-end">
                                                <div class="pt-3">
                                                <p >Total</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <div class="pt-3 pe-2">
                                                <p class="total">0.00</p>
                                                <input type="hidden" name="total_cost" id="total_val" value="0.00">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-4 text-end">
                                                <div class="pt-3">
                                                <p class="body-small text-start pb-2">Discount %</p>
                                                    <input type="text" placeholder="Discount" id="discount" oninput="payableAmount();"
                                                        class="form-control form-text mt-0 {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                                        name="company_name" value="0" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 text-end">
                                                <div class="pt-3">
                                                <p>Discount</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <div class="pt-3 pe-2">
                                                <p class="discount_amount">0.00</p>
                                                <input type="hidden" value="0" name="discount_amount" class="discount_amount">
                                                </div>
                                            </div>
                                        </div>

                                        <hr />

                                        <div class="row mt-3">
                                            <div class="col-lg-10 text-end">
                                                <div class="py-3">
                                                <p class="fw-bold">New Total</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <div class="py-3 pe-2">
                                                <p class="fw-bold payable_amount">0.00</p>
                                                <input type="hidden" name="payable_amount" class="payable_amount_1">
                                                </div>
                                            </div>
                                        </div>

                                        <hr />

                                        <div class="row">
                                            <div class="col-lg-4 text-end">
                                                <div class="pt-3">
                                                <p class="body-small text-start pb-2">Amount Paid</p>
                                                    <input type="text" id="paid" oninput="amountPaid();"
                                                        class="amountPaid form-control form-text mt-0 {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                                        name="paid_amount" value="">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-10 text-end">
                                                <div class="pt-3">
                                                <p>Balance</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <div class="pt-3 pe-2">
                                                <p id="balance">0.00</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-10 text-end">
                                                <div class="pt-3">
                                                <p >Due Amount</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <div class="pt-3 pe-2">
                                                <p class="due_amount" >0.00</p>
                                                <input type="hidden" name="due_amount" class="due_amount" value="0">
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" class="totdueAmount" name="totdueAmount" value="0"/>

                                        <input type="hidden" class="overallDue" name="overallDue"/>
                                        <input type="hidden" class="credit_limit" name="credit_limit"/>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="pt-5">
                                                    <button type="submit" class="main-button d1 a1-bg w-100" id="checkout-btn">Save</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                            <div class="form-card o-scroll">
                                <h3 class="headingH3">Dues Bills</h3>
                                <p class="credit_limit pb-3"></p>


                                        <div id="due_bills">
                                        </div>


                                <div class="d-flex justify-content-between pt-4"><h4 class="headingH4">Total Due:</h4><h4 class="headingH4" id="due_sum"></h4></div>

                            </div>
                    </div>
                </div>

            </div>
            <!-- Page content end -->
        </div>
    </section>

    <!-- add patient box -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="headingH3 text-center p-b-24">Add Patient</h4>
                    <form action="{{ route('patients.store') }}" method="post">
                        @csrf
                            <div class="row">
                                {{-- <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient ID</p>
                                        <input type="text" placeholder="Enter patient id"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="company_name" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div> --}}
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Phone</p>
                                        <input type="text" placeholder="Enter patient phone"
                                            class="form-control form-text border-green {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="contact_number" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Name</p>
                                        <input type="text" placeholder="Enter patient name"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="name" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient DOB</p>
                                        <input type="date" placeholder=""
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="DOB" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Location</p>
                                        <input type="text" placeholder="Enter patient location"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="address" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        <button type="submit" class="main-button d1 a1-bg">Add Patient</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- add Loan Pay box -->
    <div class="modal fade" id="repayModal" tabindex="-1" aria-labelledby="repayLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="headingH3 text-center p-b-24">Repay</h4>
                    <form action="{{ route('repayments.store') }}" method="post">
                        @csrf
                          @if ($errors->any())
                            <div class='alert alert-danger'>
                                <ul>
                                    @foreach ($errors->all() as $error )
                                    <li>{{$error}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Balance Amount</p>
                                        <input type="text"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="balance_amount" value="" id="balance_amount" readonly>
                                        <span class="invalid-feedback" role="alert">

                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Amount Paid</p>
                                        <input type="text" placeholder="Enter paid amount"
                                            class="form-control form-text border-green {{ $errors->has('paid_amount') ? 'is-invalid' : '' }}"
                                            name="paid_amount" id="paid_amount" value="0" required>
                                        <span class="invalid-feedback" role="alert">

                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        <input type="hidden" name="invoice_no" id="invoice_no">
                                        <button type="submit" class="main-button d1 a1-bg">Pay</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    //new table functioning

        $(document).ready(function(){
            BtnAdd();
            BtnAdd2();
        });

        function BtnShow(){
            $(".surgicalTable").toggleClass("d-none");
            var buttonText = $(".surgicalTable").hasClass("d-none") ? "Surgical" : "Close";
            $("#surgicalBtn").text(buttonText);
        }

        function BtnAdd(){
            var v = $("#TRow").clone().appendTo("#TBody");
            $(v).removeClass("hide");
             v.find('select').select2();
        }

        function BtnDel(v){
            $(v).parent().parent().remove();
        }

        function overallCost() {
            var overallCost = 0;
            var totalCostInputs = document.querySelectorAll('.total_cost');
            totalCostInputs.forEach(function(input) {
                overallCost += parseFloat(input.value) || 0;
            });
            document.querySelector('.total').innerText = overallCost.toFixed(2);
            document.getElementById('total_val').value = overallCost.toFixed(2);
            document.querySelector('.payable_amount').innerText = overallCost.toFixed(2);
            document.querySelector('.payable_amount_1').value = overallCost.toFixed(2);
        }

        function overallCostminus(v) {
            var row = v.parentNode.parentNode;

            var total_cost = row.querySelector('.total_cost').value;
            var previous_total = document.getElementById('total_val').value;
            document.getElementById('total_val').value = (Number(previous_total) - Number(total_cost)).toFixed(2);
            document.querySelector('.total').innerText = (Number(previous_total) - Number(total_cost)).toFixed(2);
            document.querySelector('.payable_amount').innerText = (Number(previous_total) - Number(total_cost)).toFixed(2);
            document.querySelector('.payable_amount_1').value = (Number(previous_total) - Number(total_cost)).toFixed(2);

        }


        function serviceTotCost(v){
        var row = v.parentNode.parentNode;
        var qty = row.querySelector('.qty').value;
        var unit_cost = row.querySelector('.unit_amount').value;
        let total_cost = parseFloat(qty) * parseFloat(unit_cost);
        row.querySelector('.amount').value = total_cost.toFixed(2);

        }

        function BtnAdd2(){
            var x = $("#TRow2").clone().appendTo("#TBody2");
            $(x).removeClass("hide");
             x.find('select').select2();
        }

        function BtnDel2(x){
            $(x).parent().parent().remove();
        }

        //end table functioning

        function servicePrice(v){

            var row = v.parentNode.parentNode;
            var service = row.querySelector('.service').value;

            var token = $('#token').val();
            // console.log(service);

            $.ajax({
        method: 'POST',
        url: '/service-billing/service-price', 
        data: {'Service':service } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token 
        },
        success: function(response) {
            // console.log(response.price);
            var formatedprice = parseFloat(response.price);

            row.querySelector('.unit_amount').value = formatedprice.toFixed(2);
              row.querySelector('.qty').value = "1";

            $('.total').html(formatedprice.toFixed(2));
            $('.payable_amount').html(formatedprice.toFixed(2));
            $('.payable_amount_1').val(formatedprice.toFixed(2));
            row.querySelector('.amount').value =formatedprice.toFixed(2);
            $('#total_val').val(formatedprice.toFixed(2));
             overallCost();




        },
        error: function(error) {
            

        }
    });


        }

        function filterBatch(v){

var row = v.parentNode.parentNode;
 var product = row.querySelector('.product').value;
// console.log(supplier);
var token = $('#token').val();

$.ajax({
        method: 'POST',
        url: '/service-billing/filter-batch', 
        data: {'Product': product } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token 
        },
        success: function(response) {
            var data = "";
            data+= "<option value=''>select batch</option>";
            row.querySelector('select.batch').innerHTML = '';
            response.batches.forEach(batch => {
                data += "<option value="+batch.id+">"+batch.batch_name+" - "+batch.stock.total_units+"</option>";
            });
            var batchDropdown = row.querySelector('select.batch');
            batchDropdown.innerHTML = data; // Set innerHTML instead of append

        },
        error: function(error) {
            

        }
    });

}

function batchDetails(v){

var row = v.parentNode.parentNode;
 var batch = row.querySelector('.batch').value;
 console.log(batch);
var token = $('#token').val();

$.ajax({
        method: 'POST',
        url: '/service-billing/batch-details', 
        data: {'Batch': batch } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token 
        },
        success: function(response) {

            row.querySelector('.retail_cost').value = response.price;
            row.querySelector('.total_cost').value = response.price;
            overallCost();
        },
        error: function(error) {
            

        }
    });

}

function checkqty(v){
    var row = v.parentNode.parentNode;
    var qty = row.querySelector('.product_qty').value;

    var batchTotalUnits = parseFloat(row.querySelector('.batch').selectedOptions[0].text.split('-')[1] ?? 0);
    if(batchTotalUnits < qty){
        row.querySelector('.product_qty').value = batchTotalUnits;
        qty = batchTotalUnits;
    }
}

function totalCost(v){

var row = v.parentNode.parentNode;
var qty = row.querySelector('.product_qty').value;
var unit_cost = row.querySelector('.retail_cost').value;
let total_cost = parseFloat(qty) * parseFloat(unit_cost);
row.querySelector('.total_cost').value = total_cost.toFixed(2);

}



function payableAmount(){

var discount = $('#discount').val();
var total_cost = $('.total').html();
var discount_amount = Number(total_cost)*(Number(discount)/100);
var payable_amount = Number(total_cost) - Number(total_cost)*(Number(discount)/100);
$('.discount_amount').html(discount_amount.toFixed(2));
$('.payable_amount').html(payable_amount.toFixed(2));
$('.discount_amount').val(discount_amount.toFixed(2));
$('.payable_amount_1').val(payable_amount.toFixed(2));

}

function amountPaid(){
    let amount_paid = $('#paid').val();
    let  payable_amount =  $('.payable_amount').html();
    let  balance = parseFloat(amount_paid) - parseFloat(payable_amount);
    $('#payment').html(amount_paid);
    if(balance >= 0){
        $('#balance').html( balance.toFixed(2));
        $('.due_amount').html('0.00');
        $('.due_amount').val('0.00');
    }else if(balance < 0){
        $('.due_amount').html((Math.abs(balance)).toFixed(2));
        $('.due_amount').val((Math.abs(balance)).toFixed(2));
        $('#balance').html('0.00');

    }

    let totalDue = parseFloat($('.totdueAmount').val());
    let newDue = parseFloat($('.due_amount').val());

    let overallDue = totalDue + newDue;
    $('.overallDue').val(overallDue);

    document.getElementById('checkout-btn').innerText = 'Checkout';
   }

   function duePayments(){

var patient = $('#patient').val();
var token = $('#token').val();

$.ajax({
     method: 'POST',
     url: '/service-billing/patient-dues', 
     data: {'Patient': patient } ,
     dataType: "json",
     headers: {
         'X-CSRF-TOKEN': token 
     },
     success: function(response) {
         console.log(response);
            var data = '';
            if (response.due_bills && response.due_bills.length > 0) {
                response.due_bills.forEach(due => {
                    var dueData = "<table class='table-details' style='width:100%'><tbody><tr><td>"+due.invoice_no+"<br/><span class='f-14'>"+due.date+"</span></td>";
                    dueData += "<td class='text-end'>"+due.due_amount.toFixed(2)+"</td>";
                    dueData += "</tr></tbody></table>";
                    data += dueData;
                });
                $('#due_bills').html(data);
                $('#due_sum').html(response.due_sum.toFixed(2));
                $('.totdueAmount').val(response.due_sum.toFixed(2));
            } else {
                data = "<div class='alert alert-secondary'>No Dues at the moment</div>";
                $('#due_bills').html(data);
            }

     },
     error: function(error) {
         

     }
 });

}


/* function loadAppointments() {
    var patient = $('#patient').val();
    var token = $('#token').val();

    console.log(token);

    $.ajax({
            method: 'POST',
            url: '/get-appointments', 
            data: {'Patient': patient } ,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': token 
            },
            success: function(response) {
                console.log(response);
                var data = "";
                data+= "<option value=''>select an appointment</option>";

                response.appointments.forEach(appointment => {
                    var selected = appointment.id === selectedAppointmentId ? 'selected' : '';
                    data += "<option value='" + appointment.id + "' " + selected + ">" + appointment.date + " - " + appointment.doctor.name + "</option>";
                });
                $('#doctor').html(data);

                //$('.credit_limit').text('CL: '+response.bill.patient.credit_limit);
                //$('.credit_limit').val(response.appointments.patient.credit_limit);

            },
            error: function(error) {
                

            }
        });

    }
 */

    function loadCreditLimit() {
    var patient = $('#patient').val();
    var token = $('#token').val();

    console.log(token);

    $.ajax({
            method: 'POST',
            url: '/get-creditlimit', 
            data: {'Patient': patient } ,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': token 
            },
            success: function(response) {
                let creditlimit = response.creditlimit.credit_limit ? parseFloat(response.creditlimit.credit_limit).toFixed(2) : 'Not Defined';
                console.log(response);
                $('.credit_limit').text('CL: '+ creditlimit);
                $('.credit_limit').val(response.creditlimit.credit_limit);

            },
            error: function(error) {
                

            }
        });

    }
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var repayModal = document.getElementById('repayModal');
        repayModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var invoiceNumber = button.getAttribute('data-invoice');
            var dueAmount = button.getAttribute('data-due');

            console.log(dueAmount);

            $('#balance_amount').val(dueAmount);
            $('#invoice_no').val(invoiceNumber);
        });

        var balanceAmountInput = document.getElementById('balance_amount');
        var paidAmountInput = document.getElementById('paid_amount');

        paidAmountInput.addEventListener('input', function () {
            var balanceAmount = parseFloat(balanceAmountInput.value);
            var paidAmount = parseFloat(paidAmountInput.value);

            if (paidAmount > balanceAmount) {
                paidAmountInput.value = balanceAmount.toFixed(2);
            }
        });

        var patientSelect = document.getElementById('patient');
        if (patientSelect.value) {
            duePayments();
            loadCreditLimit();
        }

    });

    $(document).ready(function() {
        $('#checkout-form').submit(function(event) {
            event.preventDefault();

            var amountPaid = parseFloat($('.amountPaid').val());
            var totalDue = parseFloat($('.overallDue').val());
            var creditLimit = parseFloat($('.credit_limit').val());

            if (amountPaid !== '' && totalDue > creditLimit) {
                alert('Credit limit exceeded. Cannot proceed with checkout.');
            } else {
                this.submit();
            }
        });
    });

</script>
