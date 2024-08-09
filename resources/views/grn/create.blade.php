@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Create GRN</h3>
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
                        <form action="{{ route('grn.store') }}" method="post">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="pb-3">
                                        <p class="form-label">GRN #</p>
                                        <input type="text" placeholder="GRN00218"
                                            class="form-control form-text border-green {{ $errors->has('GRN_No') ? 'is-invalid' : '' }}"
                                            name="GRN_No" value="{{ $GRN_No }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="pb-3">
                                        <p class="form-label">Date</p>
                                        <input type="date" placeholder="Enter patient name"
                                            class="form-control form-text {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                            name="date" value="{{ $current_date }}" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('date') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="pb-3">
                                        <p class="form-label">Supplier</p>
                                        <select
                                            class="form-control {{ $errors->has('supplier') ? 'is-invalid' : '' }} form-select"
                                            name="supplier" id="supplier" onchange="filterProducts();" required>
                                            <option value="" selected disabled hidden>Select supplier</option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="pb-3">
                                        <p class="form-label">Invoice No</p>
                                        <input type="text" placeholder="Enter invoice no"
                                            class="form-control form-text border-green {{ $errors->has('invoice_no') ? 'is-invalid' : '' }}"
                                            name="invoice_no" value="" required>
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('invoice_no') }}</strong>
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
                                        <th class="f-14 d-none">Total Units</th>
                                        <th>Cost</th>
                                        <th>Retail</th>
                                        <th>Qty</th>
                                        <th>FOC</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="TBody">
                                    <tr id="TRow" class="hide">
                                        <td><select name="product[]" data-placeholder="Select Product" onchange="getProductId(this),filterBatch(this)" class="Gproduct form-select h35 filter-select2" >
                                        </select></td>
                                        <td class="d-flex align-items-center">
                                            <input type="text" class="form-text2 batch" name="new_batch[]" placeholder="" readonly>
                                            <select name="batch[]" data-placeholder="Select Batch" class="form-select h35 filter-select2 batch" onchange="batchDetails(this);">
                                            <option value="">Select Batch</option>
                                            </select>
                                            <a data-bs-toggle="modal" data-bs-target="#addModal"><div class="add-btn2">+</div></a>
                                        </td>
                                        <td><input type="date" class="expire_date form-text2" name="expire_date[]"/></td>

                                        <td><input value="0" class="unit_cost form-text2 GunitCost" name="unit_cost[]" oninput="totalCost(this)" onchange="overallCost(this)"/></td>
                                        <td><input value="0" class="unit_retail form-text2" name="unit_retail[]"/></td>
                                        <td><input type="text" pattern="[0-9]*" value="0" class="form-text2 Gqty" name="pack_qty[]" oninput="Calc(this)" onchange="totalCost(this);overallCost(this)"/>
                                            <input type="hidden" value="0" class="Gpackvalue" name="pack_value[]"/>
                                        </td>
                                        <td><input type="text" pattern="[0-9]*" value="0" class="form-text2 Gfoc" name="FOC[]" oninput="Calc(this)"/></td>
                                        <td class="d-none"><input value="0" class="form-text2 Gtotalunit"  name="total_units[]" readonly/></td>
                                        <td><input value="0" class="form-text2 Gtotal_cost" name="total_cost[]" readonly/></td>
                                        <td>
                                        <span class="table-remove" onclick="BtnDel(this),overallCostminus(this);"><i class="fa fa-trash trash-icon"></i></span>
                                        </td>
                                    </tr>

                                    </tbody>

                                </table>

                                <button type="button" class="table-add btn a2-bg" onclick="BtnAdd()"><i class="fa fa-plus me-2"></i>Add Row</button>

                            </div>



                                <div class="col-lg-12 text-end">
                                    <div class="col-md-2 mt-4 text-right"> <input type="text" name="overall_cost" class="form-control overall_cost" readonly></div>
                                    <div class="pb-3 pt-5">
                                        <button type="submit" class="main-button d1 a1-bg">Add GRN</button>
                                    </div>
                                </div>

                              <!--   <div class="pt-1">
                                    <ul>
                                        <li>To change existing batch price <a href="/price-change">click here</a><br />Important: Please do the price changes first and create GRN or data will be lost</li>
                                    </ul>
                                </div> -->

                            </div>

                        </form>
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
                    <h4 class="headingH3 text-center p-b-24">Add Batch</h4>
                    <form>
                       <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Hidden input field to store selected product ID -->
                                    <input type="hidden" class="selectedProductId" name="selected_product_id">
                                    <div class="pb-3">
                                        <p class="form-label">Product Name</p>
                                        <input type="text" placeholder="Product Name" class="form-control form-text productName" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Batch Name</p>
                                        <input type="text" placeholder="Enter Batch"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="batch" value="" id="batch_name" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        <button type="button" onclick="saveBatch();" class="main-button d1 a1-bg">Add Batch</button>
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

    function filterProducts(){

        var supplier = $('#supplier').val();
        // console.log(supplier);
        var token = $('#token').val();

        $.ajax({
				method: 'POST',
				url: '/grn/filter-products',
				data: {'Supplier': supplier } ,
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token
				},
				success: function(response) {
                    $('select.Gproduct option').remove();
                    $('select.Gproduct').append("<option value=''>Select Product</option>");
                    response.products.forEach(product => {
                    $.ajax({
                        url: '/grn/getPackSize/' + product.pack_size_id,
                        type: 'GET',
                        success: function(response) {
                            var packSize = response.packsize[0];

                            var optionText = product.product_name + " (" + packSize.pack_size + ")";
                            $('select.Gproduct').append("<option value='" + product.id + "'>" + optionText + "</option>");
                            },
                            error: function(xhr, status, error) {
                                console.error("Error fetching pack size:", error);
                            }
                    });
        });

        }

    });

}

        function getProductId(selectElement) {

            var selectedProductId = $(selectElement).val();
            var selectedProductName = $(selectElement).find('option:selected').text();

            $('.selectedProductId').val(selectedProductId);
            $('.productName').val(selectedProductName);
        }

        //new table functioning
        $(document).ready(function(){
            BtnAdd();
        });

        function BtnAdd(){
            var v = $("#TRow").clone().appendTo("#TBody");
            $(v).removeClass("hide");
            v.find('.filter-select2').select2();

            var batchInput = v.find(".batch");
            batchInput.hide();
        }

        function BtnDel(v){
            $(v).parent().parent().remove();
        }

        function Calc(v){
            var row = v.parentNode.parentNode;

            var Gqty = row.querySelector('.Gqty');
            var qty = parseFloat(Gqty.value);

            var foc = row.querySelector('.Gfoc').value;
            var product = row.querySelector('.Gproduct').value;

            if(isNaN(qty)) {
                Gqty.value = '';
            }

            var token = $('#token').val();

            let totalunit = parseFloat(qty) + parseFloat(foc);



            $.ajax({
				method: 'POST',
				url: '/grn/product-units', // Replace with your route URL
				data: {'Product': product } ,
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
				},
				success: function(response) {

                let unit_value =  parseFloat(response.unit_value);
                let total_units =  unit_value*totalunit;
                //let cost_units = unit_value*qty;
                row.querySelector('.Gtotalunit').value = total_units;
                //row.querySelector('.Gcostunits').value = cost_units;
                row.querySelector('.Gpackvalue').value = unit_value;

				},
				error: function(error) {
					// Handle error

				}
            });
        }

        function totalCost(v){

        var row = v.parentNode.parentNode;

        var tqty = row.querySelector('.Gqty').value;
        var unit_cost = row.querySelector('.GunitCost').value;
        let total_cost = parseFloat(tqty) * parseFloat(unit_cost);
        // console.log(total_cost);
        row.querySelector('.Gtotal_cost').value = total_cost.toFixed(2);

        }

        //end table functioning

        function overallCost(v){

        var total = 0;
        var totalCostElements = document.querySelectorAll('.Gtotal_cost');

        totalCostElements.forEach(function(element) {
            total += parseFloat(element.value);
        });

        document.querySelector('.overall_cost').value = total.toFixed(2);


        }

        //end table functioning

        function overallCostminus(v){

        var row = v.parentNode.parentNode;
        var total_cost = row.querySelector('.Gtotal_cost').value;
        var previous_total =document.querySelector('.overall_cost').value;
        document.querySelector('.overall_cost').value = (Number(previous_total) - Number(total_cost)).toFixed(2);

        }

function saveBatch(){
    var product_id =  $('.selectedProductId').val();
    var batch =  $('#batch_name').val();
    console.log(batch,product_id);
    var token = $('#token').val();
     $.ajax({
				method: 'POST',
				url: '/batch/add', // Replace with your route URL
				data: {'Batch': batch,'Product':product_id} ,
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
				},
				success: function(response) {
                    var row = $('#addModal').data('caller-row');
                    var batchSelect = row.find("select.batch");
                    var batchInput = row.find(".batch");

                    var batchName = $('#batch_name').val();

                    batchSelect.select2('destroy');
                    batchSelect.addClass("d-none");

                    batchInput.show();
                    batchInput.val(batchName);

                    $('#addModal').modal('hide');

				},
				error: function(error) {
					// Handle error

				}
            });

}

  function filterBatch(v){
    var row = v.parentNode.parentNode;

    var rows = $(v).closest('tr');
    $('#addModal').data('caller-row', rows);

    var product = row.querySelector('.Gproduct').value;
    // console.log(supplier);
    var token = $('#token').val();

    $.ajax({
        method: 'POST',
        url: '/grn/filter-batches', // Replace with your route URL
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
                data += "<option value="+batch.id+">"+batch.batch_name+"</option>";
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
    var batch = row.querySelector('select.batch').value;
    console.log(batch);

    var token = $('#token').val();

    $.ajax({
            method: 'POST',
            url: '/grn/batch-details', // Replace with your route URL
            data: {'Batch': batch } ,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
            },
            success: function(response) {

                var costPriceFormatted = Number(response.batch_details.total_cost_price).toFixed(2);
                var retailPriceFormatted = Number(response.batch_details.total_retail_price).toFixed(2);

                row.querySelector('.expire_date').value = response.batch_details.expire_date;
                row.querySelector('.unit_cost').value = costPriceFormatted;
                row.querySelector('.unit_retail').value = retailPriceFormatted;

            },
            error: function(error) {
                // Handle error

            }
        });

    }








</script>
