@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Patient Details</h3>
                </div>

				<div class="col-lg-6 d-flex justify-content-end pe-5">
					<!-- <button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Staff</span></button> -->
                    @include('layouts.profile')
                </div>
			</div>

            <!-- Page content -->

            <div class="px-4 pt-4">
                <div class="form-card">
                <div class="row">
                    <div class="col-lg-6">

                    @if(isset($user))
                    <form action="{{ route('patients.update',$user->id) }}" method="post">
                        @method('put')
                    @else
                    <form action="{{ route('patients.store') }}" method="post">
                    @endif

                            @csrf
                            <div class="row">


                                <div class="col-lg-2">
                                    <div class="pb-3">
                                        <p class="form-label">Title</p>
                                        <select
                                            class="form-control {{ $errors->has('user_title') ? 'is-invalid' : '' }} form-select"
                                            name="user_title" >
                                            <option value="" selected disabled hidden>Select</option>
                                            <option value="Mr." {{ old('user_title',$user->user_title??'')=="Mr."  ? 'selected':'' }}>Mr</option>
                                            <option value="Master." {{ old('user_title',$user->user_title??'')=="Master."  ? 'selected':'' }}>Master</option>
                                            <option value="Miss." {{ old('user_title',$user->user_title??'')=="Miss."  ? 'selected':'' }}>Miss</option>
                                            <option value="Mrs." {{ old('user_title',$user->user_title??'')=="Mrs."  ? 'selected':'' }}>Mrs</option>
                                            <option value="Baby." {{ old('user_title',$user->user_title??'')=="Baby."  ? 'selected':'' }}>Baby</option>
                                            <option value="Dr." {{ old('user_title',$user->user_title??'')=="Dr."  ? 'selected':'' }}>Dr</option>
                                        </select>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('user_title') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Name</p>
                                        <input type="text" placeholder="Enter patient name"
                                            class="form-control form-text {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            name="name" value="{{ old('name',$user->name ?? '' )}}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="pb-3">
                                        <p class="form-label">Patient DOB</p>
                                        <input type="date" placeholder="Enter patient DOB"
                                            class="form-control form-text {{ $errors->has('DOB') ? 'is-invalid' : '' }}"
                                            name="DOB" value="{{ old('DOB',$user->DOB ?? '' )}}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('DOB') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Phone</p>
                                        <input type="text" placeholder="Enter patient phone"
                                            class="form-control form-text border-green {{ $errors->has('contact_number') ? 'is-invalid' : '' }}"
                                            name="contact_number" value="{{ old('contact_number',$user->contact_number ?? '' ) }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('contact_number') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Legacy System No</p>
                                        <input type="text" placeholder="Enter reg no"
                                            class="form-control form-text {{ $errors->has('reg_no') ? 'is-invalid' : '' }}"
                                            name="reg_no" value="{{ old('reg_no',$user->reg_no ?? $reg_no ) }}" readonly>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('reg_no') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Gender</p>
                                        <div class="radio-con d-flex">
                                            <label>
                                                <input type="radio" name="gender" value="Male" {{ @($user->gender == 'Male')? 'checked':'' }}/>
                                                <span >Male</span>
                                            </label>
                                            <label>
                                                <input type="radio" name="gender" value="Female" {{ @($user->gender == 'Female')? 'checked':'' }}/>
                                                <span >Female</span>
                                            </label>
                                            <label>
                                                <input type="radio" name="gender" value="other" {{ @($user->gender == 'other')? 'checked':'' }}/>
                                                <span value="other">Other</span>
                                            </label>
                                        </div>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('gender') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Patient Address</p>
                                        <input type="text" placeholder="Enter patient Address"
                                            class="form-control form-text {{ $errors->has('address') ? 'is-invalid' : '' }}"
                                            name="address" value="{{ old('address',$user->address ?? '' ) }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Medical History</p>
                                        <textarea class="form-text-area" placeholder="Enter medical history or Special needs" name="medical_history">{{ old('medical_history',$user->medical_history ?? '' )}}</textarea>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('medical_history') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                @if(Auth::check() && Auth::user()->user_role === 'Admin')
                                <hr />
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Credit Limit</p>
                                        <input type="text" placeholder="Enter credit limit"
                                            class="form-control form-text {{ $errors->has('credit_limit') ? 'is-invalid' : '' }}"
                                            name="credit_limit" value="{{ old('credit_limit',$user->credit_limit ?? '' ) }}">
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('credit_limit') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Credit Due</p>
                                        <input type="text" placeholder="Enter due days"
                                            class="form-control form-text {{ $errors->has('credit_due') ? 'is-invalid' : '' }}"
                                            name="credit_due" value="{{ old('credit_due',$user->credit_due ?? '' ) }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('credit_due') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                @endif
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        @if(isset($user))
                                        <button type="submit" class="main-button d1 a1-bg w-50">Update Patient</button>
                                            @method('put')
                                        @else
                                        <button type="submit" class="main-button d1 a1-bg w-50">Add Patient</button>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        </form>
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
                    <h4 class="headingH3 p-b-24">Delete Staff</h4>
                    <p class="p-b-40">Are you sure you want to delete, If you delete the staff it will permanently delete this record.</p>
                    <div class="row">
						<div class="col-lg-6">
							<button type="submit" class="main-button w-100" data-bs-dismiss="modal">Cancel</button>
						</div>
						<div class="col-lg-6">
							<button type="submit" class="main-button delete-color w-100">Delete</button>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>


@endsection
