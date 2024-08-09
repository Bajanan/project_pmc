@extends('layouts.header')
<style>

</style>
@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-3">
                    <h3 class="headingH3">Appointments</h3>
                </div>
				<div class="col-lg-9 d-flex justify-content-end pe-5">
					<!-- <button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Staff</span></button> -->
                    @include('layouts.profile')
                </div>
			</div>

            <!-- Page content -->

            <div class="px-4 pt-4">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-card">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form action="{{ route('appointments.store') }}" method="post" id="form">
                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                 {{ session('status') }}
                                            </div>
                                        @endif
                                        <input type="hidden" name="_token" id="token" value="{{  csrf_token()  }}">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <div class="pb-3">
                                                    <p class="form-label">Token No</p>
                                                    <input type="text" placeholder=""
                                                        class="form-control form-text border-green {{ $errors->has('token_no') ? 'is-invalid' : '' }}"
                                                        name="token_number" value="" id="token_no" readonly>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('token_no') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-10">
                                                <div class="pb-3">
                                                    <p class="form-label">Patient Name</p>
                                                    <div class="d-flex align-items-center">
                                                        <select id="patient" data-placeholder="Select Patient" name="patient" class=" SelExample form-control form-select" required>
                                                        <option value="">Select Patient</option>
                                                            @foreach($patients as $patient)
                                                            <option value="{{ $patient->id }}" {{ $patientId == $patient->id ? 'selected' : '' }}>{{ $patient->name }}</option>
                                                            @endforeach
                                                           </select>
                                                        <a data-bs-toggle="modal" data-bs-target="#addModal"><div class="add-btn">+</div></a>
                                                    </div>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('patient') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="pb-3">
                                                    <p class="form-label">Doctor Name</p>
                                                        <select id="doctor" data-placeholder="Select Doctor" name="doctor" class="SelExample form-control form-select" onchange="appointmentAvailability();" required>
                                                        <option value="">Select Doctor</option>
                                                         @foreach($doctors as $doctor)
                                                         <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                         @endforeach
                                                        </select>

                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('doctor') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="pb-3">
                                                    <p class="form-label">Shift</p>
                                                    <select
                                                        class="form-control {{ $errors->has('company_status') ? 'is-invalid' : '' }} form-select"
                                                        name="shift" id="shift" onchange="appointmentAvailability();" required>
                                                        <option value="" selected disabled hidden>Select Shift</option>
                                                        <option value="{{App\Models\Appointment::MORNING }}">Morning</option>
                                                        <option value="{{App\Models\Appointment::LUNCH }}">Lunch</option>
                                                        <option value="{{App\Models\Appointment::EVENING }}">Evening</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="pb-3">
                                                    <p class="form-label">Date</p>
                                                    <input type="date" placeholder="Enter doctor email"
                                                        class="form-control form-text {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                                        name="date" value="<?php echo date('Y-m-d'); ?>"  onchange="appointmentAvailability();" id="date" required>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('date') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="pb-3">
                                                    <p class="form-label">Time</p>
                                                    <input type="time" placeholder="Enter doctor email"
                                                        class="form-control form-text {{ $errors->has('time') ? 'is-invalid' : '' }}"
                                                        name="time" value="" id="time" required min="06:00" max="21:00" oninput="validateTime(this)" onchange="validateTime(this)">
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('time') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3 pt-3">
                                                    <button type="submit" class="main-button d1 a1-bg">Create Appointment</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6">
                        <div class="o-scroll h-1000">
                            <div id="table"></div>
                        </div>
                        <h5 id="totalpayment"></h5>
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
                    <h4 class="headingH3 text-center p-b-24">Add Patient</h4>
                    <form action="{{ route('appointment.newPatient') }}" method="post">
                        @csrf
                            <div class="row">
                                <input type="hidden" name="_token" id="token3" value="{{  csrf_token()  }}">
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Phone</p>
                                        <input type="text" placeholder="Enter patient phone"
                                            class="form-control form-text border-green {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            name="contact_number" value="" id="phone" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Name</p>
                                        <input type="text" placeholder="Enter patient name"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="name" value="" id="name" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient DOB</p>
                                        <input type="date" placeholder=""
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="DOB" value="" id="DOB" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Location</p>
                                        <input type="text" placeholder="Enter patient location"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="address" value="" id="location" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        <button type="submit" class="main-button d1 a1-bg">Add Patient</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<!-- reschedule box -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="headingH3 text-center p-b-24">Reschedule Appointment</h4>
                    <form >

                        <input type="hidden" name="_token" id="token2" value="{{  csrf_token()  }}">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Doctor Name</p>
                                        <input type="text" placeholder=""
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                             value="" id="doctor_name" readonly>
                                        <span class="invalid-feedback" role="alert">
                                            <input type="hidden" name="reschedule_doctor" id="doctor_id">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Appointment Start</p>
                                          <select
                                                        class="form-control {{ $errors->has('company_status') ? 'is-invalid' : '' }} form-select"
                                                        name="new_shift" id="reschedule_shift" required>
                                                        <option value="" selected disabled hidden>Select Shift</option>
                                                        <option value="{{App\Models\Appointment::MORNING }}">Morning</option>
                                                        <option value="{{App\Models\Appointment::LUNCH }}">Lunch</option>
                                                        <option value="{{App\Models\Appointment::EVENING }}">Evening</option>
                                                    </select>
                                        {{-- <input   class="form-select form-text border-green" name="shift"  id="reschedule_shift" > --}}

                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>

                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">New Date</p>
                                        <input type="date" placeholder=""
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="new_date" value="" id="reschedule_date" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>

                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">New Time</p>
                                        <input type="time" placeholder=""
                                            class="form-control form-text {{ $errors->has('new_time') ? 'is-invalid' : '' }}"
                                            name="new_time" min="08:00" max="20:00" value="" id="new_time" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('new_time') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="old_shift" value="" id="old_shift">
                                <input type="hidden" name="old_date" value="" id="old_date">

                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        <button type="button" class="main-button d1 a1-bg w-100" onclick="rescheduleForm();appointmentAvailability();">Reschedule</button>
                                        <button type="button" class="main-button d1 d5-bg w-100 mt-3" onclick="cancelSchedule();">Cancel All Appointments</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>

        //Full Calendar
        document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            timeZone: 'Asia/Colombo',
            initialView: 'timeGridDay',
            headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotDuration: '00:30',
            //events: '/api/demo-feeds/events.json',
            events: "{{ route('appointments.events') }}",
            slotMinTime: '08:00:00', // 8 AM
            slotMaxTime: '20:00:00', // 8 PM
            allDaySlot: false,
            displayEventTime: false,
            eventClick: function(info) {
                // Show event details in modal
            }
        });

        calendar.render();
        });

</script>

<script>
    function validateTime(input) {
            const inputTime = input.value;
            const time = new Date('1970-01-01T' + inputTime + 'Z'); // Create a date object with the input time
            const minTime = new Date('1970-01-01T06:00:00Z');
            const maxTime = new Date('1970-01-01T21:00:00Z');

            if (time < minTime || time > maxTime) {
                alert('Please select a time between 6:00 AM and 9:00 PM');
                input.value = ''; // Clear the input if the time is out of range
            }
        }
</script>

<script>
//   window.alert("fs");
    function appointmentAvailability(){
    var form_data =  $('#doctor,#shift,#date').serialize();
    var token = $('#token').val();

    $.ajax({
				method: 'POST',
				url: '/appointments/availability', // Replace with your route URL
				data: form_data ,
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
				},
				success: function(response) {
					console.log(response);
                    // response.all_appointments.forEach(element => {
                    //     console.log(element.id);
                    // });
                    $('#token_no').val( response.next_token_no);

                    //making the data table
                    if(response.all_appointments){

                    var totalDoctorPayment = 0;

                    var data =  "<h5 class='headingH3 pb-3'>"+response.doctor+"'s Schedule </h5>";
                    data += "<table id='searchtbl' class='table' style='width:100%'>";
                    data += " <thead class='table-header'><tr><th>Token No</th><th>Patient</th><th>Time</th><th>Status</th><th>Paid</th></tr></thead>";
                    data += " <tbody>";
                    response.all_appointments.forEach(appointment => {
                        data += "<tr><td>"+appointment.token_number+"</td><td>"+appointment.patient.name+"<br />"+appointment.patient.contact_number+"</td><td>"+appointment.time+"</td>";

                        if (appointment.status === "Pending") {
                          var csrfToken = "{{ csrf_token() }}";
                          var id = appointment.id;
                            data += "<td><form id='deleteForm' action='{{ route('appointment.update',':id') }}' method='POST'>";
                            data += "<input type='hidden' name='_method' value='PUT'>";
                            data += "<input type='hidden' name='_token' value='"+csrfToken+"'>";// Add your CSRF token here
                            data = data.replace(':id', id);
                            data += "<button type='submit' class='no-style'><i class='fa fa-cancel text-danger'></i></button>";
                            data += "</form></td>";
                            var payLink = "{{ url('service-billing/create') }}" + "?patient_id=" + appointment.patient_id + "&appointment_id=" + appointment.id+ "&doctor_name=" + response.doctor;
                            data += `<td><a class='view-btn pay' href='${payLink}'>Pay</a></td></tr>`;

                        } else {
                            data += "<td class=''>"+appointment.status+"</td>";
                            data += "<td class=''>"+appointment.doctor_payment+"</td></tr>";
                            totalDoctorPayment += parseFloat(appointment.doctor_payment);
                        }


                    });
                     data += "</tbody></table>";
                     data += "<div> <div class='pb-3 pt-4 text-end'><a data-bs-toggle='modal' data-bs-target='#rescheduleModal'><button type='button' class='main-button d1 d5-bg'>Reschedule</button></a></div></div>";
                    }else{
                        var data = "<div class='alert alert-secondary'>No appointments at the momenet</div>";
                    }

                    document.getElementById('table').innerHTML = data;
                    $('#time').val(response.time);

                    $('#totalpayment').html('Total Payment: '+ totalDoctorPayment.toFixed(2));

                     //reschedule data
                     $('#doctor_name').val( response.doctor);
                     $('#doctor_id').val( response.doctor_id);
                     $('#reschedule_shift').val( response.shift);
                     $('#reschedule_date').val(response.date);

                     //old data in hidden fields
                     $('#old_shift').val( response.shift);
                      $('#old_date').val( response.date);

				},
				error: function(error) {
					// Handle error

				}
			});
    }

   function rescheduleForm(){

        var doctor = $('#doctor_id').val();
        var shift = $('#old_shift').val();
        var date = $('#old_date').val();
        var new_time = $('#new_time').val();
        var new_date = $('#reschedule_date').val();
        var new_shift = $('#reschedule_shift').val();


        var token = $('#token2').val();

        $.ajax({
				method: 'POST',
				url: '/appointments/reschedule', // Replace with your route URL
				data: {'Doctor':doctor,'Shift':shift,'Date':date,'newTime':new_time,'newDate':new_date,'newShift':new_shift},
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
				},
				success: function(response) {
                    $('#rescheduleModal').modal('hide');
					console.log(response);


				},
				error: function(error) {
					// Handle error

				}
			});

    }

    function cancelSchedule(){


        var doctor = $('#doctor_id').val();
        var shift = $('#old_shift').val();
        var date = $('#old_date').val();
        var token = $('#token2').val();

        $.ajax({
				method: 'POST',
				url: '/appointments/cancel-schedule', // Replace with your route URL
				data: {'Doctor':doctor,'Shift':shift,'Date':date},
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
				},
				success: function(response) {
                    $('#rescheduleModal').modal('hide');
					console.log(response);


				},
				error: function(error) {
					// Handle error

				}
			});

    }


    function newPatient(){
        var form_data =  $('#phone,#name,#DOB,#location').serialize();
        var token = $('#token3').val();

        $.ajax({
				method: 'POST',
				url: '/appointments/add-patient', // Replace with your route URL
				data: form_data ,
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token // Use the CSRF token from your layout or view
				},
				success: function(response) {
                    $('#addModal').modal('hide');
                    $('#patient').val('2'); // Set value
                    $('#patient option[value="2"]').prop('text', 'New Option Text'); // Set text
					console.log(response);


				},
				error: function(error) {
					// Handle error

				}
			});

    }


    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const doctorId = urlParams.get('doctor_id');
        const date = urlParams.get('date');
        const shift = urlParams.get('shift');

        if (doctorId && date && shift) {
            document.getElementById('doctor').value = doctorId;
            document.getElementById('shift').value = shift;
            document.getElementById('date').value = date;
            appointmentAvailability();
        }
    });


</script>
