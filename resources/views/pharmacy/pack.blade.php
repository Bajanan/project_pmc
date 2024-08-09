@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-3">
                    <h3 class="headingH3">Pack Sizes</h3>
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
                                    @if(isset($packSize))
                                    <form action="{{ route('pack-sizes.update',$packSize->id) }}" method="post">
                                        @method('put')
                                        @else
                                        <form action="{{ route('pack-sizes.store') }}" method="post">
                                    @endif
                                    @csrf
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
                                                    <p class="form-label">Pack size Name</p>
                                                    <input type="text" placeholder="10 x 10s"
                                                        class="form-control form-text mt-0 {{ $errors->has('pack_size') ? 'is-invalid' : '' }}"
                                                        name="pack_size" value="{{ old('pack_size',$packSize->pack_size ?? '' )}}" required>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('pack_size') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3">
                                                    <p class="form-label">Pack size Value</p>
                                                    <input type="text" placeholder="100"
                                                        class="form-control form-text mt-0 {{ $errors->has('pack_size_value') ? 'is-invalid' : '' }}"
                                                        name="pack_size_value" value="{{ old('pack_size_value',$packSize->pack_size_value ?? '' )}}" required>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('pack_size_value') }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="pb-3 pt-3">
                                                    @if(isset($packSize))
                                                    <button type="submit" class="main-button d1 a1-bg w-50">Update Pack Size</button>
                                                     @else
                                                <button type="submit" class="main-button d1 a1-bg w-50">Add Pack Size
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
                        <h3 class="headingH3 pb-3">Manage Pack Size</h3>
                            <table id="searchtbl" class="table-details" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pack</th>
                                        <th>Size</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packSizes as $packSize )
                                    <tr>
                                        <td>{{ $packSize->id }}</td>
                                        <td>{{ $packSize->pack_size }}</td>
                                        <td>{{ $packSize->pack_size_value }}</td>
                                        <td class="d-flex justify-content-end">
                                            @if( isset($packSize->deleted_at))
                                             <form action="{{ route('pack-sizes.restore',$packSize->id) }}" method="post">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure to restore?')"  class="ms-3 no-style"><i class="fa-solid fa-toggle-on active-icon inactive"></i></button>
                                            </form>
                                            @else
                                            <a href="{{ route('pack-sizes.edit', $packSize->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>
                                             <form action="{{ route('pack-sizes.destroy',$packSize->id) }}" method="post">
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
