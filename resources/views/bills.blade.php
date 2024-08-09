@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3 text-uppercase"><?php echo date('l'); ?>&nbsp;&nbsp;<?php echo date('d.m.Y'); ?></h3>
                </div>
				<div class="col-lg-10 d-flex justify-content-end pe-5">
					@include('layouts.profile')
                </div>
			</div>

 <!-- Page content -->

            <div class="px-5 pt-4">
                <form id="checkout-form" action="{{ route('checkout') }}" method="POST">
                @csrf
                <div class="row">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <div class="col-lg-12">
                        @if (session('status'))
                            <div class="alert alert-success mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="mb-3 border-bottom">
                            <select data-placeholder="Select the invoice" name="invoiceno" class="SelExample form-select filter-select2 invoice" onchange="invoiceDetails();">
                                <option value="">Select an Invoice</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}">{{ $invoice->invoice_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="search-box2">



                            <div class="d-flex justify-content-between align-items-end mb-5">
                                <div>
                                    <p>Invoice no</p>
                                    <h3 class="headingH3 mb-3 invoice-no"></h3>

                                    <p>Invoice Time : <br /><span class="invoice-time"></span></p>
                                </div>
                                <div>
                                <p>Patient :<br /><b class="patient_name"></b></p>

                                </div>
                            </div>

                            <div class="invoice_content"></div>

                            <div class="text-end mb-4">
                                <p class="pb-2">Total : <span class="total_bill">0.00</span></p>
                                <p class="pb-3">Discount : <span class="total_discount">0.00</span></p>
                                <h4 class="headingH4">New Total : <span class="total_payable">0.00</span></h4>
                                <input type="hidden" value="0" class="total_amount"/>
                            </div>
                            <div class="row align-items-center d1-bg p-2 mb-2">
                                <div class="col-lg-8">
                                    <p class="text-end">Paid Amount</p>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" id="paid" oninput="amountPaid();" class="form-text2 text-end" name="paidAmount" value="0"/>
                                </div>
                            </div>
                            <div class="row align-items-center d1-bg p-2 mb-2">
                                <div class="col-lg-8">
                                    <p class="text-end">Balance Amount</p>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-text2 text-end balance" name="balanceAmount" value="0" readonly/>
                                </div>
                            </div>
                            <div class="row align-items-center d1-bg p-2 mb-2">
                                <div class="col-lg-8">
                                    <p class="text-end">Due Amount</p>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-text2 text-end due_amount" name="dueAmount" value="0" readonly/>
                                </div>
                            </div>
                            <div class="row align-items-center d1-bg p-2 mb-2">
                                <div class="col-lg-8">
                                    <p class="text-end text-danger">Total Due <span class="credit_limit"></span></p>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-text2 text-end tot_due_amount" name="totdueAmount" value="" readonly/>
                                </div>
                            </div>

                            <input type="hidden" class="overallDue" name="overallDue"/>
                            <input type="hidden" class="credit_limit" name="credit_limit"/>

                            <div class="d-flex justify-content-between mt-5">
                                <button type="button" class="cancel-btn main-button d5-bg d1" onclick="cancelInvoice()">Cancel Invoice</button>
                                <button type="submit" class="check-btn main-button a1-bg d1">Checkout</button>
                            </div>


                        </div>

                    </div>

                </div>
                </form>

            </div>

            <!-- Page content end -->
        </div>
    </section>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    window.onload = function() {
        amountPaid();
    }

    function formatDate(dateString) {
        var date = new Date(dateString);
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        var hours = ('0' + date.getHours()).slice(-2);
        var minutes = ('0' + date.getMinutes()).slice(-2);
        var meridiem = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        var formattedDate = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ' ' + meridiem;
        return formattedDate;
    }

    function invoiceDetails() {
        var invoice = $('.invoice').val();
        var token = $('#token').val();
        if (invoice !== '') {
            $.ajax({
                url: '/bills/invoices/' + invoice,
                method: 'GET', // Change method to GET
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {
                    // Populate invoice details in the section below

                    var formatedDate = formatDate(response.bill.created_at);

                    var total_invoice = parseFloat(response.bill.total_invoice);
                    var payable_amount = parseFloat(response.bill.payable_amount);
                    var total_due = parseFloat(response.total_due);
                    var discount = parseFloat(response.bill.discount ?? 0);

                    $('.total_bill').html(total_invoice.toFixed(2));
                    $('.total_discount').html(discount.toFixed(2));
                    $('.total_payable').html(payable_amount.toFixed(2));

                    $('.invoice-no').text(response.bill.invoice_no);
                    $('.total_amount').val(response.bill.payable_amount);
                    $('.tot_due_amount').val(total_due.toFixed(2));
                    $('.invoice-time').text(formatedDate);
                    $('.patient_name').html(response.patient_name);

                    $('.credit_limit').text('CL: '+response.bill.patient.credit_limit);
                    $('.credit_limit').val(response.bill.patient.credit_limit);

                    // Check if invoice number starts with 'SINV' (Service Invoice)
                    if (response.bill.invoice_no.startsWith('SINV')) {
                        // Show service details table
                        var serviceHtml = '<table class="table"><thead><th class="f-14">Service Description</th><th class="f-14 text-end">Amount</th></thead><tbody>';
                        for (var i = 0; i < response.service_invoice.length; i++) {

                            var serviceId = response.service_invoice[i].service_id;
                            $.ajax({
                            url: '/bills/services/' + serviceId,
                            method: 'GET',
                            dataType: 'json',
                            async: false, // Ensure synchronous execution to maintain order
                            success: function(serviceResponse) {
                                console.log(response);
                                serviceHtml += '<tr><td>' + serviceResponse.description + '</td><td class="text-end">' + response.service_invoice[i].total_amount + '</td></tr>';
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                            });

                        }
                        serviceHtml += '</tbody></table>';
                        $('.invoice_content').html(serviceHtml);
                    } else {
                        // Show product details table
                        var productHtml = '<table class="table"><thead><th class="f-14">Product</th><th class="f-14 text-end">Unit Price</th><th class="f-14 text-center">Quantity</th><th class="f-14 text-end">Total</th><th class="f-14 text-end">Payable Amount</th></thead><tbody>';
                        for (var i = 0; i < response.invoice_item.length; i++) {
                            var totalPrice = (response.invoice_item[i].unit_price * response.invoice_item[i].qty).toFixed(2);
                            var payableAmount = parseFloat(response.invoice_item[i].payable_amount).toFixed(2);
                            productHtml += '<tr><td>' + response.invoice_item[i].batch.product.product_name + '</td><td class="text-end">' + (response.invoice_item[i].unit_price * 1).toFixed(2) + '</td><td class="text-center">' + response.invoice_item[i].qty + '</td><td class="text-end">' + totalPrice + '</td><td class="text-end">' + payableAmount + '</td></tr>';
                        }
                        productHtml += '</tbody></table>';
                        $('.invoice_content').html(productHtml);
                    }


                    amountPaid();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    }


    function amountPaid(){
        let amount_paid = $('#paid').val();
        let  payable_amount =  $('.total_amount').val();
        let  balance = parseInt(amount_paid) - parseFloat(payable_amount);

        if(balance < 0){
            $('.due_amount').val(Math.abs(balance.toFixed(2)));
            $('.balance').val('0.00');
        }else if(balance > 0){
            $('.balance').val(Math.abs(balance.toFixed(2)));
            $('.due_amount').val('0.00');
        }
        else{
            $('.due_amount').val('0.00');
            $('.balance').val('0.00');
        }

        let totalDue = parseFloat($('.tot_due_amount').val());
        let newDue = parseFloat($('.due_amount').val());

        let overallDue = totalDue + newDue;
        $('.overallDue').val(overallDue);
   }

    function cancelInvoice() {
        var invoiceNumber = $('.invoice').val(); // Assuming you have a way to get the invoice number
        var token = $('#token').val();
        if (invoiceNumber !== '') {
            $.ajax({
                url: '/bills/cancel-invoice/' + invoiceNumber,
                method: 'POST',
                headers: {
					'X-CSRF-TOKEN': token
				},
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    }


    $(document).ready(function() {
        $('#checkout-form').submit(function(event) {
            event.preventDefault();

            var totalDue = parseFloat($('.overallDue').val());
            var creditLimit = parseFloat($('.credit_limit').val());

            if (totalDue > creditLimit) {
                alert('Credit limit exceeded. Cannot proceed with checkout.');
            } else {
                this.submit();
            }
        });
    });

</script>
