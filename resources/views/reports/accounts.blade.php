@extends('layouts.report-main-header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Accounts</h3>
                </div>
				<div class="col-lg-4">
                    <!-- <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div> -->
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					@include('layouts.profile')
                </div>
			</div>

 <!-- Page content -->

            <div class="px-4 pt-4">

           <div class="filter-sec row align-items-start my-3">

                <form id="dateRangeForm" action="{{ route('reports.accounts') }}" method="GET">
                @csrf <!-- Added an ID to the form for easier reference -->
                    <div class="col-lg-6 d-flex align-items-center">
                        <div id="reportrange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                        <!-- Hidden inputs for start and end date -->
                        <input type="hidden" id="startDateInput" name="start_date">
                        <input type="hidden" id="endDateInput" name="end_date">
                        <button class="main-button d5-bg d1 rounded-0" type="submit">Search</button>
                    </div>
                </form>
            </div>

			<table id="searchtbl" class="table" style="width:100%">
            <thead class="table-header">
                 @if(isset($start_date))
                <div class="alert alert-success">From Date: {{ $start_date }} To {{ $end_date }}</div>
                <textarea style="display: none;" id="dt-title">{{ $start_date }} To {{ $end_date }} | Total Sales: {{ number_format($total_sales??0, 2) }} | Total Due: {{ number_format($total_due??0, 2) }} | Cash: {{ number_format(($total_sales - $total_due), 2)}}</textarea>
                @endif
                <tr>
                    <th>Date</th>
                    <th>Invoice # / Patient Name</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Due</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($combined))
                    @foreach ($combined as $item)
                        <tr>
                            <td>{{ $item['created_at'] }}</td>
                            <td>
                                @if($item['invoice_no'])
                                    {{ $item['invoice_no'] }}
                                @else
                                    Repay - {{ $item['patient_name'] }}<br /> {{ $item['patient_reg_no'] ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                            <td class="text-end">{{ number_format($item['due_amount'], 2) ?? 0.00 }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <h5 id="totalSales" class="headingH5 l-40 text-end mt-4">Total In : {{ number_format($total_sales??0, 2) }} <br />Total Dues: {{ number_format($total_due??0, 2) }} <br />Cash: {{ number_format(($total_sales - $total_due), 2)}}</h5>

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
                    return 'Sales Report';
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Sales Report',
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
                            doc.content[2].table.body[i][1].alignment = 'left';
                            doc.content[2].table.body[i][2].alignment = 'right';
                            doc.content[2].table.body[i][3].alignment = 'right';
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

             // Recalculate total sales on DataTable redraw
            table.on('draw', function () {
                var totalSales = 0;
                var totalDues = 0;
                table.rows({search: 'applied'}).data().each(function (value, index) {
                    totalSales += parseFloat(value[2].replace(/[^0-9.-]+/g,""));
                    totalDues += parseFloat(value[3].replace(/[^0-9.-]+/g,""));
                });
                $('#totalSales').html('Total Sales : ' + totalSales.toFixed(2) + '<br />Total Dues : ' + totalDues.toFixed(2)+ '<br />Cash : ' + (totalSales - totalDues).toFixed(2) );
            });

            $('#search').keyup(function() {
                var table = $('#searchtbl').DataTable();
                table.search($(this).val()).draw();
            });
        });
</script>
@endpush
