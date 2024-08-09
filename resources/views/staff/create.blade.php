@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Staff Details</h3>
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
                <div class="form-card">
                <div class="row">
                    <div class="col-lg-6">
                        @if(isset($user))
                        <form  action="{{ route('staffs.update',$user->id) }}" method="post">
                            @method('put')
                            @else
                        <form  action="{{ route('staffs.store') }}" method="post">
                            @endif
                            @csrf

                            <div class="row">

                                @if(!isset($user))
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Password</p>
                                            <div class="icon-container">
                                                <input id="password" type="password" placeholder="Enter a password"
                                                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }} form-text"
                                                    name="password" required>
                                                <a onclick="togglePasswordVisibility()">
                                                    <i id="password_img" class="fa fa-eye-slash d5 icon-over"></i>
                                                </a>

                                                @if ($errors->has('password'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Staff Name</p>
                                        <input type="text" placeholder="Enter staff name"
                                            class="form-control form-text {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            name="name" value="{{ old('name',$user->name ?? '' ) }}" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Staff Email</p>
                                        <input type="email" placeholder="Enter staff email"
                                            class="form-control form-text {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                            name="email" value="{{ old('email',$user->email ?? '' ) }}" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Staff Phone</p>
                                        <input type="text" placeholder="Enter staff phone"
                                            class="form-control form-text {{ $errors->has('contact_number') ? 'is-invalid' : '' }}"
                                            name="contact_number" value="{{ old('contact_number',$user->contact_number ?? '' ) }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('contact_number') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Staff Role</p>
                                        <select
                                            class="form-control {{ $errors->has('user_role') ? 'is-invalid' : '' }} form-select"
                                            name="user_role" >
                                            <option value="" selected disabled hidden>Select Role</option>
                                            <option {{ @($user->user_role == App\Models\User::STAFF)? 'selected':''  }} value="{{ App\Models\User::STAFF  }}">Staff
                                            </option>
                                            <option {{ @($user->user_role == App\Models\User::MANAGER)? 'selected':''  }} value="{{ App\Models\User::MANAGER  }}">Manager
                                            </option>
                                            <option {{ @($user->user_role == App\Models\User::ADMIN)? 'selected':''  }} value="{{ App\Models\User::ADMIN  }}">
                                                Admin
                                            </option>
                                        </select>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('user_role') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-4">
                                        <p class="form-label">Staff Active</p>
                                        <label class="checkbox_wrap">
                                        @if(isset($user))
                                            <input type="checkbox" name="active_status" class="checkbox_inp" {{ old('active_status', $user->active_status) == '1' ? 'checked' : '' }}>
                                        @else
                                            <input type="checkbox" value="1" name="active_status" class="checkbox_inp" checked>
                                        @endif
                                            <span class="checkbox_mark" ></span>

                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        @if(isset($user))
                                        <button type="submit" class="main-button d1 a1-bg w-50">Update Staff</button>
                                            @else
                                            <button type="submit" class="main-button d1 a1-bg w-50">Add Staff</button>
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

@endsection
