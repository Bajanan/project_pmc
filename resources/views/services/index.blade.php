@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-3">
                    <h3 class="headingH3">New Service</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-5 d-flex justify-content-end pe-5">
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
                                    @if(isset($service))
                                    <form action="{{ route('services.update',$service->id) }}" method="post">
                                        @method('put')
                                        @else
                                        <form action="{{ route('services.store') }}" method="post">
                                        @endif
                                        @csrf
                                        @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Service Type</p>
                                                    <select
                                                        class="form-control {{ $errors->has('service_type') ? 'is-invalid' : '' }} form-select"
                                                        name="service_type" required>
                                                        <option value="" selected disabled hidden>Service</option>
                                                        <option  {{ (@$service->service_type =="Test" ) ? 'selected':'' }} value="Test">Test</option>
                                                        <option  {{ (@$service->service_type =="Consultation" ) ? 'selected':'' }} value="Consultation">Consultation</option>
                                                        <option  {{ (@$service->service_type =="Booking" ) ? 'selected':'' }} value="Booking">Booking</option>
                                                        <option  {{ (@$service->service_type =="Surgical" ) ? 'selected':'' }}  value="Surgical">Surgical</option>
                                                    </select>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('service_type') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Service Description</p>
                                                    <input type="text" placeholder="Service Description"
                                                        class="form-control form-text mt-0 {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                        name="description" value="{{ old('description',$service->description ?? '' )}}" >
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('description') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Unit Price</p>
                                                    <input type="text" placeholder="Enter unit price"
                                                        class="form-control form-text {{ $errors->has('unit_price') ? 'is-invalid' : '' }}"
                                                        name="unit_price" value="{{ old('unit_price',$service->unit_price ?? '' )}}" r>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('unit_price') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3 pt-3">
                                                    @if(isset($service))
                                                    <button type="submit" class="main-button d1 a1-bg w-50">Update Service</button>
                                                        @else
                                                        <button type="submit" class="main-button d1 a1-bg w-50">Create Service</button>
                                                        @endif

                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="headingH3 pb-3">Manage Services</h3>
                            <table id="searchtbl" class="table-details" style="width:100%">
                                <thead>

                                    <tr>
                                        <th>#</th>
                                        <th>Service</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $service )
                                    <tr>
                                        <td>{{ $service->id }}</td>
                                        <td>{{ $service->service_type }}</td>
                                        <td>{{ $service->description }}</td>
                                        <td>{{ $service->unit_price }}</td>
                                        <td class="d-flex justify-content-end">
                                            @if( isset($service->deleted_at))
                                            <form action="{{ route('services.restore',$service->id) }}" method="post">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure to restore?')"  class="ms-3 no-style"><i class="fa-solid fa-toggle-on active-icon inactive"></i></button>
                                            </form>
                                            @else
                                            <a href="{{ route('services.edit', $service->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>
                                            <form action="{{ route('services.destroy',$service->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" onclick="return confirm('Are you sure to delete ?')"  class="ms-3 no-style"><i class="fa-solid fa-toggle-on active-icon"></i></button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
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
                    <h4 class="headingH3 p-b-24">Delete Service</h4>
                    <p class="p-b-40">Are you sure you want to delete, If you delete the service it will permanently delete this record.</p>
                    <form action="#" method="post">
                        @method('delete')
                        @csrf
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
