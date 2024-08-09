@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Company Return</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					<a href="{{ route('company-return.create') }}"><button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">New Return</span></button></a>
                    @include('layouts.profile')
                </div>
			</div>

 <!-- Page content -->

            <div class="px-4 pt-4">

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
					<tr>
                        <th>CRN #</th>
						<th>Date</th>
                        <th>Supplier</th>
                        <th>Reason</th>
						<th class="text-end">Total Amount</th>
						<th class="text-end">Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($all_company_returns as $company_return)
					<tr>
						<td>{{ $company_return->CRN_No }}</td>
						<td>{{ $company_return->date }}</td>
						<td>{{ $company_return->Supplier->name }}</td>
                        <td>{{ $company_return->reason }}</td>
                        <td class="text-end">{{ number_format($company_return->total_cost, 2) }}</td>
						<td class="text-end">
							<a href="{{ route('company-return.show',$company_return->id) }}" class="ms-3"><i class="fa-regular fa-eye edit-icon"></i></a>
						</td>
					</tr>
					@endforeach
			</table>

        </div>

            <!-- Page content end -->
        </div>
    </section>

@endsection
