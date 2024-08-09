@extends('layouts.report-main-header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Appointments</h3>
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

                <form id="dateRangeForm" action="{{ route('reports.appointments') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">

                <div class="row my-3">
                    <div class="col-lg-3">
                        <select class="form-select3 h-100" name="doctor">
                            <option value="" selected disabled hidden>Select Doctor</option>
                            @foreach ($doctors as $doctor )
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select3 h-100" name="shift">
                            <option value="Morning">Morning</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Evening">Evening</option>
                        </select>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center">
                        <div id="reportrange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                        <!-- Hidden inputs for start and end date -->
                        <input type="hidden" id="startDateInput" name="start_date">
                        <input type="hidden" id="endDateInput" name="end_date">
                        <button class="main-button d5-bg d1 rounded-0" type="submit">Filter</button>
                    </div>
                </div>
            </form>

			<table id="searchtbl" class="table" style="width:100%">

				<thead class="table-header">

                @if(isset($start_date))
                <div class="alert alert-success">From Date: {{ $start_date }} To {{ $end_date }}</div>
                <textarea style="display: none;" id="dt-title">{{ $start_date }} To {{ $end_date }}</textarea>

                @endif
					<tr>
                        <th>Date</th>
						<th>Patient Name</th>
                        <th>Doctor</th>
						<th>Token</th>
                        <th>Status</th>
                        <th class="text-end">Doc Fee</th>
                        <th class="text-end">Hos Fee</th>
					</tr>
				</thead>
				<tbody>
                @if(isset($appointments))
                @foreach($appointments as $appointment)
					<tr>
						<td>{{ $appointment->date }}<br /> {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
						<td>{{ $appointment->patient->name }}<br /> {{ $appointment->patient->contact_number?? 0 }}</td>
						<td>{{ $appointment->doctor->name }}</td>
                        <td>{{ $appointment->token_number }}</td>
                        <td>@if($appointment->status == 'Completed')
                            <span class="alert-success p-1">{{ $appointment->status }}</span>
                            @elseif($appointment->status == 'Cancelled')
                            <span class="alert-danger p-1">{{ $appointment->status }}</span>
                            @else
                            <span class="alert-info p-1">{{ $appointment->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($appointment->doctor_payment, 2) }}</td>
                        <td class="text-end">{{ number_format($appointment->hospital_fee, 2) }}</td>
					</tr>
                @endforeach
                @endif
			</table>

            <h5 id="totalSales" class="headingH5 text-end l-40 mt-4">Total Doctor Fee : {{ number_format($totalDoctorPayment??0, 2) }} <br />Hospital Fee : {{ number_format($totalHospitalFee??0, 2) }}</h5>

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
                    return 'Appointments';
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Appointments',
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
                    var columnCount = doc.content[2].table.body[0].length;

                    for (var i = 0; i < rowCount; i++) {
                        for (var j = 0; j < columnCount; j++) {
                            doc.content[2].table.body[i][0].alignment = 'left';
                            doc.content[2].table.body[i][1].alignment = 'left';
                            doc.content[2].table.body[i][2].alignment = 'left';
                            doc.content[2].table.body[i][3].alignment = 'left';
                            doc.content[2].table.body[i][4].alignment = 'left';
                            doc.content[2].table.body[i][5].alignment = 'right';
                            doc.content[2].table.body[i][6].alignment = 'right';
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
                var totalHos = 0;
                table.rows({search: 'applied'}).data().each(function (value, index) {
                    totalSales += parseFloat(value[5].replace(/[^0-9.-]+/g,""));
                    totalHos += parseFloat(value[6].replace(/[^0-9.-]+/g,""));
                });
                $('#totalSales').html('Total Doctor Fee : ' + totalSales.toFixed(2)+'<br />Hospital Fee : ' + totalHos.toFixed(2));
            });

            $('#search').keyup(function() {
                var table = $('#searchtbl').DataTable();
                table.search($(this).val()).draw();
            });
        });
</script>
@endpush
