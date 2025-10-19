<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\OperationalCost;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Only owner can access dashboard
        if (!auth()->user()->canAccessDashboard()) {
            abort(403, 'Unauthorized access');
        }

        $filterType = $request->input('filter_type', 'month'); // default to month
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $yearlySales = 0;
        $yearlyProfit = 0;
        $yearlyCosts = 0;

        if ($filterType == 'month') {
            // Filter by selected month/year
            $startDate = Carbon::create($selectedYear, $selectedMonth)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $lastMonthStart = $startDate->copy()->subMonth()->startOfMonth();
            $lastMonthEnd = $startDate->copy()->subMonth()->endOfMonth();

            // Today's sales (only if selected month is current)
            if ($selectedMonth == $currentMonth && $selectedYear == $currentYear) {
                $todaySales = Transaction::whereDate('transaction_date', $today)->sum('total_amount');
                $todayProfit = Transaction::whereDate('transaction_date', $today)->sum('total_profit');
                $todayTransactions = Transaction::whereDate('transaction_date', $today)->count();
            } else {
                $todaySales = 0;
                $todayProfit = 0;
                $todayTransactions = 0;
            }

            // Monthly sales (selected month)
            $monthlySales = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_amount');
            $monthlyProfit = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_profit');
            $monthlyTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->count();

            // Last month's sales for comparison
            $lastMonthSales = Transaction::whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
            $salesGrowth = $lastMonthSales > 0 ? (($monthlySales - $lastMonthSales) / $lastMonthSales) * 100 : 0;

            // Operational costs for selected month
            $monthlyCosts = OperationalCost::whereBetween('cost_date', [$startDate, $endDate])->sum('amount');

            // Daily sales data for selected month
            $dailySales = Transaction::selectRaw('DATE(transaction_date) as date, SUM(total_amount) as sales')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date')
                ->toArray();

            // Prepare data for chart (fill missing days with 0)
            $daysInMonth = $startDate->daysInMonth;
            $chartLabels = [];
            $chartData = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($selectedYear, $selectedMonth, $day)->format('Y-m-d');
                $chartLabels[] = $day;
                $chartData[] = isset($dailySales[$date]) ? (float) $dailySales[$date]['sales'] : 0;
            }

            $chartTitle = 'Grafik Penjualan Harian - ' . $startDate->locale('id')->isoFormat('MMMM YYYY');

        } elseif ($filterType == 'year') {
            // Filter by selected year
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = $startDate->copy()->endOfYear();

            // Yearly stats
            $yearlySales = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_amount');
            $yearlyProfit = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_profit');
            $yearlyCosts = OperationalCost::whereBetween('cost_date', [$startDate, $endDate])->sum('amount');

            // Monthly sales data for selected year
            $monthlySalesData = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(total_amount) as sales')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month')
                ->toArray();

            // Prepare data for chart (fill missing months with 0)
            $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartData = [];
            for ($month = 1; $month <= 12; $month++) {
                $chartData[] = isset($monthlySalesData[$month]) ? (float) $monthlySalesData[$month]['sales'] : 0;
            }

            $chartTitle = 'Grafik Penjualan Tahun ' . $selectedYear;

            // Set variables for view (reuse monthly vars for year)
            $todaySales = 0;
            $todayProfit = 0;
            $todayTransactions = 0;
            $monthlySales = $yearlySales;
            $monthlyProfit = $yearlyProfit;
            $monthlyTransactions = 0; // not used in year view
            $salesGrowth = 0; // not used
            $monthlyCosts = $yearlyCosts;
        }

        // Low stock products (always same)
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->get();

        // Recent transactions (always same, or filter by date? keep same for now)
        $recentTransactions = Transaction::with('user')
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        // Top-selling products for pie chart
        $totalSales = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_amount');

        if ($totalSales > 0) {
            $topProducts = \DB::table('transactions as t')
                ->join('transaction_items as ti', 't.id', '=', 'ti.transaction_id')
                ->join('products as p', 'ti.product_id', '=', 'p.id')
                ->selectRaw('p.name, SUM(ti.quantity * ti.unit_price) as product_sales')
                ->whereBetween('t.transaction_date', [$startDate, $endDate])
                ->groupBy('p.id', 'p.name')
                ->orderBy('product_sales', 'desc')
                ->limit(5)
                ->get();

            $topProductNames = $topProducts->pluck('name')->toArray();
            $topProductPercentages = $topProducts->map(function ($product) use ($totalSales) {
                return round(($product->product_sales / $totalSales) * 100, 1);
            })->toArray();
        } else {
            $topProductNames = [];
            $topProductPercentages = [];
        }

        // Products for dropdown (for chart filter, if needed)
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('dashboard.index', compact(
            'todaySales',
            'todayProfit',
            'todayTransactions',
            'monthlySales',
            'monthlyProfit',
            'monthlyTransactions',
            'salesGrowth',
            'lowStockProducts',
            'recentTransactions',
            'monthlyCosts',
            'yearlySales',
            'yearlyProfit',
            'yearlyCosts',
            'chartLabels',
            'chartData',
            'chartTitle',
            'currentMonth',
            'currentYear',
            'products',
            'filterType',
            'selectedMonth',
            'selectedYear',
            'topProductNames',
            'topProductPercentages'
        ));
    }

    public function getDailySalesData(Request $request)
    {
        // Only owner can access
        if (!auth()->user()->canAccessDashboard()) {
            abort(403, 'Unauthorized access');
        }

        $productId = $request->input('product_id');
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        if ($productId) {
            // Product-specific: sum quantity * unit_price per day
            $dailySales = \DB::table('transactions as t')
                ->join('transaction_items as ti', 't.id', '=', 'ti.transaction_id')
                ->join('products as p', 'ti.product_id', '=', 'p.id')
                ->selectRaw('DATE(t.transaction_date) as date, SUM(ti.quantity * ti.unit_price) as sales')
                ->where('p.id', $productId)
                ->whereBetween('t.transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date')
                ->toArray();
        } else {
            // All products: sum total_amount per day
            $dailySales = Transaction::selectRaw('DATE(transaction_date) as date, SUM(total_amount) as sales')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date')
                ->toArray();
        }

        // Prepare labels and data, fill missing days with 0
        $chartLabels = [];
        $chartData = [];

        for ($day = 1; $day <= $endDate->daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day)->format('Y-m-d');
            $chartLabels[] = Carbon::create($year, $month, $day)->format('d');
            $chartData[] = isset($dailySales[$date]) ? (float) $dailySales[$date]['sales'] : 0;
        }

        return response()->json([
            'labels' => $chartLabels,
            'data' => $chartData
        ]);
    }
}
