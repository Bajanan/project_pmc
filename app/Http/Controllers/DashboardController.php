<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Appointment;
use App\Models\Batch;
use App\Models\Clinic;
use App\Models\InvoiceService;
use App\Models\Service;
use App\Models\Stock;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        Bill::whereDate('date', $yesterday)
            ->where('status', 'pending')
            ->update(['status' => Bill::CANCELLED]);

        Appointment::whereDate('date', '<=', $thirtyDaysAgo)
            ->where('status', 'Pending')
            ->update(['status' => Appointment::CANCELLED]);

        $invoices = Bill::select('invoice_no', 'id')
            ->whereDate('date', $today)
            ->where('status', 'pending')
            ->distinct()
            ->orderBy('id', 'desc')
            ->get();

        $invoice = Bill::whereDate('date', $today)
            ->where('status', 'paid')
            ->get();
        $total_sales = $invoice->sum('payable_amount');

        $countAppointmentsToday = Appointment::whereDate('date', $today)->count();

        $expiredStocks = DB::table('stocks')
            ->join('batch', 'stocks.batch_id', '=', 'batch.id')
            ->whereDate('batch.expire_date', '=', $today)
            ->select('stocks.*', 'stocks.total_units')
            ->whereRaw('stocks.id = (SELECT MAX(id) FROM stocks AS s WHERE s.batch_id = stocks.batch_id)')
            ->get();

        // Sum up the total_units of the latest stock record for each expired batch
        $totalExpiredUnits = 0;

        foreach ($expiredStocks as $stock) {
            // Add the total_units of the batch associated with the stock
            $totalExpiredUnits += $stock->total_units;
        }

        $pendingInvoicesCount = Bill::where('status', 'pending')->distinct()->count();

        return view('dashboard', [
            'invoices' => $invoices,
            'total_sales' => $total_sales,
            'countAppointmentsToday' => $countAppointmentsToday,
            'expiredProductsCount' => $totalExpiredUnits,
            'pendingInvoicesCount' => $pendingInvoicesCount,
        ]);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

}
