@extends('layouts.report-header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3 text-uppercase">Invoice Details</h3>
                </div>
				<div class="col-lg-10 d-flex justify-content-end pe-5">
					@include('layouts.profile')
                </div>
			</div>

 <!-- Page content -->
 <div class="px-4 pt-4">



                <div class="row">

                    <div class="col-lg-7">

                        @if (session('status'))
                            <div class="alert alert-warning" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form id="checkout-form" action="{{ route('printinvoice') }}" method="POST">
                        @csrf
                        <div class="search-box2">

                            <div class="d-flex justify-content-between align-items-end mb-5">
                                <div>
                                    <p>Invoice no</p>
                                    <h3 class="headingH3 mb-3 invoice-no"></h3>
                                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="invoiceno" class="inv_id" value="{{ $invoice->id }}"/>

                                    <p>Invoice Time : <br /><span class="">{{ $invoice->created_at->format('d-m-Y H:i A') }}</span></p>
                                </div>
                                <div>
                                <button class="main-button d1 a1-bg mb-3" type="submit"><i class="fa fa-print me-2"></i>Print</button>
                                <p>Patient :<br /><b class="patient_name"></b></p>
                                </div>
                            </div>

                            <div class="invoice_content"></div>

                            <div class="text-end mb-4">
                                <p class="pb-2">Total : <span class="total_bill"></span></p>
                                <p class="pb-3">Discount : <span class="total_discount" ></span></p>
                                <h4 class="headingH4">Net Total : <span class="total_payable"></span></h4>
                                <p class="pb-3">Paid Amount : <span class="paid_amount"></span></h4>
                                <h4 class="headingH6 text-danger">Total Due : <span class="total_due"></span></h4>
                                <input type="hidden" value="0" class="tot_due_amount" name="totdueAmount"/>
                            </div>
                        </div>
                        </form>
                    </div>
                    
                </div>
            <!-- Page content end -->
        </div>
    </section>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        invoiceDetails();
    });

    function invoiceDetails() {
        var id = $('.inv_id').val();
        var token = $('#token').val();
        if (id !== '') {
            $.ajax({
                url: '/bills/invoices/' + id,
                method: 'GET', // Change method to GET
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function(response) {

                    console.log(response);
                    // Populate invoice details in the section below
                    var total_invoice = parseFloat(response.bill.total_invoice);
                    var payable_amount = parseFloat(response.bill.payable_amount);
                    var bill_due = parseFloat(response.bill.due_amount);
                    var due_amount = parseFloat(response.total_due);
                    var discount = parseFloat(response.bill.discount ?? 0);

                    var paid_amount = payable_amount - bill_due;

                    var appointment_id = response.doctor_id;

                    $('.total_bill').html(total_invoice.toFixed(2));
                    $('.total_discount').html(discount.toFixed(2));
                    $('.total_payable').html(payable_amount.toFixed(2));
                    $('.paid_amount').html(paid_amount.toFixed(2));
                    $('.total_due').html(due_amount.toFixed(2));

                    $('.invoice-no').text(response.bill.invoice_no);
                    $('.invoice-time').text(response.bill.created_at);
                    $('.patient_name').html(response.patient_name);
                    $('.tot_due_amount').val(due_amount.toFixed(2));

                    // Check if invoice number starts with 'SINV' (Service Invoice)
                    if (response.bill.invoice_no.startsWith('SINV')) {

                        $('#service_section').show();

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

                                serviceHtml += '<tr><td>' + serviceResponse.description + '</td><td class="text-end">' + response.service_invoice[i].total_amount + '</td></tr>';
                                populateServiceList(serviceResponse);
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                            });

                        }
                        serviceHtml += '</tbody></table>';
                        $('.invoice_content').html(serviceHtml);
                    } else {

                        $('#service_list').hide();

                        var productHtml = '<table class="table"><thead><th class="f-14">Product</th><th class="f-14 text-end">Unit Price</th><th class="f-14 text-center">Quantity</th><th class="f-14 text-end">Total</th><th class="f-14 text-end">Payable Amount</th></thead><tbody>';
                        for (var i = 0; i < response.invoice_item.length; i++) {
                            var totalPrice = (response.invoice_item[i].unit_price * response.invoice_item[i].qty).toFixed(2);
                            var payableAmount = parseFloat(response.invoice_item[i].payable_amount).toFixed(2);
                            productHtml += '<tr><td>' + response.invoice_item[i].batch.product.product_name + '</td><td class="text-end">' + (response.invoice_item[i].unit_price * 1).toFixed(2) + '</td><td class="text-center">' + response.invoice_item[i].qty + '</td><td class="text-end">' + totalPrice + '</td><td class="text-end">' + payableAmount + '</td></tr>';
                        }
                        productHtml += '</tbody></table>';
                        $('.invoice_content').html(productHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    }

    function populateServiceList(service) {
    var serviceList = $('#service_list');
        var option = $('<option>', {
            value: service.id,
            text: service.description
        });
        serviceList.append(option);
    }


</script>
