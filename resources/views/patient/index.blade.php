@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Patients</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					<a href="{{ route('patients.create') }}"><button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Patient</span></button></a>
                    @include('layouts.profile')
                </div>
			</div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
 <!-- Page content -->

            <div class="px-4 pt-4">

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
					<tr>
						<th>Patient Name</th>
						<th>Phone</th>
                        <th>Address</th>
                        <th>Age</th>
                        <th>Age Category</th>
						<th class="text-end">Action</th>
					</tr>
				</thead>
				<tbody>

					@foreach($all_patients as $patient)
					<tr>
						<td>{{ $patient->user_title }} {{ $patient->name }} <br> {{ $patient->reg_no }}</td>
						<td>{{ $patient->contact_number}}</td>
						<td>{{ $patient->address }}</td>
                        <td class="fw-bold">{{calculateAgeinmonths($patient->DOB)}}</td>
                        <td>{{ calculateAgeRange(calculateAge($patient->DOB)) }}</td>
						<td class="d-flex justify-content-end">
							<a href="{{ route('patients.show',$patient->id) }}" class=""><i class="fa-solid fa-eye view-icon"></i></a>
							<a href="{{ route('patients.edit',$patient->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>
						<form action="{{ route('patients.destroy',$patient->id) }}" method="post">
						@method('delete')
						@csrf
						<button type="submit" class="ms-3 no-style"  onclick="return confirm('Are you sure?')"><i class="fa-regular fa-trash-can trash-icon"></i></button>
						</form>
						</td>
					</tr>
					@endforeach
			</table>

        </div>

            <!-- Page content end -->
        </div>
    </section>

@endsection

@php
function calculateAge($dob) {
    $dob = new DateTime($dob);
    $now = new DateTime();
    $age = $dob->diff($now)->y;
    return $age;
}

function calculateAgeRange($age) {
    if ($age >= 0 && $age <= 2) {
        return "Baby";
    } elseif ($age >= 3 && $age <= 9) {
        return "Child";
    } elseif ($age >= 10 && $age <= 12) {
        return "Pre Teen";
    } elseif ($age >= 13 && $age <= 17) {
        return "Teenager";
    } elseif ($age >= 18 && $age <= 24) {
        return "Young Adult";
    } elseif ($age >= 25 && $age <= 60) {
        return "Adult";
    } else {
        return "Elder";
    }
}

function calculateAgeinmonths($dob) {
    $dob = new DateTime($dob);
    $now = new DateTime();
    $age = $dob->diff($now);
    $years = $age->y;
    $months = $age->m;
    return "$years y $months m";
}
@endphp
