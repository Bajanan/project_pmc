@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Doctor Details</h3>
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

            <!-- Page content -->

            <div class="px-4 pt-4">

                    <div class="row">
                        <div class="col-lg-6">
                        <div class="form-card">
                            <table class="table-details" style="width:100%">
                                <tr>
                                    <td>Doctor ID</td>
                                    <td class="fw-bold">{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td>Doctor Name</td>
                                    <td class="fw-bold">{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td class="fw-bold">{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td class="fw-bold">{{ $user->contact_number }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>

                                    <td class="fw-bold">@if($user->active_status == 1)
                                        Active
                                    @else
                                        Inactive
                                    @endif</td>
                                </tr>
                            </table>
                            <div class="d-flex pt-4">
                                <div class="pb-3 pt-3">
                                    <a href="{{ route('doctors.edit',$user->id) }}"><button type="submit" class="main-button d1 a1-bg w-100">Edit Doctor</button></a>
                                </div>
                                <div class="pb-3 pt-3">
                                <form action="{{ route('doctors.destroy',$user->id) }}" method="post">
						            @method('delete')
						            @csrf
                                <button type="submit" class="main-button delete-color d1 w-100 ms-4" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-card">
                            <h3 class="headingH3 pb-3">Doctor Schedule</h3>
                            <table class="table-details" style="width:100%">
                                <thead>
                                    <tr class="fw-bold">
                                        <td>Patient Name</td>
                                        <td>Shift</td>
                                        <td>Time</td>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->Patient->name }}</td>
                                        <td>{{ $schedule->shift }}</td>
                                        <td>{{\Carbon\Carbon::parse($schedule->time)->format('h:i A')  }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Page content end -->
        </div>
    </section>


    <!-- Alert box -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h4 class="headingH3 p-b-24">Delete Doctor</h4>
                    <p class="p-b-40">Are you sure you want to delete, If you delete the doctor it will permanently delete this record.</p>
                    <form action="{{ route('doctors.destroy',$user->id) }}" method="post">
                    <div class="row">

                            @method('delete')
                            @csrf
						<div class="col-lg-6">
							<button type="button" class="main-button w-100" data-bs-dismiss="modal">Cancel</button>
						</div>
						<div class="col-lg-6">
							<button type="submit" class="main-button delete-color w-100">Delete</button>
						</div>

					</div>
                </form>
                </div>
            </div>
        </div>
    </div>


@endsection
