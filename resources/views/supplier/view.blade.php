@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Supplier Details</h3>
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
                                    <td>Supplier ID</td>
                                    <td class="fw-bold">{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td>Supplier Name</td>
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
                                    <td class="fw-bold">{{ $user->active_status }}</td>
                                </tr>
                            </table>
                            <div class="d-flex pt-4">
                                <div class="pb-3 pt-3">
                                    <a href="{{ route('suppliers.edit',$user->id) }}"><button type="submit" class="main-button d1 a1-bg w-100">Edit Supplier</button></a>
                                </div>
                                <div class="pb-3 pt-3">
                                <form action="{{ route('suppliers.destroy',$user->id) }}" method="post">
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
                            <h3 class="headingH3 pb-3">Supplier Bills</h3>
                             <table class="table-details" style="width:100%">
                                <thead>
                                    <tr class="fw-bold">
                                        <td>GRN No</td>
                                        <td>Invoice No</td>
                                        <td class="text-end">Date</td>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($grns as $grn)
                                    <tr>
                                        <td>{{ $grn->GRN_No }}</td>
                                        <td>{{ $grn->invoice_no }}<br />{{ $grn->date }}</td>
                                         <td class="text-end">{{ number_format($grn->total_cost, 2) }}</td>
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

@endsection
