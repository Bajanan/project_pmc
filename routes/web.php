<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\BrandNamesController;
use App\Http\Controllers\CompanyReturnsController;
use App\Http\Controllers\DoctorsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BillsController;
use App\Http\Controllers\GenericNamesController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\PackSizesController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\PharmacyBillsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RepaymentsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ServiceBillsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\StaffsController;
use App\Http\Controllers\StockAdjustmentsController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//all the routes that can access when the user is logged-in
Route::middleware('auth')->group(function () {
    // Routes that require authentication

    Route::resource('staffs', StaffsController::class);
    Route::resource('doctors', DoctorsController::class);
    Route::resource('repayments', RepaymentsController::class);
    Route::resource('patients', PatientsController::class);

    //pack-sizes
    Route::resource('pack-sizes', PackSizesController::class);
    Route::post('pack-sizes/{id}', [ PackSizesController::class, 'restore'])->name('pack-sizes.restore');

    //services
    Route::resource('services', ServicesController::class);
    Route::post('services-restore/{id}', [ ServicesController::class, 'restore'])->name('services.restore');

    //generic names
    Route::resource('generic-names', GenericNamesController::class);
    Route::post('generic-names/{id}', [ GenericNamesController::class, 'restore'])->name('generic-names.restore');

    //stockAdjustments
    Route::resource('stock-adjustments', StockAdjustmentsController::class);
    Route::post('stock-adjustments/filter-batches',[ StockAdjustmentsController::class, 'filterBatches']);
    Route::post('stock-adjustments/batch-details',[ StockAdjustmentsController::class, 'batchDetails']);

    //CRN
    Route::resource('company-return', CompanyReturnsController::class);
    Route::post('crn/filter-products',[CompanyReturnsController::class, 'filterProducts']);
    Route::post('crn/filter-batches',[CompanyReturnsController::class, 'filterBatches']);
    Route::post('crn/batch-details',[CompanyReturnsController::class, 'batchDetails']);

    //GRN
    Route::resource('grn', GRNController::class);
    Route::post('grn/filter-products',[GRNController::class, 'filterProducts']);
    Route::get('grn/getPackSize/{id}', [GRNController::class, 'getPackSize'])->name('grn.getPackSize');
    Route::post('grn/product-units',[GRNController::class, 'productUnits']);
    Route::post('grn/filter-batches',[ GRNController::class, 'filterBatches']);
    Route::post('grn/batch-details',[GRNController::class, 'batchDetails']);

    //products
    Route::resource('products', ProductsController::class);
    Route::post('products/filter-brands', [ProductsController::class, 'filterBrandNames']);
    Route::post('products/sellingUnit', [ProductsController::class, 'sellingUnit']);
    Route::post('products/restore/{id}', [ ProductsController::class, 'restore'])->name('products.restore');

    //appointments
    Route::view('appointment', 'appointment.index');
    Route::get('appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
    Route::post('appointments/store', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::post('appointments/availability', [AppointmentsController::class, 'checkAvailability'])->name('appointment.availability');
    Route::post('appointments/reschedule', [AppointmentsController::class, 'reschedule'])->name('appointment.reschedule');
    Route::post('appointments/add-patient', [AppointmentsController::class, 'newPatient'])->name('appointment.newPatient');
    Route::put('appointment-status/{id}', [AppointmentsController::class, 'update'])->name('appointment.update');
    Route::post('appointments/cancel-schedule', [AppointmentsController::class, 'cancelSchedule'])->name('appointment.cancel');

    //batch add
   Route::post('batch/add', [GRNController::class, 'batchNameStore']);

    //pharmacy-billing

    Route::get('pharmacy-billing/create',[PharmacyBillsController::class,'create'])->name('pharmacy-bill.create');
    Route::post('pharmacy-billing/filter-batch',[PharmacyBillsController::class,'filterBatches']);
    Route::post('pharmacy-billing/batch-details',[PharmacyBillsController::class,'batchDetails']);
    Route::post('pharmacy-billing/store',[PharmacyBillsController::class,'store'])->name('pharmacy-bill.store');
    Route::post('pharmacy-billing/patient-dues', [PharmacyBillsController::class,'billDueRecords']);
    Route::post('/pharmacy-billing/filter-product-by-barcode', 'PharmacyBillsController@filterProductByBarcode')->name('pharmacy-bill.filter-product-by-barcode');
    Route::post('get-creditlimit',[PharmacyBillsController::class,'getCreditLimit']);

     //service-billing
     Route::get('service-billing/create',[ServiceBillsController::class,'create'])->name('service-bill.create');
     Route::post('service-billing/service-price',[ServiceBillsController::class,'servicePrice']);
     Route::post('service-billing/filter-batch',[ServiceBillsController::class,'filterBatches']);
     Route::post('service-billing/batch-details',[ServiceBillsController::class,'batchDetails']);
     Route::post('service-billing/store',[ServiceBillsController::class,'store'])->name('service-bill.store');
     Route::post('service-billing/patient-dues', [ServiceBillsController::class,'billDueRecords']);
     Route::post('get-appointments',[ServiceBillsController::class,'getAppointments']);
     Route::post('get-creditlimit',[ServiceBillsController::class,'getCreditLimit']);

     //Repayment
     Route::post('repayments/pay-all' , [RepaymentsController::class,'payAll'])->name('repayments.payAll');

     //reports
     Route::post('report/appointment' , [ReportsController::class,'appointments'])->name('reports.appointments');
     Route::get('report/create-appointment' , [ReportsController::class,'createAppointments'])->name('reports.create-appointment');
     Route::get('report/accounts' , [ReportsController::class,'combinedReport'])->name('reports.accounts');
     Route::get('report/sales' , [ReportsController::class,'sales'])->name('reports.sales');
     Route::get('report/sales/invoice/{id}' , [ReportsController::class,'salesInvoice'])->name('reports.sales.invoice');
     Route::post('report/item-sales' , [ReportsController::class,'itemSales'])->name('reports.item-sales');
     Route::get('report/dues' , [ReportsController::class,'dues'])->name('reports.dues');
     Route::get('report/repayments' , [ReportsController::class,'repayments'])->name('reports.repayments');
     Route::get('report/create-stocks' , [ReportsController::class,'create_stock'])->name('reports.create-stocks');
     Route::post('report/stocks' , [ReportsController::class,'stocks'])->name('reports.stocks');
     Route::post('report/stocks-expiry' , [ReportsController::class,'stocks'])->name('reports.stocks-expiry');
     Route::get('report/create-stock-moving' , [ReportsController::class,'createStockMoving'])->name('reports.create-stock-moving');
     Route::post('report/filter-batch' , [ReportsController::class,'filterBatch']);
     Route::post('report/stocks-moving' , [ReportsController::class,'stockMoving'])->name('reports.stocks-moving');
     Route::get('report/create-purchase-order' , [ReportsController::class,'createPurchaseOrder'])->name('reports.create-purchase-order');
     Route::post('report/purchase-order' , [ReportsController::class,'purchaseOrder'])->name('reports.purchase-order');
     Route::post('report/filter-brands' , [ReportsController::class,'filterBrands']);
     Route::post('/generate-pdf', [ReportsController::class, 'generatePDF'])->name('generate.pdf');

//dashboard
Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');
Route::get('/bills', 'App\Http\Controllers\BillsController@index')->name('bills');
Route::get('/bills/invoices/{invoice}', [BillsController::class, 'getInvoiceDetails'])->name('invoices.details');
Route::post('/bills/cancel-invoice/{invoiceNumber}', [BillsController::class, 'cancelInvoice']);
Route::post('/bills/checkout', [BillsController::class, 'checkout'])->name('checkout');
Route::post('/bills/printinvoice', [BillsController::class, 'printinvoice'])->name('printinvoice');
Route::get('/bills/services/{id}', [BillsController::class, 'show']);
Route::post('/bills/cancel', [BillsController::class, 'cancel_invoice'])->name('invoices.cancel');
Route::post('/bills/refund', [BillsController::class, 'refundService'])->name('invoices.refund');


//reports
Route::view('/appointment-report', 'reports.appointment');
Route::view('/sales-report', 'reports.sales');
Route::view('/accounts', 'reports.accounts');
Route::view('/itemsales-report', 'reports.itemsale');
Route::view('/due-report', 'reports.due');
Route::view('/stock-report', 'reports.stock');
Route::view('/moving-report', 'reports.moving');
Route::view('/purchase-order', 'reports.purchase-order');


Route::get('appointments/events',[AppointmentsController::class,'events'])->name('appointments.events');

});

Route::middleware(['auth', 'role:Admin'])->group(function () {
     //brand Names and suppliers
     Route::resource('brand-names', BrandNamesController::class);
     Route::post('brand-names/{id}', [ BrandNamesController::class, 'restore'])->name('brand-names.restore');
     Route::resource('suppliers', SuppliersController::class);
     Route::resource('clinic', ClinicController::class);

});

Route::view('/','auth.login');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

