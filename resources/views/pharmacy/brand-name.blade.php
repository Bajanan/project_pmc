@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-3">
                    <h3 class="headingH3">Brands</h3>
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
                                    @if(isset($brandName))
                                    <form action="{{ route('brand-names.update',$brandName->id) }}" method="post">
                                        @method('put')
                                        @else
                                        <form action="{{ route('brand-names.store') }}" method="post">
                                    @endif

                                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}" >
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
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Supplier</p>
                                                    <select
                                                        class="form-control {{ $errors->has('supplier') ? 'is-invalid' : '' }} form-select"
                                                        name="supplier" id="supplier" required {{ isset($brandName->supplier_id) ? 'disabled' : '' }}>
                                                        <option value="" selected disabled hidden>Select Supplier</option>
                                                       @foreach ($suppliers as $supplier )
                                                       <option value="{{ $supplier->id }}" {{ @($brandName->supplier_id == $supplier->id)? 'selected':''}}> {{ $supplier->name }}</option>
                                                       @endforeach
                                                    </select>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('supplier') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Brand</p>
                                                    <input type="text" placeholder="Enter brand"
                                                        class="form-control form-text mt-0 {{ $errors->has('brand_name') ? 'is-invalid' : '' }}"
                                                        name="brand_name" value="{{ old('brand_name',$brandName->brand_name ?? '' )}}" id="brand_name" required>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('brand_name') }}</strong>
                                                    </span>
                                                </div>

                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3 pt-3">
                                                    @if(isset($brandName))
                                                    <button type="submit" class="main-button d1 a1-bg w-50">Update Brand</button>
                                                     @else
                                                    <button type="submit" class="main-button d1 a1-bg w-50">Add Brand</button>
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
                    <h3 class="headingH3 pb-3">Manage Brands</h3>
                            <table id="searchtbl" class="table-details" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Supplier</th>
                                        <th>Brand</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brandNames as $brandName)

                                    <tr>
                                        <td>{{ $brandName->id }}</td>
                                        <td>{{ $brandName->user->name }}</td>
                                        <td>{{ $brandName->brand_name }}</td>
                                        <td class="d-flex justify-content-end">

                                            @if( isset($brandName->deleted_at))
                                             <form action="{{ route('brand-names.restore',$brandName->id) }}" method="post">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure to restore?')"  class="ms-3 no-style"><i class="fa-solid fa-toggle-on active-icon inactive"></i></button>
                                            </form>
                                            @else
                                            <a href="{{ route('brand-names.edit', $brandName->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>

                                            <form action="{{ route('brand-names.destroy',$brandName->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" onclick="return confirm('Are you sure to delete?')"  class="ms-3 no-style"><i class="fa-solid fa-toggle-on active-icon active"></i></button>
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
                    <form >
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
