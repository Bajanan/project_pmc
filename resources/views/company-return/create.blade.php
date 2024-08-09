@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Create Company Return</h3>
                </div>
				<!-- <div class="col-lg-4">
					<div class="search-container">
						<input class="form-search" placeholder="Type to search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div> -->
				<div class="col-lg-6 d-flex justify-content-end pe-5">
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
                <div class="form-card">
                <div class="row">
                    <div class="col-lg-12">
                        <form action="{{ route('company-return.store') }}" method="post">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="pb-3">
                                        <p class="form-label">CRN #</p>
                                        <input type="text" placeholder="CRN00218"
                                            class="form-control form-text border-green {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="CRN_No" value="{{ $CRN_No }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="pb-3">
                                        <p class="form-label">Date</p>
                                        <input type="date" placeholder="Enter patient name"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="date" value="{{ $current_date}}" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="pb-3">
                                        <p class="form-label">Supplier</p>
                                        <select
                                            class="form-control {{ $errors->has('supplier') ? 'is-invalid' : '' }} form-select"
                                            name="supplier" id="supplier" onchange="filterProducts();" required>
                                            <option value="" selected disabled hidden>Select supplier</option>
                                            @foreach ($suppliers as $supplier )
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="pb-3">
                                        <p class="form-label">Reason</p>
                                        <select
                                            class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }} form-select"
                                            name="reason" required>
                                            <option value="" selected disabled hidden>Select reason</option>
                                            <option value="Expired">Expired</option>
                                            <option value="Damaged">Damaged</option>
                                            <option value="Company Return Policy">Company Return Policy</option>
                                            <option value="Short Expiry">Short Expiry</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="pb-3">
                                        <p class="form-label">Notes</p>
                                        <input type="text" placeholder="Reference"
                                            class="form-control form-text {{ $errors->has('note') ? 'is-invalid' : '' }}"
                                            name="notes" value="" required>
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('note') }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="table2-card" id="editableTable">
                                    <table class="table-bordered table2">
                                    <thead>
                                    <tr>
                                        <th style='width: 25%;'>Product</th>
                                        <th style='width: 15%;'>Batch</th>
                                        <th>Expiry Date</th>
                                        <th>Qty<br /><span class="f-14">single units</span></th>
                                        <th>Unit Cost</th>
                                        <th>Unit Retail</th>
                                        <th>Total Cost</th>
                                        <th>Edit</th>
                                    </tr>
                                    </thead>
                                    <tbody id="TBody">
                                    <tr id="TRow" class="hide">
                                        <td><select name="product[]" data-placeholder="Select Product" class="product form-select filter-select2" onchange="filterBatch(this);">
                                        </select></td>
                                        <td><select name="batch[]" data-placeholder="Select Batch" class="batch form-select filter-select2" onchange="batchDetails(this);">
                                        <option value="">Select Batch</option>
                                        </select>
                                        </td>
                                        <td><input type="date" value="" class="expiry_date form-text2" name="expiry_date[]"  placeholder="" readonly/></td>
                                        <td><input type="text" pattern="[0-9]*" value="0" class="qty form-text2" name="qty[]"  placeholder="" oninput="totalCost(this)" onchange="overallCost(this)"/></td>
                                        <td><input value="0" class="unit_cost form-text2" name="unit_cost[]"  placeholder="" readonly/></td>
                                        <td><input value="0" class="unit_retail form-text2" name="unit_retail[]"  placeholder="" readonly/></td>
                                        <td><input value="0" class="total_cost form-text2" name="total_cost[]"  placeholder="" readonly/></td>
                                        <td>
                                        <span class="table-remove" onclick="BtnDel(this),overallCostminus(this)"><i class="fa fa-trash trash-icon"></i></span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                                <button type="button" class="table-add btn a2-bg" onclick="BtnAdd()"><i class="fa fa-plus me-2"></i>Add Row</button>
                            </div>



                                <div class="col-lg-12 text-end">
                                    <div class="col-md-2 mt-4 text-right"> <input type="text" name="overall_cost" class="form-control overall_cost" readonly></div>
                                    <div class="pb-3 pt-5">
                                        <button type="submit" class="main-button d1 a1-bg">Add Company Return</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            </div>

            <!-- Page content end -->
        </div>
    </section>


@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    function filterProducts(){

var supplier = $('#supplier').val();
// console.log(supplier);
var token = $('#token').val();

$.ajax({
        method: 'POST',
        url: '/crn/filter-products', // Replace with your route URL
        data: {'Supplier': supplier } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
        },
        success: function(response) {
           var data = "";
            data+= "<option value=''>Select Product</option>";
			$('select.product option').remove();
            response.products.forEach(product => {
                data += "<option value="+product.id+">"+product.product_name+"</option>";
            });
            $('select.product').append(data);

        },
        error: function(error) {
            // Handle error

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
        url: '/crn/filter-batches', // Replace with your route URL
        data: {'Product': product } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
        },
        success: function(response) {
            console.log(response);
            var data = "";
            data+= "<option value=''>Select Batch</option>";
            row.querySelector('select.batch').innerHTML = '';
            response.batches.forEach(batch => {

                data += "<option value="+batch.id+">"+batch.batch_name+"-"+batch.stock.total_units+"</option>";
            });
            var batchDropdown = row.querySelector('select.batch');
            batchDropdown.innerHTML = data; // Set innerHTML instead of append

        },
        error: function(error) {
            // Handle error

        }
    });

}

function batchDetails(v){

var row = v.parentNode.parentNode;
var batch = row.querySelector('.batch').value;
// console.log(batch);

var token = $('#token').val();

$.ajax({
        method: 'POST',
        url: '/crn/batch-details', // Replace with your route URL
        data: {'Batch': batch } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
        },
        success: function(response) {

            var costPriceFormatted = Number(response.batch_details.cost_price).toFixed(2);
            var retailPriceFormatted = Number(response.batch_details.retail_price).toFixed(2);

            row.querySelector('.expiry_date').value = response.batch_details.expire_date;
            row.querySelector('.unit_cost').value = costPriceFormatted;
            row.querySelector('.unit_retail').value = retailPriceFormatted;


        },
        error: function(error) {
            // Handle error

        }
    });

}

function totalCost(v){

var row = v.parentNode.parentNode;
var qtyInput = row.querySelector('.qty');
var qty = parseFloat(qtyInput.value);
var unit_cost = parseFloat(row.querySelector('.unit_cost').value);
var total_cost = qty * unit_cost;

var batchTotalUnits = parseFloat(row.querySelector('.batch').selectedOptions[0].text.split('-')[1] ?? 0);
    if (qty > batchTotalUnits) {
        qtyInput.value = batchTotalUnits;
        total_cost = batchTotalUnits * unit_cost;
    }
    else if(isNaN(qty)){
        qtyInput.value = '';
    }

    // console.log(total_cost);
    row.querySelector('.total_cost').value = total_cost.toFixed(2);

}

function overallCost(v){

    var total = 0;
    var totalCostElements = document.querySelectorAll('.total_cost');

    totalCostElements.forEach(function(element) {
        total += parseFloat(element.value);
    });

    document.querySelector('.overall_cost').value = total.toFixed(2);

}

function overallCostminus(v){

var row = v.parentNode.parentNode;
var total_cost = row.querySelector('.total_cost').value;
var previous_total =document.querySelector('.overall_cost').value;
document.querySelector('.overall_cost').value = (Number(previous_total) - Number(total_cost)).toFixed(2);

}


  //new table functioning

  function BtnAdd(){
            var v = $("#TRow").clone().appendTo("#TBody");
            $(v).removeClass("hide");
            v.find('.filter-select2').select2();
        }

        function BtnDel(v){
            $(v).parent().parent().remove();
        }

        $(document).ready(function(){
            BtnAdd();
        });

</script>
