@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Doctors</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					<a href="{{ route('doctors.create') }}"><button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Doctor</span></button></a>
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
						<th>Doctor</th>
						<th>Email</th>
						<th>Phone</th>
                        <th>Active</th>
						<th>Created</th>
						<th class="text-end">Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($doctors as $doctor)

					<tr>
						<td>{{ $doctor->name }}</td>
						<td>{{ $doctor->email }}</td>
						<td>{{ $doctor->contact_number }}</td>
                        <td>{{ $doctor->active_status == 1 ? 'Active' : 'Inactive' }}</td>
                        <td>{{ $doctor->created_at->format('d-m-Y') }}</td>
						<td class="d-flex justify-content-end">
							<a href="{{ route('doctors.show',$doctor->id) }}" class=""><i class="fa-solid fa-eye view-icon"></i></a>
							<a href="{{ route('doctors.edit',$doctor->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>
							<form action="{{ route('doctors.destroy',$doctor->id) }}" method="post">
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


    <!-- Alert box -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
				<form >
					@csrf
					@method('delete')
                <div class="modal-body text-center">
                    <h4 class="headingH3 p-b-24">Delete Doctor</h4>
                    <p class="p-b-40">Are you sure you want to delete, If you delete the doctor it will permanently delete this record.</p>
                    <div class="row">
						<div class="col-lg-6">
							<button type="submit" class="main-button w-100" data-bs-dismiss="modal">Cancel</button>
						</div>
						<div class="col-lg-6">
							<button type="submit" class="main-button delete-color w-100">Delete</button>
						</div>
					</div>
                </div>


			</form>
            </div>
        </div>
    </div>


@endsection
