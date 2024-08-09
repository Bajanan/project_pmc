@extends('layouts.report-main-header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Stock Balance Report</h3>
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

            <div class="filter-sec row align-items-center my-3">
            <form action="{{ route('reports.stocks') }}" method="post">
            @csrf
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <select class="form-select3 h-100" name="expiry_range">
                            <option value="All" selected>All</option>
                            <option value="3months">3 Months</option>
                            <option value="6months">6 Months</option>
                            <option value="expired">Expired</option>
                        </select>
                       <!--  <div class="d-flex align-items-center">
                            <input class="form-check-input form-check me-3" type="checkbox" value="1" name="expiry" id="expiry">Short Expiry
                        </div> -->
                    </div>
                    <div class="col-lg-8 d-flex align-items-center">
                    <select class="form-select3 h-100" name="supplier">
                        <option value="" selected disabled hidden>Select a supplier</option>
                        <option value="All" selected>All</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                    </select>
                    <button class="main-button d5-bg d1 rounded-0" type="submit">Filter</button>
                    </div>
                </div>
                </div>
                </form>

            </div>
            @if(isset( $selected_Supplier))
                <div class="alert alert-success">Supplier : {{ $selected_Supplier}} | {{ $short_expiry }}</div>
                <textarea style="display: none;" id="dt-title">Supplier : {{ $selected_Supplier}} | {{ $short_expiry }}</textarea>
                @endif

            <h5 class="headingH6">Total Stock : &nbsp;&nbsp; {{ $totalUnitsSum ?? 0 }}</h5>

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
					<tr>
                        <th>Brand</th>
                        <th>Generic Name</th>
						<th>Product</th>
                        <th>Batch</th>
                        <th class="text-end">Available Qty</th>
					</tr>
				</thead>
				<tbody>
                @if(isset($stocks))
                @php
                    $previousGenericName = '';
                @endphp
                @foreach ($stocks as $stock)
                    @if($stock->total_units > 0)
					<tr>
                        <td>{{ $stock->batch->Product->brand_name }}</td>
                        <td>{{ $stock->batch->Product->generic_name }}</td>
						<!-- @if ($stock->batch->Product->generic_name !== $previousGenericName)
                                <td>{{ $stock->batch->Product->generic_name }}</td>
                        @else
                                <td></td>
                        @endif -->

						<td>{{ $stock->batch->Product->product_name }}</td>
						<td>{{ $stock->batch->batch_name }} - {{ $stock->batch->expire_date }}</td>
                        <td class="text-end">{{ $stock->total_units }}</td>
					</tr>
                    @endif
                    @php
                        $previousGenericName = $stock->batch->Product->generic_name;
                    @endphp
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
                        buttons: ['excel', 'colvis'],
                    }
                },
            });

            table.button().add(0, {
                extend: 'pdfHtml5',
                title: function () {
                    return 'Stock Balance Report';
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Stock Balance Report',
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
                            doc.content[2].table.body[i][3].alignment = 'left';
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

            $('#search').keyup(function() {
                var table = $('#searchtbl').DataTable();
                table.search($(this).val()).draw();
            });
        });
</script>
@endpush
