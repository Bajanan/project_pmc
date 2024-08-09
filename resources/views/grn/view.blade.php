@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-12">
                    <h3 class="headingH3">View GRN</h3>
                </div>
			</div>

 <!-- Page content -->

            <div class="px-4 pt-4">

            <div class="form-card" id="printTable">
			<table class="table-bordered mt-4" style="width:100%">
				<thead>
                    <tr>
                        <td>#</td>
                        <td class="fw-bold">{{ $grn->GRN_No }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td class="fw-bold">{{ $grn->date }}</td>
                    </tr>
                    <tr>
                        <td>Invoice No</td>
                        <td class="fw-bold">{{ $grn->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td>Supplier</td>
                        <td class="fw-bold">{{ $grn->supplier->name }}</td>
                    </tr>
                    {{-- <tr>
                        <td>Reason</td>
                        <td class="fw-bold">Adjustment</td>
                    </tr>
                    <tr>
                        <td>Notes</td>
                        <td class="fw-bold"></td>
                    </tr> --}}
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
					<tr class="text-start">
                        <th>Product</th>
						<th>Batch</th>
                        <th>Expiry</th>
						<th>Pack Qty</th>
                        <th>FOC</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Unit Retail</th>
                        <th class="text-end">Total Cost</th>
					</tr>
				</thead>
				<tbody>
                    @foreach($grn->stock as $product)

					<tr class="">
						<td>{{ $product->batch->Product->product_name }}</td>
						<td>{{ $product->batch->batch_name }}</td>
                        <td>{{ $product->batch->expire_date }}</td>
                        <td>{{ $product->pack_qty }}</td>
                        <td>{{ $product->free_qty }}</td>
                        <td class="text-end">{{ number_format($product->batch->total_cost_price, 2) }}</td>
                        <td class="text-end">{{ number_format($product->batch->total_retail_price, 2) }}</td>
                        <td class="text-end">{{ number_format($product->total_grn_cost, 2) }}</td>
					</tr>
                   @endforeach
                </tbody>
                <tfoot class="fw-bold">
                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td>Total Cost</td>
                        <td>{{number_format($grn->total_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td>User Added</td>
                        <td>{{ $grn->getStaffMember()}}</td>
                    </tr>
                </tfoot>
			</table>
            </div>
            <div class="mt-4">
                <button type="button" class="main-button d1 a1-bg" onclick="printDiv('printTable','Title')"><i class="fa fa-print me-2"></i>Print</button>
            </div>
        </div>

            <!-- Page content end -->
        </div>
    </section>

@endsection

<script>
    function printDiv(printTable, title) {

    let mywindow = window.open('', 'PRINT', 'height=650,width=900,top=100,left=150');

    mywindow.document.write(`<html><head><title>${title}</title>`);
    mywindow.document.write('</head><body >');
    mywindow.document.write(document.getElementById(printTable).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/
    mywindow.print();
    mywindow.close();

    return true;
    }
</script>
