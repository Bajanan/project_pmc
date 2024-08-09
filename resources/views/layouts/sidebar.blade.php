<!-- Sidebar -->

<div id="sidebar-wrapper">
    <a id="menu-toggle" href="#" class="sidebar-arrow"><i class="fa fa-chevron-left"></i></a>

    <div class="mb-5 pb-3 d-flex align-items-center ms-4 d-block">
        <img src="{{ asset($clinic->clinic_logo) }}" width="55px" class="sidebar-logo me-3" alt="PMC">
        <span class="headingH2 c1 c-name">Ganesa<span class="f-14 d2">Meds</span></span>
    </div>

    <ul class="sidebar-nav" id="sidebar">
        <!-- Dash -->
        <li class="p-b-40 main-link">
            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'c1' : 'd1' }} d-flex align-items-center">
                <span class="material-symbols-outlined">
                    dashboard
                </span>
                <span class="hidetext">Dashboard</span>
            </a>
        </li>

        <!-- Activities -->
        <li class="p-b-40 main-link">
            <a href="/appointment" class="{{ request()->is('appointment') ? 'c1' : 'd1' }} d-flex align-items-center">
                <span class="material-symbols-outlined">
                    calendar_month
                </span>
                <span class="hidetext">Appointments</span>
            </a>
        </li>

        <li class="p-b-40 main-link">
            <a href="{{ route('pharmacy-bill.create') }}" class="{{ request()->is('pharmacy-bill') ? 'c1' : 'd1' }} d-flex align-items-center">
                <span class="material-symbols-outlined">
                    vaccines
                </span>
                <span class="hidetext">Pharmacy Billing</span>
            </a>
        </li>

        <li class="p-b-40 main-link">
            <a href="{{ route('service-bill.create') }}" class="{{ request()->is('service-bill') ? 'c1' : 'd1' }} d-flex align-items-center">
                <span class="material-symbols-outlined">
                    receipt
                </span>
                <span class="hidetext">Service Billing</span>
            </a>
        </li>

        <!-- USER -->
        @if(Auth::check() && Auth::user()->user_role === 'Staff')
            <li class="p-b-40 main-link">
                <a href="/patients" class="{{ request()->is('patients') ? 'c1' : 'd1' }} d-flex align-items-center">
                    <span class="material-symbols-outlined">
                        group
                    </span>
                    <span class="hidetext">Patients</span>
                </a>
            </li>
        @endif
        @if(Auth::check() && Auth::user()->user_role === 'Admin')
        <li class="p-b-40 main-link">
            <a data-bs-toggle="collapse" href="#collapse2" role="button" aria-expanded="true"
                aria-controls="collapseExample" class="d1 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                <span class="material-symbols-outlined">
                    group
                </span>
                <span>Manage Users</span>
                </div>

            <i class="fa py-2 fa-plus d1"></i>
            </a>
        </li>

        <div class="collapse sidebar-sub " id="collapse2">
            <a href="/patients" class="{{ request()->is('patients') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Patients</li>
            </a>

            <a href="/staffs" class="{{ request()->is('staffs') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Staffs</li>
            </a>
            <a href="/doctors" class="{{ request()->is('doctors') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Doctors</li>
            </a>
        </div>
        @endif

        <!-- Data -->
        @if(Auth::check() && Auth::user()->user_role !== 'Staff')
        <li class="p-b-40 main-link">
            <a data-bs-toggle="collapse" href="#collapse3" role="button" aria-expanded="true"
                aria-controls="collapseExample" class="d1 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                <span class="material-symbols-outlined">
                    database
                </span>
                <span>Manage Data</span>
                </div>

            <i class="fa py-2 fa-plus d1"></i>
            </a>
        </li>

        <div class="collapse sidebar-sub " id="collapse3">

            <a href="{{ route('services.create') }}" class="{{ request()->is('services') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Services</li>
            </a>

            <a href="{{ route('products.index') }}" class="{{ request()->is('pharmacy') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Inventory</li>
            </a>

            <a href="/suppliers" class="{{ request()->is('suppliers') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Suppliers</li>
            </a>

            <a href="{{ route('grn.index') }}" class="{{ request()->is('grn') ? 'c1' : 'd1' }}">
                <li class="p-b-40">GRN</li>
            </a>
            <a href="{{ route('company-return.index') }}" class="{{ request()->is('company-return') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Company Return</li>
            </a>
            @if(Auth::check() && Auth::user()->user_role === 'Admin')
            <a href="{{ route('stock-adjustments.index') }}" class="{{ request()->is('stock-adjustment') ? 'c1' : 'd1' }}">
                <li class="p-b-40">Stock Adjustment</li>
            </a>
            @endif

        </div>

        @endif

         <!-- Reports -->
         <li class="p-b-40 main-link">
            <a data-bs-toggle="collapse" href="#collapse4" role="button" aria-expanded="true"
                aria-controls="collapseExample" class="d1 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                <span class="material-symbols-outlined">
                    lab_profile
                </span>
                <span>Reports</span>
                </div>

            <i class="fa py-2 fa-plus d1"></i>
            </a>
        </li>

        <div class="collapse sidebar-sub " id="collapse4">
            <a href="{{ route('reports.create-appointment') }}" class="d1">
                <li class="p-b-40">Appointments</li>
            </a>
            @if(Auth::check() && Auth::user()->user_role !== 'Staff')
            <a href="{{ route('reports.sales') }}" class="d1">
                <li class="p-b-40">Sales</li>
            </a>
            <a href="{{ route('reports.accounts') }}" class="d1">
                <li class="p-b-40">Accounts</li>
            </a>
            <a href="/itemsales-report" class="d1">
                <li class="p-b-40">Item Sales</li>
            </a>
            @endif
            <a href="{{ route('reports.dues') }}" class="d1">
                <li class="p-b-40">Payment Due</li>
            </a>
            <a href="{{ route('reports.create-stocks') }}" class="d1">
                <li class="p-b-40">Stock Balance</li>
            </a>
            <a href="{{ route('reports.create-stock-moving') }}" class="d1">
                <li class="p-b-40">Stock Moving</li>
            </a>
            <a href="{{ route('reports.create-purchase-order') }}" class="d1">
                <li class="p-b-40">Purchase Order</li>
            </a>
        </div>

        <!-- Dash -->
        @if(Auth::check() && Auth::user()->user_role === 'Admin')
        <li class="p-b-40 main-link">
            <a href="/clinic" class="{{ request()->is('clinic') ? 'c1' : 'd1' }} d-flex align-items-center">
                <span class="material-symbols-outlined">
                    settings
                </span>
                <span>Setting</span>
            </a>
        </li>
        @endif
    </ul>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        var collapseLinks = document.querySelectorAll('[data-bs-toggle="collapse"]');

        collapseLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                var target = document.querySelector(link.getAttribute('href'));
                var allCollapses = document.querySelectorAll('.collapse.show');

                allCollapses.forEach(function (collapse) {
                    if (collapse !== target) {
                        collapse.classList.remove('show');
                    }
                });
            });
        });
    });
</script>
