<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\OperationalCost;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->canAccessProfit()) {
            abort(403, 'Unauthorized access');
        }

        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $transactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->get();
        $operationalCosts = OperationalCost::whereBetween('cost_date', [$startDate, $endDate])->get();

        $paidTransactions = $transactions->where('payment_method', '!=', 'unpaid');
        $unpaidTransactions = $transactions->where('payment_method', 'unpaid');

        $totalSales = $transactions->sum('total_amount');
        $totalProfit = $transactions->sum('total_profit');
        $paidProfit = $paidTransactions->sum('total_profit');
        $unpaidProfit = $unpaidTransactions->sum('total_profit');
        $totalCosts = $operationalCosts->sum('amount');
        $netProfit = $totalProfit - $totalCosts;

        // Daily breakdown
        $dailyData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dayTransactions = $transactions->where('transaction_date', '>=', $currentDate->startOfDay())
                                          ->where('transaction_date', '<=', $currentDate->endOfDay());

            $dayCosts = $operationalCosts->where('cost_date', '>=', $currentDate->startOfDay())
                                         ->where('cost_date', '<=', $currentDate->endOfDay());

            $dailyData[] = [
                'date' => $currentDate->format('d/m/Y'),
                'sales' => $dayTransactions->sum('total_amount'),
                'profit' => $dayTransactions->sum('total_profit'),
                'costs' => $dayCosts->sum('amount'),
                'net' => $dayTransactions->sum('total_profit') - $dayCosts->sum('amount')
            ];

            $currentDate->addDay();
        }

        return view('profit.index', compact(
            'totalSales',
            'totalProfit',
            'paidProfit',
            'unpaidProfit',
            'totalCosts',
            'netProfit',
            'dailyData',
            'month'
        ));
    }
}
