@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Suppliers</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					<a href="{{ route('suppliers.create') }}"><button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Supplier</span></button></a>
                    @include('layouts.profile')
                </div>
			</div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

 <!-- Page content -->

            <div class="px-4 pt-4">
            <div class="row">
                <div class="col-lg-3">
                    <a href="{{ route('brand-names.create') }}"><button class="dash-btn2">Brands</button></a>
                </div>
            </div>

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
					<tr>
						<th>Supplier Name</th>
						<th>Email</th>
						<th>Phone</th>
                        <th>Active</th>
						<th>Created</th>
						<th class="text-end">Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($suppliers as $supplier )


					<tr>
						<td>{{ $supplier->name}}</td>
						<td>{{ $supplier->email }}</td>
						<td>{{ $supplier->contact_number }}</td>
                        <td>{{ $supplier->active_status == 1 ? 'Active' : 'Inactive' }}</td></td>
                        <td>{{ $supplier->created_at->format('m-d-Y') }}</td>
						<td class="d-flex justify-content-end">
							<a href="{{ route('suppliers.show',$supplier->id) }}" class=""><i class="fa-solid fa-eye view-icon"></i></a>
							<a href="{{ route('suppliers.edit',$supplier->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>
							<form action="{{ route('suppliers.destroy',$supplier->id) }}" method="post">
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
                <div class="modal-body text-center">
                    <h4 class="headingH3 p-b-24">Delete Supplier</h4>
                    <p class="p-b-40">Are you sure you want to delete, If you delete the supplier it will permanently delete this record.</p>
					<form >
						@csrf
						@method('delete')
                    <div class="row">
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
