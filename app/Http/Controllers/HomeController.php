<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $currentYear = now()->year;

        // Monthly sales for the current year (DB-agnostic: group in PHP)
        $transactionsYear = Transaction::completed()
            ->whereYear('created_at', $currentYear)
            ->get();

        $monthlySalesBuckets = array_fill(1, 12, 0.0);
        foreach ($transactionsYear as $transaction) {
            $month = (int) $transaction->created_at->format('n'); // 1-12
            $monthlySalesBuckets[$month] += (float) $transaction->total_amount;
        }

        $monthlySales = array_values($monthlySalesBuckets);

        // Sales distribution per product (pie chart)
        $productSales = TransactionItem::whereHas('transaction', function ($query) {
                $query->completed();
            })
            ->selectRaw('product_id, SUM(total_price) as total')
            ->groupBy('product_id')
            ->with('product')
            ->get();

        $productLabels = $productSales->map(function ($row) {
            return optional($row->product)->name ?? 'Unknown';
        })->toArray();

        $productTotals = $productSales->pluck('total')->map(fn ($v) => (float) $v)->toArray();

        // Date range for bar chart
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : now()->copy()->subDays(6)->startOfDay();

        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : now()->copy()->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        // Daily sales within range (DB-agnostic: group in PHP)
        $transactionsRange = Transaction::completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        $dailySalesRaw = [];
        foreach ($transactionsRange as $transaction) {
            $dateKey = $transaction->created_at->toDateString();
            if (! isset($dailySalesRaw[$dateKey])) {
                $dailySalesRaw[$dateKey] = 0.0;
            }
            $dailySalesRaw[$dateKey] += (float) $transaction->total_amount;
        }

        $dailyLabels = [];
        $dailyTotals = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $key = $cursor->toDateString();
            $dailyLabels[] = $cursor->format('M d');
            $dailyTotals[] = (float) ($dailySalesRaw[$key] ?? 0);
            $cursor->addDay();
        }

        // Summary metrics
        $totalSalesYear = array_sum($monthlySales);
        $totalTransactions = Transaction::completed()->count();
        $totalSalesMonth = Transaction::completed()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        return view('home', [
            'monthlySales' => $monthlySales,
            'productLabels' => $productLabels,
            'productTotals' => $productTotals,
            'dailyLabels' => $dailyLabels,
            'dailyTotals' => $dailyTotals,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'totalSalesYear' => (float) $totalSalesYear,
            'totalSalesMonth' => (float) $totalSalesMonth,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
