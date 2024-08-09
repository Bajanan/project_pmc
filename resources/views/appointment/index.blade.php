@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Appointments</h3>
                </div>
				<!-- <div class="col-lg-4">
					<div class="search-container">
						<input class="form-search" placeholder="Type to search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div> -->
				<div class="col-lg-6 d-flex justify-content-end pe-5">
                    <a href="{{ route('appointments.create') }}"><button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">New Appointment</span></button></a>
                    @include('layouts.profile')
                </div>
			</div>

            <!-- Page content -->

            <div class="px-4 pt-4">
                <div class="form-card">
                <div id='calendar'></div>

                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h4 class="headingH3 p-b-24"><span class="a1">Patient:</span><br /><span id="eventTitle"></span></h4>
                            <!-- <p class="p-b-40"><span class="a1">Start Time:</span> <span id="eventStartDate"></span></p> -->
                        </div>
                    </div>
                </div>
                </div>

                </div>
            </div>
            <!-- Page content end -->
        </div>
    </section>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>

        //Full Calendar
        document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            timeZone: 'Asia/Colombo',
            initialView: 'listMonth',
            headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'listMonth,dayGridMonth'
            },
            slotDuration: '00:10',
            //events: '/api/demo-feeds/events.json',
            events: "{{ route('appointments.events') }}",
            slotMinTime: '06:00:00', // 6 AM
            slotMaxTime: '21:00:00', // 9 PM
            allDaySlot: false,
            displayEventTime: false,
            eventClick: function(info) {
                var doctorId = info.event.extendedProps.doctor_id;
                var adate = info.event.extendedProps.adate;
                var shift = info.event.extendedProps.shift;

                window.location.href = "{{ route('appointments.create') }}?doctor_id=" + doctorId + "&date=" + adate + "&shift=" + shift;
            }
        });

        calendar.render();
        });

    </script>

@endsection


