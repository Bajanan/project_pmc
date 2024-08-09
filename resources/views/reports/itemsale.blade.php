@extends('layouts.report-main-header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-3">
                    <h3 class="headingH3">Items Sales Report</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-5 d-flex justify-content-end pe-5">
					@include('layouts.profile')
                </div>
			</div>

 <!-- Page content -->

            <div class="px-4 pt-4">

            <div class="filter-sec row align-items-start my-3">

                <div class="col-lg-10">
                   <form id="dateRangeForm" action="{{ route('reports.item-sales') }}" method="POST">
                @csrf <!-- Added an ID to the form for easier reference -->
                <div class="row">
                    <div class="col-lg-4">
                        <select class="form-select3 h-100" name="category">
                            <option value="" selected disabled hidden>Select a Category</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Surgical">Surgical</option>
                            <option value="Groceries">Grocery</option>
                        </select>
                    </div>
                    <div class="col-lg-8 d-flex align-items-center">
                        <div id="reportrange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                        <!-- Hidden inputs for start and end date -->
                        <input type="hidden" id="startDateInput" name="start_date">
                        <input type="hidden" id="endDateInput" name="end_date">
                        <button class="main-button d5-bg d1 rounded-0" type="submit">Search</button>
                    </div>
                    </div>
                </div>

                 </form>
            </div>

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
                @if(isset($start_date) && $category==null)
                <div class="alert alert-success">From Date: {{ $start_date }} To {{ $end_date }} </div>
                <textarea style="display: none;" id="dt-title">{{ $start_date }} To {{ $end_date }}</textarea>

                @elseif(isset($start_date) && $category!==null)
                 <div class="alert alert-success">From Date: {{ $start_date }} To {{ $end_date }}  AND Category: {{ $category }}</div>
                 <textarea style="display: none;" id="dt-title">{{ $start_date }} To {{ $end_date }} | Category: {{ $category }}</textarea>
                 @endif
					<tr>
                        <th>Date</th>
						<th>Invoice #</th>
                        <th>Product</th>
                        <th>Qty</th>
						<th class="text-end">Total</th>
					</tr>
				</thead>
				<tbody>

                @php
                    $totalSalesAmount = 0; // Initialize the total payable amount variable
                @endphp

                @if(isset($item_sales))
                @foreach ($item_sales as $item_sale)

					<tr>
						<td>{{ $item_sale->created_at->format('Y-m-d') }}</td>
						<td>{{ $item_sale->{'GRN_No/CRN_No/Stock_adjustment'} }}</td>
						<td>{{ $item_sale->batch->Product->product_name }}</td>
                        <td>{{ $item_sale->qty }}</td>
                        <td class="text-end">{{ number_format($item_sale->qty*$item_sale->batch->retail_price, 2) }}</td>
					</tr>
                     @php
                        $totalSalesAmount += $item_sale->qty*$item_sale->batch->retail_price ; // Add the payable amount of the current invoice to the total
                    @endphp
                @endforeach
                @endif

			</table>

            <h5 id="totalSales" class="headingH6 mt-4">Total Sales : {{ number_format($totalSalesAmount??0, 2) }}</h5>

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
                    return 'Item Sales Report';
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Item Sales Report',
                        fontSize: 18,
                        alignment: 'center',
                        margin: [0, 0, 0, 8]
                    });

                    // Adding custom header
                    doc.content.splice(1, 0, {
                        text: $('#dt-title').val(),
                        fontSize: 10,
                        alignment: 'center',
                        margin: [0, 0, 0, 16]
                    });
                    // Set table width to A4 page width
                    doc.content[2].table.widths = Array(table.columns().count()).fill('*');

                    var rowCount = doc.content[2].table.body.length;
                    var columnCount = doc.content[2].table.body[0].length; // Assuming all rows have the same length

                    for (var i = 0; i < rowCount; i++) {
                        for (var j = 0; j < columnCount; j++) {
                            doc.content[2].table.body[i][0].alignment = 'left';
                            doc.content[2].table.body[i][1].alignment = 'left';
                            doc.content[2].table.body[i][2].alignment = 'left';
                            doc.content[2].table.body[i][3].alignment = 'right';
                            doc.content[2].table.body[i][4].alignment = 'right';
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

            table.on('draw', function () {
                var totalSales = 0;
                table.rows({search: 'applied'}).data().each(function (value, index) {
                    totalSales += parseFloat(value[4].replace(/[^0-9.-]+/g,""));
                });
                $('#totalSales').text('Total Sales : ' + totalSales.toFixed(2));
            });

            $('#search').keyup(function() {
                var table = $('#searchtbl').DataTable();
                table.search($(this).val()).draw();
            });
        });
</script>
@endpush
