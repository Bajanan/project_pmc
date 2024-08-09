@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3">Products</h3>
                </div>
				<div class="col-lg-4">
                    <div class="search-container">
						<input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						<i class="fa-solid fa-magnifying-glass f-20"></i>
                	</div>
				</div>
				<div class="col-lg-6 d-flex justify-content-end pe-5">
					<a href="{{ route('products.create') }}"><button type="button" class="main-button a1-bg d1"><i class="fa-solid fa-circle-plus"></i><span class="ms-2">Add Product</span></button></a>
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
                    <a href="{{ route('generic-names.create') }}"><button class="dash-btn2">Generic Names</button></a>
                </div>
                <div class="col-lg-3">
                    <a href="{{ route('pack-sizes.create') }}"><button class="dash-btn2">Packsizes</button></a>
                </div>
            </div>

			<table id="searchtbl" class="table" style="width:100%">
				<thead class="table-header">
					<tr>
                        <th>#</th>
                        <th>Category</th>
						<th>Generic Name</th>
                        <th>Brand Name</th>
						<th>Product Name</th>
						<th class="text-end">Action</th>
					</tr>
				</thead>
				<tbody>
                @foreach ($product as $products)
					<tr>
						<td>{{ $products->barcode }}</td>
						<td>{{ $products->category }}</td>
						<td>{{ $products->generic_name }}</td>
                        <td>{{ $products->brand_name }}</td>
                        <td>{{ $products->product_name }}</td>
						<td class="d-flex justify-content-end">
                            @if( isset( $products->deleted_at))
                            <form action="{{ route('products.restore',$products->id) }}" method="post">
                            @csrf
                            <button type="submit" onclick="return confirm('Are you sure?')"  class="ms-3 no-style"><i class="fa-solid fa-toggle-on active-icon inactive"></i></button>
                            </form>
                            @else
                            <a href="{{ route('products.edit',$products->id) }}" class="ms-3"><i class="fa-solid fa-pen edit-icon"></i></a>
                             <form action="{{ route('products.destroy',$products->id) }}" method="post">
                             @csrf
                            @method('delete')
                            <button type="submit" onclick="return confirm('Are you sure?')"  class="ms-3 no-style"><i class="fa-solid fa-toggle-on active-icon"></i></button>
                            </form>
                            @endif
						</td>
					</tr>
                    @endforeach
                    </tbody>
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
                    <h4 class="headingH3 p-b-24">Delete Product</h4>
                    <p class="p-b-40">Are you sure you want to delete, If you delete the product it will permanently delete this record.</p>
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
