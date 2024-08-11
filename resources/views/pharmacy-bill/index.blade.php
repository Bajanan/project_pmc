@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-3">
                    <h3 class="headingH3">Billing</h3>
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
                    <div class="col-lg-12">
                        <div class="form-card">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="checkout-form" action="{{ route('pharmacy-bill.store') }}" method="post">
                                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                        <div class="row">
                                        <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Invoice No</p>
                                                    <input type="hidden" name="invoice_no" value="{{ $invoice_no }}"><p class="headingH3" >{{ $invoice_no }}</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="pb-3">
                                                    <div class="d-flex align-items-center mt-3">
                                                        <select name="patient" data-placeholder="Select the patient" class="SelExample form-control form-select" id="patient" onchange="duePayments(),loadCreditLimit();" required>
                                                            <option value="">Select the patient</option>
                                                            @foreach($patients as $patient)
                                                            <option value="{{ $patient->id }}" {{ $patientId == $patient->id ? 'selected' : '' }}>{{ $patient->name }}-{{ $patient->contact_number }}-{{ $patient->address }}</option>
                                                            @endforeach
                                                        </select>
                                                        <a data-bs-toggle="modal" data-bs-target="#addModal"><div class="add-btn">+</div></a>
                                                        </div>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('doctor') }}</strong>
                                                    </span>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="table2-card" id="editableTable">
                                                <table class="table-bordered table2">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style='width: 35%;'>Item Description</th>
                                                    <th style='width: 15%;'>Batch </th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                    <th>%</th>
                                                    <th>Total</th>
                                                    <th>Payable</th>
                                                </tr>
                                                </thead>
                                                <tbody id="TBody">
                                                <tr id="TRow" class="hide">
                                                    <td>
                                                    <span class="table-remove" onclick="BtnDel(this),overallCostminus(this);"><i class="fa fa-trash trash-icon"></i></span>
                                                    </td>
                                                    <td>
                                                        <select name="product[]" data-placeholder="Select product" class="product form-control form-select" onchange="filterBatch(this);">
                                                        <option value="">select product</option>
                                                        @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-brand_name="{{ $product->brand_name }}" data-barcode="{{ $product->barcode }}" data-generic="{{ $product->generic_name }}">{{ $product->product_name }}</option>
                                                        @endforeach
                                                       </select>
                                                    </td>
                                                    <td><select name="batch[]" data-placeholder="Select batch" class="batch  form-control form-select" onchange="batchDetails(this)" >
                                                        <option value="">Select batch</option>
                                                       </select>
                                                       </td>
                                                    <td><input value="" class="retail_cost form-text2" name="retail_cost[]" placeholder=""  readonly/></td>
                                                    <td><input type="text" pattern="[0-9]*" value="1" name="qty[]"  class="qty form-text2" placeholder="" oninput="checkqty(this)" onchange="totalCost(this),overallCost(this)"/></td>
                                                    <td><input value="0" name="discount[]" id="discount" class="form-text2" onchange="payableAmount(this),overallCost(this)"/></td>
                                                    <td><input value="0" name="total_cost[]" class="total_cost form-text2" placeholder="" readonly/></td>
                                                    <td><input value="0" name="payable_price[]" class="payable_price form-text2" readonly/></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <button type="button" onclick="BtnAdd()" class="table-add btn a2-bg"><i class="fa fa-plus me-2"></i>Add Item</button>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-10 text-end">
                                                <div class="pt-3">
                                                <p>Total</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <div class="pt-3 pe-2">
                                                <p class="overall_cost">0.00</p>
                                                <input type="hidden" value="0.00" name="overall_cost" class="overall_cost_1">
                                                </div>
                                            </div>
                                        </div>

                                        <hr />

                                        <div class="row mt-3">
                                            <div class="col-lg-10 text-end">
                                                <div class="py-3">
                                                <p class="fw-bold">Payable Total</p>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-end">
                                                <div class="py-3 pe-2">
                                                <p class="payable_amount fw-bold">0.00</p>
                                                <input type="hidden" value="0.00" name="payable_amount" class="payable_amount_1">
                                                </div>
                                            </div>
                                        </div>

                                        <hr />

                                        <div class="row">
                                            <div class="col-lg-4 text-end">
                                                <div class="pt-3">
                                                <p class="body-small text-start pb-2">Amount Paid</p>
                                                    <input type="text" id="paid" oninput="amountPaid();" name="paid_total_amount"
                                                        class="amountPaid form-control form-text mt-0 {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                                        value="">

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
                    <div class="col-lg-12 mt-4">
                            <div class="form-card o-scroll">
                                <h3 class="headingH3 pb-3">Dues Bills</h3>
                                <table class="table-details" style="width:100%">
                                    <tbody>
                                       <div id="due_bills">
                                       </div>
                                    </tbody>
                                </table>
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

                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Phone</p>
                                        <input type="text" placeholder="Enter patient phone"
                                            class="form-control form-text border-green {{ $errors->has('contact_number') ? 'is-invalid' : '' }}"
                                            name="contact_number" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('contact_number') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Name</p>
                                        <input type="text" placeholder="Enter patient name"
                                            class="form-control form-text {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            name="name" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient DOB</p>
                                        <input type="date" placeholder=""
                                            class="form-control form-text {{ $errors->has('DOB') ? 'is-invalid' : '' }}"
                                            name="DOB" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('DOB') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Location</p>
                                        <input type="text" placeholder="Enter patient location"
                                            class="form-control form-text {{ $errors->has('address') ? 'is-invalid' : '' }}"
                                            name="address" value="" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('address') }}</strong>
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


@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    //new table functioning
        $(document).ready(function(){
            BtnAdd();

        });

        function BtnAdd(){
            var v = $("#TRow").clone().appendTo("#TBody");
            $(v).removeClass("hide");
            v.find('select.batch').select2();

            var newProductSelect = v.find('select.product');
            newProductSelect.select2({
                matcher: customMatcher
            });

            newProductSelect.focus();
        }

        function customMatcher(params, data) {

            if ($.trim(params.term) === '') {
                return data;
            }

            var searchTerm = params.term.toUpperCase();

            if (data && data.element) {
                var barcode = $(data.element).data('barcode');
                var generic = $(data.element).data('generic');
                var brand_name = $(data.element).data('brand_name');
                var product_name = $(data.element).text();

                if (barcode && barcode.toString().toUpperCase().indexOf(searchTerm) > -1) {
                    return data;
                }

                if (generic && generic.toString().toUpperCase().indexOf(searchTerm) > -1) {
                    return data;
                }

                if (brand_name && brand_name.toString().toUpperCase().indexOf(searchTerm) > -1) {
                    return data;
                }

                if (product_name && product_name.toUpperCase().indexOf(searchTerm) > -1) {
                    return data;
                }
            }
            return null;
        }

        function BtnDel(v){
            $(v).parent().parent().remove();
        }



function filterBatch(v){

var row = v.parentNode.parentNode;
var product = row.querySelector('.product').value;
var token = $('#token').val();

$.ajax({
        method: 'POST',
        url: '/pharmacy-billing/filter-batch',
        data: {'Product': product } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function(response) {
            console.log(response);
            var data = "";
            data+= "<option value=''>select batch</option>";
            row.querySelector('select.batch').innerHTML = '';
            response.batches.forEach(batch => {
                data += "<option value="+batch.id+">"+batch.batch_name+" - "+batch.stock.total_units+"</option>";
            });
            var batchDropdown = row.querySelector('select.batch');
            batchDropdown.innerHTML = data;

        },
        error: function(error) {
            

        }
    });

}

function batchDetails(v){

var row = v.parentNode.parentNode;
 var batch = row.querySelector('.batch').value;
var token = $('#token').val();

$.ajax({
        method: 'POST',
        url: '/pharmacy-billing/batch-details', 
        data: {'Batch': batch } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token 
        },
        success: function(response) {

            var formatedprice = parseFloat(response.price);

            row.querySelector('.retail_cost').value = formatedprice.toFixed(2);
            row.querySelector('.total_cost').value = formatedprice.toFixed(2);
            row.querySelector('.payable_price').value = formatedprice.toFixed(2);
            //payableAmount();
            overallCost();
        },
        error: function(error) {
            

        }
    });

}

    function checkqty(v){
        var row = v.parentNode.parentNode;
        var qty = row.querySelector('.qty').value;

        var batchTotalUnits = parseFloat(row.querySelector('.batch').selectedOptions[0].text.split('-')[1] ?? 0);
        if(batchTotalUnits < qty){
            row.querySelector('.qty').value = batchTotalUnits;
            qty = batchTotalUnits;
        }
    }

        function totalCost(v){

        var row = v.parentNode.parentNode;
        var qty = row.querySelector('.qty').value;
        var unit_cost = row.querySelector('.retail_cost').value;
        let total_cost = parseFloat(qty) * parseFloat(unit_cost);
        row.querySelector('.total_cost').value = total_cost.toFixed(2);
        row.querySelector('.payable_price').value = total_cost.toFixed(2);

        }

        function overallCost() {
            var overallCost = 0;
            var totalCostInputs = document.querySelectorAll('.total_cost');
            totalCostInputs.forEach(function(input) {
                overallCost += parseFloat(input.value) || 0;
            });
            document.querySelector('.overall_cost').innerText = overallCost.toFixed(2);
            document.querySelector('.overall_cost_1').value = overallCost.toFixed(2);

            var overallPayable = 0;
            var overallPayableInputs = document.querySelectorAll('.payable_price');
            overallPayableInputs.forEach(function(input) {
                overallPayable += parseFloat(input.value) || 0;
            });
            document.querySelector('.payable_amount').innerText = overallPayable.toFixed(2);
            document.querySelector('.payable_amount_1').value = overallPayable.toFixed(2);
        }

        function overallCostminus(v) {
            var row = v.parentNode.parentNode;

            var total_cost = row.querySelector('.total_cost').value;
            var previous_total =document.querySelector('.overall_cost_1').value;
            document.querySelector('.overall_cost_1').value = (Number(previous_total) - Number(total_cost)).toFixed(2);
            document.querySelector('.overall_cost').innerText = (Number(previous_total) - Number(total_cost)).toFixed(2);

            var total_payable = row.querySelector('.payable_price').value;
            var previous_payable =document.querySelector('.payable_amount_1').value;
            document.querySelector('.payable_amount_1').value = (Number(previous_payable) - Number(total_payable)).toFixed(2);
            document.querySelector('.payable_amount').innerText = (Number(previous_payable) - Number(total_payable)).toFixed(2);
        }

   function payableAmount(v){
    var row = v.parentNode.parentNode;
    var discount = row.querySelector('#discount').value;
    var total_cost = row.querySelector('.total_cost').value;
    var payable_amount = parseFloat(total_cost) - parseFloat(total_cost/100) * discount;
    row.querySelector('.payable_price').value = payable_amount.toFixed(2);
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
        $('.due_amount').html(Math.abs(balance).toFixed(2));
        $('.due_amount').val(Math.abs(balance).toFixed(2));
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
        url: '/pharmacy-billing/patient-dues', 
        data: {'Patient': patient } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token 
        },
        success: function(response) {
            //console.log(response);
            var data = '';
            if (response.due_bills && response.due_bills.length > 0) {
                response.due_bills.forEach(due => {
                    var dueData = "<table class='table-details' style='width:100%'><tbody><tr><td>"+due.invoice_no+"<br/><span class='f-14'>"+due.date+"</span></td>";
                    dueData += "<td class='text-end'>"+due.due_amount+"</td>";
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

   function loadCreditLimit() {
    var patient = $('#patient').val();
    var token = $('#token').val();

    $.ajax({
            method: 'POST',
            url: '/get-creditlimit', 
            data: {'Patient': patient } ,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': token 
            },
            success: function(response) {
                console.log(response);
                $('.credit_limit').text('CL: '+response.creditlimit.credit_limit);
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

    function clearFormData() {
        window.location.reload();
    }

</script>
