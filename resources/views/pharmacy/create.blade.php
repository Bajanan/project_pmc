@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Product Details</h3>
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
                        @if(isset($product))
                        <form action="{{ route('products.update',$product->id) }}" method="post">
                        @method('put')
                        @else
                        <form action="{{ route('products.store') }}" method="post">
                        @endif
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <div class="row">
                                @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Supplier</p>
                                        <select
                                            class="form-control {{ $errors->has('company_status') ? 'is-invalid' : '' }} form-select"
                                            name="supplier" id="supplier" onchange="filterBrands();" required {{ isset($product->supplier_id) ? 'disabled' : '' }}>
                                            <option value="" selected disabled hidden>Select Supplier</option>
                                            @foreach ($suppliers as $supplier )
                                            <option value="{{ $supplier->id }}"{{ old('supplier',$product->supplier_id?? '')==$supplier->id ? 'selected':'' }} >{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Brand</p>
                                        <select
                                            class="form-control {{ $errors->has('company_status') ? 'is-invalid' : '' }} form-select"
                                            name="brand" id="brand"  required {{ isset($product->supplier_id) ? 'disabled' : '' }}>
                                            <option value="" selected disabled hidden>Select brand</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Category</p>
                                        <select
                                            class="form-control {{ $errors->has('category') ? 'is-invalid' : '' }} form-select"
                                            name="category" id="category" required>
                                            <option value="" selected disabled hidden>Select category</option>
                                            <option value="Medicine" {{ old('category',$product->category?? '')=="Medicine" ? 'selected':'' }}  >Medicine</option>
                                            <option value="Surgical" {{ old('category',$product->category?? '')=="Surgical" ? 'selected':'' }}>Surgical</option>
                                            <option value="Groceries" {{ old('category',$product->category?? '')=="Groceries" ? 'selected':'' }}>Groceries</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Generic Name</p>
                                        <select
                                            class="form-control {{ $errors->has('generic_name') ? 'is-invalid' : '' }} form-select"
                                            name="generic_name" id="generic_name">
                                            <option value="" selected disabled hidden>Select generic name</option>
                                            @foreach ($generic_names as $generic_name )
                                            <option value="{{ $generic_name->generic_name }}" {{ old('generic_name',$product->generic_name ?? '')==$generic_name->generic_name ? 'selected':'' }}>{{ $generic_name->generic_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Product Name</p>
                                        <input type="text" placeholder="Enter product name"
                                            class="form-control form-text border-green {{ $errors->has('product_name') ? 'is-invalid' : '' }}"
                                            name="product_name" value="{{ old('product_name',$product->product_name ?? '')}}" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('product_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Barcode</p>
                                        <input type="text" placeholder=""
                                            class="form-control form-text {{ $errors->has('barcode') ? 'is-invalid' : '' }}"
                                            name="barcode" value="{{ old('barcode',$product->barcode ?? '')}}">
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('barcode') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="pb-3">
                                        <p class="form-label">Pack size</p>
                                        <select
                                            class="form-control {{ $errors->has('pack_size') ? 'is-invalid' : '' }} form-select"
                                            name="pack_size" id="pack_size"  required>
                                            <option value="" selected disabled hidden>Select pack size</option>
                                            @foreach ($pack_sizes as $pack_size )
                                            <option value="{{ $pack_size->id }}" {{ old('pack_size',$product->pack_size_id?? '')==$pack_size->id  ? 'selected':'' }}>{{ $pack_size->pack_size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="pb-4">
                                        <p class="form-label">Product Active</p>
                                        <label class="checkbox_wrap">
                                        @if(isset($product))
                                            <input type="checkbox" name="active_status" class="checkbox_inp" {{ old('active_status', $product->active_status) == '1' ? 'checked' : '' }}>
                                        @else
                                            <input type="checkbox" value="1" name="active_status" class="checkbox_inp" checked>
                                        @endif
                                            <span class="checkbox_mark" ></span>

                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                    @if(isset($product))
                                        <button type="submit" class="main-button d1 a1-bg w-50">Update Product</button>
                                    @else
                                     <button type="submit" class="main-button d1 a1-bg w-50">Add Product</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        $(document).ready(function(){
            var product = @json(@$product);
           var drop_down = "";
           drop_down = "<option value="+product.brand_name+" selected>"+product.brand_name+"</option>";
            $('select[id="brand"]').append(drop_down);
        });

    function filterBrands(){


        var supplier = $('#supplier').val();
        var  token = $('#token').val();


        $.ajax({
				method: 'POST',
				url: '/products/filter-brands', 
				data: {'Supplier':supplier},
				dataType: "json",
				headers: {
					'X-CSRF-TOKEN': token 
				},
				success: function(response) {
                    var data = "";
                    console.log(response.brands);
                     $('select[id="brand"] option').not(':first').remove();

                    response.brands.forEach(brand => {
                                data += "<option value="+brand.brand_name+" >"+brand.brand_name+"</option>";
                    });
                   // console.log(data);
                //  alert(data);
                $('select[id="brand"]').append(data);

				},
				error: function(error) {
					

				}
			});

    }

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var categorySelect = document.getElementById('category');
        var genericNameSelect = document.getElementById('generic_name');

        // Add event listener to category select
        categorySelect.addEventListener('change', function () {
            if (categorySelect.value === 'Medicine') {
                genericNameSelect.setAttribute('required', 'required');
            } else {
                genericNameSelect.removeAttribute('required');
            }
        });

        // Trigger change event on page load
        categorySelect.dispatchEvent(new Event('change'));
    });
</script>
