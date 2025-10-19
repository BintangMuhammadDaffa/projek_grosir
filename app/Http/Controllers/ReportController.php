<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->canAccessReports()) {
            abort(403, 'Unauthorized access');
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->get('status', 'all');

        $transactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->with('user')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $totalSales = $transactions->sum('total_amount');
        $totalProfit = $transactions->sum('total_profit');
        $totalTransactions = $transactions->count();

        // Best selling products
        $bestSellingProducts = Product::withCount(['transactionItems as total_sold' => function ($query) use ($startDate, $endDate) {
            $query->selectRaw('sum(quantity)')
                  ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                  ->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
        }])->having('total_sold', '>', 0)
          ->orderBy('total_sold', 'desc')
          ->limit(10)
          ->get();

        // Unpaid customers
        $unpaidCustomers = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('payment_method', 'unpaid')
            ->selectRaw('customer_name, sum(total_amount) as total_debt')
            ->groupBy('customer_name')
            ->havingRaw('sum(total_amount) > 0')
            ->get();

        return view('reports.index', compact(
            'transactions',
            'totalSales',
            'totalProfit',
            'totalTransactions',
            'bestSellingProducts',
            'unpaidCustomers',
            'startDate',
            'endDate',
            'status'
        ));
    }
}
