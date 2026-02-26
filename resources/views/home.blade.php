@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card fade-in">
                <div class="card-header">
                    <h4 class="mb-0">Sales Dashboard</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center shadow-sm hover-lift">
                                <p class="text-muted mb-1">Total Sales (This Year)</p>
                                <h3 class="mb-0">${{ number_format($totalSalesYear, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center shadow-sm hover-lift">
                                <p class="text-muted mb-1">Total Sales (This Month)</p>
                                <h3 class="mb-0">${{ number_format($totalSalesMonth, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center shadow-sm hover-lift">
                                <p class="text-muted mb-1">Completed Transactions</p>
                                <h3 class="mb-0">{{ $totalTransactions }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <h5 class="mb-3">Monthly Sales ({{ now()->year }})</h5>
                            <canvas id="monthlySalesChart" height="200"></canvas>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <h5 class="mb-3">Sales by Product</h5>
                            <canvas id="productSalesChart" height="200"></canvas>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Sales by Date Range</h5>
                                <form method="GET" action="{{ route('home') }}" class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">From</span>
                                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">To</span>
                                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter"></i> Apply
                                    </button>
                                </form>
                            </div>
                            <canvas id="rangeSalesChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        const productCtx = document.getElementById('productSalesChart').getContext('2d');
        const rangeCtx = document.getElementById('rangeSalesChart').getContext('2d');

        const monthlyData = @json($monthlySales);
        const productLabels = @json($productLabels);
        const productTotals = @json($productTotals);
        const dailyLabels = @json($dailyLabels);
        const dailyTotals = @json($dailyTotals);

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Sales',
                    data: monthlyData,
                    borderColor: '#000000',
                    backgroundColor: 'rgba(0,0,0,0.05)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(productCtx, {
            type: 'pie',
            data: {
                labels: productLabels,
                datasets: [{
                    data: productTotals,
                    backgroundColor: ['#000000', '#6c757d', '#343a40', '#adb5bd', '#212529', '#495057'],
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        new Chart(rangeCtx, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Sales',
                    data: dailyTotals,
                    backgroundColor: '#000000',
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
