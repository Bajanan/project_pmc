@extends('layouts.report-main-header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Stock Moving Report</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					@include('layouts.profile')
                </div>
			</div>

 <!-- Page content -->

            <div class="px-4 pt-4">
        <form action="{{ route('reports.stocks-moving') }}" method="post">
            <div class="filter-sec row my-3">

            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                <div class="col-lg-4">
                <select class="form-select3 h-100" name="product" id="product" onchange="filterBatch();">
                    <option value="" selected disabled hidden>Select a Product</option>
                    @foreach ($products as $product )
                         <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
                </div>
                <div class="col-lg-4">
                <select class="form-select3 batch h-100" name="batch" >
                    <option value='All'>All Batches</option>
                </select>
                </div>
                <div class="col-lg-4">
                    <div id="reportrange"  class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                     <input type="hidden" id="startDateInput" name="start_date">
                    <input type="hidden" id="endDateInput" name="end_date">
                </div>

            </div>
               <button class="main-button d5-bg d1 rounded-0" type="submit">Search</button>
        </form>
            @if(isset($start_date))
                <div class="alert alert-success mt-2">From Date: {{ $start_date }} To {{ $end_date }} , Product:{{ $selected_product }} in batch: {{ $selected_batch }} </div>
                <textarea style="display: none;" id="dt-title">Product: {{ $selected_product }} | Batch: {{ $selected_batch }} | {{ $start_date }} To {{ $end_date }}</textarea>
            @endif
            @if(isset($opening_bal))
            <h5 class="headingH6 mt-3">Opening Balance - {{ $opening_bal }}</h5>
            @else
            <h5 class="headingH6 mt-3">Opening Balance - 0</h5>
            @endif

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
					<tr>
                        <th>Transcation Date</th>
                        <th>Transcation Number</th>
						<th>Transcation Type</th>
                        <th>In Qty</th>
                        <th>Out Qty</th>
                        <th>Balance</th>
					</tr>
				</thead>
				<tbody>
                @if(isset($stocks))
                @foreach($stocks as $stock)
					<tr>
                        <td>{{ $stock->created_at->format('Y-m-d') }}</td>
                        <td>{{ $stock->{'GRN_No/CRN_No/Stock_adjustment'} }}</td>
						<td>
                       @php
                         $substr = substr($stock->{'GRN_No/CRN_No/Stock_adjustment'}, 0, 2)
                        @endphp
                        @if($substr == "IN")
                         {{ "INVOICE" }}
                        @elseif($substr == "SI")
                         {{ "SERVICE INVOICE" }}
                        @elseif($substr == "GR")
                         {{ "GRN" }}
                        @elseif($substr == "CR")
                         {{ "Company Return" }}
                        @elseif($substr == "SA")
                         {{ "Stock Adjustment" }}
                        @elseif($substr == "CI")
                         {{ "Cancelled Invoice" }}
                        @endif
                        </td>
						<td>
                            @if($substr == "GR")
                                {{ $stock->qty }}
                            @elseif($substr == "SA" && $stock->qty > 0)
                                {{ $stock->qty }}
                            @elseif($substr == "CI")
                                {{ $stock->qty * (-1) }}
                            @else
                                {{ 0 }}
                            @endif
                        </td>
                        <td>
                            @if($substr != "GR" && $substr != "SA" && $substr != "CI")
                                {{ $stock->qty }}
                            @elseif($substr = "SA" && $stock->qty < 0 && $substr != "CI")
                                {{ $stock->qty * (-1) }}
                            @else
                            {{ 0 }}
                            @endif
                        </td>
                        <td>{{ $stock->total_units }}</td>
					</tr>
                @endforeach
                @endif
			</table>

        </div>

            <!-- Page content end -->
        </div>
    </section>

@endsection

@push('js')
<script>
    $(document).ready(function() {
            var table = $('#searchtbl').DataTable({
                "language": {
                "paginate": {
                "previous": "<",
                "next": ">"
                }
            },
            "bLengthChange" : true,
            columnDefs: [
                { orderable: false }
            ],
            layout: {
                    topEnd: {
                        buttons: ['excel'],
                    }
                },
            });

            table.button().add(0, {
                extend: 'pdfHtml5',
                title: function () {
                    return 'Stock Moving Report';
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Stock Moving Report',
                        fontSize: 18,
                        alignment: 'center',
                        margin: [0, 0, 0, 8]
                    });
                    doc.content.splice(1, 0, {
                        text: $('#dt-title').val(),
                        fontSize: 10,
                        alignment: 'center',
                        margin: [0, 0, 0, 16]
                    });
                    doc.content[2].table.widths = Array(table.columns().count()).fill('*');
                    var rowCount = doc.content[2].table.body.length;
                    var columnCount = doc.content[2].table.body[0].length;

                    for (var i = 0; i < rowCount; i++) {
                        for (var j = 0; j < columnCount; j++) {
                            doc.content[2].table.body[i][0].alignment = 'left';
                            doc.content[2].table.body[i][1].alignment = 'left';
                            doc.content[2].table.body[i][2].alignment = 'left';
                            doc.content[2].table.body[i][3].alignment = 'right';
                            doc.content[2].table.body[i][4].alignment = 'right';
                            doc.content[2].table.body[i][5].alignment = 'right';
                        }
                    }

                    doc.content.push({
                        text: 'Generated by PMC',
                        margin: [0, 20, 0, 0],
                        alignment: 'center',
                        fontSize: 10
                    });
                }
            });

            $('#search').keyup(function() {
                var table = $('#searchtbl').DataTable();
                table.search($(this).val()).draw();
            });
        });
</script>



<script>

function filterBatch(){
   var product = $('#product').val();
   var token = $('#token').val();

     $.ajax({
				method: 'POST',
				url: '/report/filter-batch', 
				data: {'Product':product},
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token 
				},
				success: function(response) {
					var data = "";
                    data+= "<option value='All'>All Batch</option>";
                    $('select.batch option').remove();
                    response.batches.forEach(batch=> {

                      data += "<option value="+batch.id+">"+batch.batch_name+"</option>";
                    });
                    $('select.batch').append(data);

				},
				error: function(error) {
					

				}
			});
}
</script>

@endpush
