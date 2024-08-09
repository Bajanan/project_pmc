@extends('layouts.header')

@section('content')
    <section id="wrapper">

        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="p-b-100">

			<div class="row py-3 d1-bg ps-5 align-items-center">
                <div class="col-lg-2">
                    <h3 class="headingH3 text-uppercase"><?php echo date('l'); ?>&nbsp;&nbsp;<?php echo date('d.m.Y'); ?></h3>
                </div>
				<div class="col-lg-10 d-flex justify-content-end pe-5">
					@include('layouts.profile')
                </div>
			</div>

 <!-- Page content -->

            <div class="px-5 pt-4">
                <div class="row pb-3">
                    <div class="col-lg-4">
                        <a href="/patients"><div class="dash-btn2 greenish">
                            <h5 class="headingH5 d4">Patients</h5>
                        </div></a>
                    </div>
                    <div class="col-lg-4">
                        <a href="/pharmacy-billing/create"><div class="dash-btn2 greenish">
                            <h5 class="headingH5 d4">Pharmacy Bill</h5>
                        </div></a>
                    </div>
                    <div class="col-lg-4">
                        <a href="{{ route('reports.create-stocks') }}"><div class="dash-btn2 greenish">
                            <h5 class="headingH5 d4">Stock Balance</h5>
                        </div></a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="dash-btn2 greenn d-flex justify-content-between">
                                    <div>
                                        <h4>Sales</h4>
                                        <p>Today Sales Total</p>
                                    </div>
                                    <h3>{{ number_format($total_sales, 2) }}</h3>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <a href="/bills">
                                <div class="dash-btn2 greenn d-flex justify-content-between">
                                    <div>
                                        <h4 class="d4">Pending Bills</h4>
                                        <p class="d4">Today Pending Bills</p>
                                    </div>
                                    <h3 class="d4">{{ $pendingInvoicesCount ?? 0 }}</h3>
                                </div>
                                </a>
                            </div>
                            <div class="col-lg-4">
                            <a href="/appointment">
                                <div class="dash-btn2 greenn d-flex justify-content-between">
                                    <div>
                                        <h4 class="d4">Appointments</h4>
                                        <p class="d4">Today Appointments Total</p>
                                    </div>
                                    <h3 class="d4">{{ $countAppointmentsToday ?? 0 }}</h3>
                                </div>
                            </a>
                            </div>
                           <!--  <div class="col-lg-12">
                                <div class="dash-btn2 greenn d-flex justify-content-between">
                                    <div>
                                        <h4>Expired</h4>
                                        <p>Today Expired Stocks</p>
                                    </div>
                                    <h3>{{ $expiredProductsCount ?? 0}}</h3>
                                </div>
                            </div> -->

                        </div>
                    </div>

                </div>

            </div>

            <!-- Page content end -->
        </div>
    </section>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
