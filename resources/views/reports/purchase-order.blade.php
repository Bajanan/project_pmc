@extends('layouts.report-main-header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Purchase Order Report</h3>
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
 <form action="{{ route('reports.purchase-order') }}" method="post">

            <div class="filter-sec row my-3">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                <div class="col-lg-3">
                <select class="form-select3 h-100" name="supplier" id="supplier" onchange="filterBrands()">
                    <option value="" selected disabled hidden>Select a Supplier</option>
                    @foreach($suppliers as $supplier)
                     <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                </div>
                <div class="col-lg-3">
                <select class="form-select3 h-100" name="brand" id="brand">
                    <option value="All">All Brands</option>
                </select>
                </div>
                <div class="col-md-3">
                 <button class="main-button d5-bg d1 rounded-0" type="submit">Search</button>
                </div>
            </div>
  </form>
            @if(isset($supplierss))
                <div class="alert alert-success">Supplier: {{ $supplierss->value('name')}}, Brand: {{ $selected_brand }} </div>
                <textarea style="display: none;" id="dt-title">Supplier: {{ $supplierss->value('name') }} | Brand: {{ $selected_brand }}</textarea>
            @endif

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
					<tr>
                        <th>Product</th>
                        <th class="text-end"><?php echo date("M",strtotime("-3 Months")); ?> Sales</th>
						<th class="text-end"><?php echo date("M",strtotime("-2 Months")); ?> Sales</th>
                        <th class="text-end"><?php echo date("M",strtotime("-1 Months")); ?> Sales</th>
                        <th class="text-end">Average Sales</th>
                        <th class="text-end">Available Qty</th>
					</tr>
				</thead>
				<tbody>
            @if(isset($results))
            @foreach($results as $result)
					<tr>
						<td>{{ $result['product_name'] }}</td>
                        <td class="text-end">{{ $result['previous_before_2_month_sales'] }}</td>
						<td class="text-end">{{ $result['previous_before_month_sales'] }}</td>
						<td class="text-end">{{ $result['previous_month_sales'] }}</td>
                        @php
                        $average = ($result['previous_before_2_month_sales'] +  $result['previous_before_month_sales'] + $result['previous_month_sales'])/3
                        @endphp
                        <td class="text-end">{{ number_format($average) }}</td>
                        <td class="text-end">{{ $result['current_available_qty'] }}</td>
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
                    return 'Purchase Order Report';
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Purchase Order Report',
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
                    doc.content[2].table.widths = Array(table.columns().count()).fill('*');
                    var rowCount = doc.content[2].table.body.length;
                    var columnCount = doc.content[2].table.body[0].length; // Assuming all rows have the same length

                    for (var i = 0; i < rowCount; i++) {
                        for (var j = 0; j < columnCount; j++) {
                            doc.content[2].table.body[i][0].alignment = 'left';
                            doc.content[2].table.body[i][1].alignment = 'right';
                            doc.content[2].table.body[i][2].alignment = 'right';
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
function filterBrands(){

   var supplier =  $('#supplier').val();
   var token = $('#token').val();


       $.ajax({
        method: 'POST',
        url: '/report/filter-brands', 
        data: {'Supplier':supplier } ,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': token 
        },
        success: function(response) {
            console.log(response);
            var data = "";
            data+= "<option value='All'>All Brands</option>";
            $('select#brand option').remove();
            response.forEach(brand =>{
                data += "<option value='"+brand.brand_name+"'>"+brand.brand_name+"</option>";
            });
           $('select#brand').append(data);

        },
        error: function(error) {

        }
    });

}
</script>

@endpush
