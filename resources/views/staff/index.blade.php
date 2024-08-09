@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Staffs</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					<a href="{{ route('staffs.create') }}"><button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Staff</span></button></a>
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
						<th>Staff Name</th>
						<th>Email</th>
						<th>User Type</th>
                        <th>Active</th>
						<th>Created</th>
						<th class="text-end">Action</th>
					</tr>
				</thead>
				<tbody>

					@foreach ($staffs as $staff )
					<tr>
						<td>{{ $staff->name }}</td>
						<td>{{ $staff->email }}</td>
						<td>{{ $staff->user_role }}</td>
                        <td>{{ $staff->active_status == 1 ? 'Active' : 'Inactive' }}</td></td>
                        <td>{{ $staff->created_at->format('d-m-Y') }}</td>
						<td class="d-flex justify-content-end">
							<a href="{{ route('staffs.show',$staff->id) }}" class=""><i class="fa-solid fa-eye view-icon"></i></a>
							<a href="{{ route('staffs.edit',$staff->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>
							<form action="{{ route('staffs.destroy',$staff->id) }}" method="post">
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
