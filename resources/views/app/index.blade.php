@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Clinic Details</h3>
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
                        <form action="{{ route('clinic.update', $clinic->id) }}" enctype="multipart/form-data" method="post">
                        @csrf
                        @method('put')
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Clinic Name</p>
                                        <input type="text" placeholder="Enter clinic name"
                                            class="form-control form-text border-green"
                                            name="clinic_name" value="{{ $clinic->clinic_name }}" >
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Email</p>
                                        <input type="text" placeholder="Enter email"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="email" value="{{ $clinic->email }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Phone</p>
                                        <input type="text" placeholder="Enter phone"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="phone" value="{{ $clinic->phone }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Mobile</p>
                                        <input type="text" placeholder="Enter mobile"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="mobile" value="{{ $clinic->mobile }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Bill Message</p>
                                        <input type="text" placeholder="Enter phone"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="bill_message" value="{{ $clinic->bill_message }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Clinic Address</p>
                                        <input type="text" placeholder="Enter clinic address"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="clinic_address" value="{{ $clinic->clinic_address }}" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Clinic Logo</p>
                                        @if($clinic->clinic_logo)
                                            <img src="{{ asset($clinic->clinic_logo) }}" width="100px" alt="Clinic Logo">
                                        @else
                                            <p>No logo uploaded</p>
                                        @endif
                                        <img id="uploadedImage" src="#" alt="Uploaded Image" accept="image/png, image/jpeg" style="display:none;">
                                        <input type="file" id="readUrl" class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="clinic_logo" value="">
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        <button type="submit" class="main-button d1 a1-bg">Update Clinic Details</button>
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

    <script>
        document.getElementById('readUrl').addEventListener('change', function(){
        if (this.files[0] ) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
            document.getElementById('uploadedImage').setAttribute('src', event.target.result);
            document.getElementById('uploadedImage').style.display = 'block';
            });
        }
        });
    </script>

@endsection


