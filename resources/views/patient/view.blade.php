@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="pb-4">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-6">
                    <h3 class="headingH3">Patient Details - {{ $user->name }}</h3>
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

                    <div class="row">
                        <div class="col-lg-4">
                        <div class="form-card grad-bg">
                            <h3 class="d1 headingH3">{{ $user->name }}</h3>
                            <p class="pb-3 d1">{{ $user->gender }}</p>
                            <table class="table-details" style="width:100%">
                                <tr>
                                    <td>Phone</td>
                                    <td class="fw-bold">{{ $user->contact_number }}</td>
                                </tr>
                                <tr>
                                    <td>Patient Reg No.</td>
                                    <td class="fw-bold">{{ $user->reg_no }}</td>
                                </tr>
                                <tr>
                                    <td>Patient DOB</td>
                                    <td class="fw-bold">{{ $user->DOB }}</td>
                                </tr>
                                <tr>
                                    <td>Patient Age</td>
                                    <td class="fw-bold">{{ calculateAgeinmonths($user->DOB) }} - {{ calculateAgeRange(calculateAge($user->DOB)) }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td class="fw-bold">{{ $user->address }}</td>
                                </tr>
                                <tr>
                                    <td>Credit Limit</td>
                                    <td class="fw-bold">{{ number_format($user->credit_limit?? 0 ,2) }}</td>
                                </tr>
                                <tr>
                                    <td>Credit Due</td>
                                    <td class="fw-bold">{{ $user->credit_due?? 0 }}</td>
                                </tr>
                            </table>
                        </div>

                            <div class="form-card mt-4">
                                <div class="d-block">
                                    <div class="pb-3 pt-3">
                                        <a href="{{ route('patients.edit',$user->id) }}"><button type="submit" class="main-button d1 a1-bg w-100">Edit Patient</button></a>
                                    </div>
                                    <div class="">
                                    <form action="{{ route('patients.destroy',$user->id) }}" method="post">
						            @method('delete')
						            @csrf
                                    <button type="submit" class="main-button delete-color d1 w-100" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-card">

                                <h3 class="headingH3 pb-3">Bill History</h3>

                                <div class="search-container pb-3">
                                    <input type="text" id="search" class="form-search" placeholder="Type to Search"/>
						            <i class="fa-solid fa-magnifying-glass f-20"></i>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pb-3">
                                    <div>
                                        <h4 class="headingH5">Total Due: {{ number_format($total_dues, 2) }}</h4>
                                    </div>
                                    <a data-bs-toggle="modal" data-bs-target="#repayModal" data-due="{{ $total_dues }}" class="view-btn pay">Pay</a>
                                </div>
                                <table id="searchtbl" class="table-details" style="width:100%">
                                    <thead class="table-header d-none">
                                        <tr>
                                            <th>#</th>
                                            <th>Total Amount</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($bills as $bill)
                                         <tr>
                                            <td><a href="{{ route('reports.sales.invoice',$bill->id) }}" target="_blank">{{ $bill->invoice_no }}</a><br /><span class="f-14">{{ $bill->created_at->format('Y-m-d h:i A') }}</span></td>
                                            <td>{{ number_format($bill->total_invoice ,2) }}</td>
                                            @if($bill->due_amount != 0)
                                            <td class="text-end">Due {{ number_format($bill->due_amount ,2) }}<!-- <br /><a  data-bs-toggle="modal" data-bs-target="#repayModal" data-invoice="{{ $bill->invoice_no }}" data-due="{{ number_format($bill->due_amount, 2) }}" class="view-btn pay">Pay</a> --></td>
                                            @else
                                            <td class="text-end"><p class="rounded a1-bg d1 text-center">Paid</p></td>
                                            @endif
                                        </tr>
                                     @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">

                            <div class="form-card mb-4">
                                <div class="d-block">
                                    <div class="pt-3">
                                        <a href="{{ route('appointments.create', ['patient_id' => $user->id]) }}"><div class="dash-btn2 greenish"><h5 class="headingH5 d4">New Appointment</h5></div></a>
                                    </div>
                                    <div class="">
                                        <a href="{{ route('pharmacy-bill.create', ['patient_id' => $user->id]) }}"><div class="dash-btn2 greenish"><h5 class="headingH5 d4">New Pharmacy Billing</h5></div></a>
                                    </div>
                                    <div class="">
                                        <a href="{{ route('service-bill.create', ['patient_id' => $user->id]) }}"><div class="dash-btn2 greenish"><h5 class="headingH5 d4">New Service Billing</h5></div></a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-card o-scroll">
                                <h3 class="headingH3 pb-3">Check ups</h3>
                                <table class="table-details" style="width:100%">

                                    <tbody>
                                    @if(isset($appointment))
                                        <tr class="active">
                                            <td>DR {{ $appointment->doctor->name }}<br /><span class="f-14">{{ $appointment->status }}</span></td>
                                            <td>{{\Carbon\Carbon::parse($appointment->time)->format('Y-m-d h:i A') }}</td>
                                       </tr>
                                       @endif
                                    </tbody>
                                </table>
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
                    <h4 class="headingH3 p-b-24">Delete Patient</h4>
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

    <!-- add Loan Pay box -->
    <div class="modal fade" id="repayModal" tabindex="-1" aria-labelledby="repayLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="headingH3 text-center p-b-24">Repay</h4>
                    <form action="{{ route('repayments.payAll') }}" method="post">
                        @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Balance Amount</p>
                                        <input type="text"
                                            class="form-control form-text {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="company_name" id="balance_amount" value="{{ $total_dues }}" readonly>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3">
                                        <p class="form-label">Amount Paid</p>
                                        <input type="text" placeholder="Enter paid amount"
                                            class="form-control form-text border-green {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                                            name="paid_amount" id="paid_amount" value="0" required>
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pb-3 pt-3">
                                        <input type="hidden" name="total_due" value="{{ $total_dues }}">
                                        <input type="hidden" name="patient_id" value="{{ $user->id }}">
                                        <button type="submit" class="main-button d1 a1-bg">Pay</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var repayModal = document.getElementById('repayModal');
        repayModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var invoiceNumber = button.getAttribute('data-invoice');
            var dueAmount = button.getAttribute('data-due');
        });

        var balanceAmountInput = document.getElementById('balance_amount');
        var paidAmountInput = document.getElementById('paid_amount');

        paidAmountInput.addEventListener('input', function () {
            var balanceAmount = parseFloat(balanceAmountInput.value);
            var paidAmount = parseFloat(paidAmountInput.value);

            if (paidAmount > balanceAmount) {
                paidAmountInput.value = balanceAmount.toFixed(2);
            }
        });
    });
</script>


@php
function calculateAge($dob) {
    $dob = new DateTime($dob);
    $now = new DateTime();
    $age = $dob->diff($now)->y;
    return $age;
}

function calculateAgeRange($age) {
    if ($age >= 0 && $age <= 2) {
        return "Baby";
    } elseif ($age >= 3 && $age <= 9) {
        return "Child";
    } elseif ($age >= 10 && $age <= 12) {
        return "Pre Teen";
    } elseif ($age >= 13 && $age <= 17) {
        return "Teenager";
    } elseif ($age >= 18 && $age <= 24) {
        return "Young Adult";
    } elseif ($age >= 25 && $age <= 60) {
        return "Adult";
    } else {
        return "Elder";
    }
}

function calculateAgeinmonths($dob) {
    $dob = new DateTime($dob);
    $now = new DateTime();
    $age = $dob->diff($now);
    $years = $age->y;
    $months = $age->m;
    return "$years y $months m";
}

@endphp
