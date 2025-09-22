<!-- Yearly Report Table -->
@if(count($reportData['data']) > 0)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-header">
            <tr>
                <th>Tahun</th>
                <th>Kas Masuk</th>
                <th>Kas Keluar</th>
                <th>Saldo Tahunan</th>
                <th>Jumlah Transaksi</th>
                <th>Rata-rata Bulanan</th>
                <th>Pertumbuhan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['data'] as $index => $yearly)
            <tr>
                <td>
                    <strong>{{ $yearly['year'] }}</strong>
                </td>
                <td class="text-success">
                    <strong>Rp {{ number_format($yearly['income'], 0, ',', '.') }}</strong>
                </td>
                <td class="text-danger">
                    <strong>Rp {{ number_format($yearly['expense'], 0, ',', '.') }}</strong>
                </td>
                <td class="{{ $yearly['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <strong>Rp {{ number_format($yearly['balance'], 0, ',', '.') }}</strong>
                </td>
                <td>
                    <span class="badge bg-primary">{{ $yearly['transaction_count'] }} transaksi</span>
                </td>
                <td>
                    <div class="text-muted">
                        <small>Masuk: Rp {{ number_format($yearly['income'] / 12, 0, ',', '.') }}</small><br>
                        <small>Keluar: Rp {{ number_format($yearly['expense'] / 12, 0, ',', '.') }}</small>
                    </div>
                </td>
                <td>
                    @if($index < count($reportData['data']) - 1)
                        @php
                            $prevYear = $reportData['data'][$index + 1];
                            $growth = $prevYear['balance'] != 0 ? (($yearly['balance'] - $prevYear['balance']) / abs($prevYear['balance'])) * 100 : 0;
                        @endphp
                        @if($growth > 0)
                            <span class="badge bg-success">
                                <i class="fas fa-arrow-up"></i> {{ number_format($growth, 1) }}%
                            </span>
                        @elseif($growth < 0)
                            <span class="badge bg-danger">
                                <i class="fas fa-arrow-down"></i> {{ number_format(abs($growth), 1) }}%
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-minus"></i> 0%
                            </span>
                        @endif
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="table-footer">
            <tr>
                <th>Total</th>
                <th class="text-success">Rp {{ number_format($reportData['summary']['total_income'], 0, ',', '.') }}</th>
                <th class="text-danger">Rp {{ number_format($reportData['summary']['total_expense'], 0, ',', '.') }}</th>
                <th class="{{ $reportData['summary']['total_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($reportData['summary']['total_balance'], 0, ',', '.') }}
                </th>
                <th>{{ $reportData['summary']['total_transactions'] }} transaksi</th>
                <th>
                    @php
                        $yearCount = count($reportData['data']);
                        $avgIncome = $yearCount > 0 ? $reportData['summary']['total_income'] / $yearCount / 12 : 0;
                        $avgExpense = $yearCount > 0 ? $reportData['summary']['total_expense'] / $yearCount / 12 : 0;
                    @endphp
                    <div class="text-muted">
                        <small>Masuk: Rp {{ number_format($avgIncome, 0, ',', '.') }}</small><br>
                        <small>Keluar: Rp {{ number_format($avgExpense, 0, ',', '.') }}</small>
                    </div>
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Chart Section for Yearly Report -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="chart-section">
            <h6><i class="fas fa-chart-bar"></i> Perbandingan Tahunan</h6>
            <canvas id="yearlyBarChart" height="150"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-section">
            <h6><i class="fas fa-chart-pie"></i> Distribusi Total</h6>
            <canvas id="yearlyPieChart" height="150"></canvas>
        </div>
    </div>
</div>

@else
<div class="empty-state">
    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">Tidak Ada Data</h5>
    <p class="text-muted">Tidak ada transaksi ditemukan untuk periode yang dipilih.</p>
</div>
@endif

<style>
    .chart-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
    }

    .chart-section h6 {
        margin-bottom: 15px;
        color: var(--pln-blue);
        font-weight: 600;
    }

    .table-header th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }

    .table-footer th {
        background-color: #e9ecef;
        border-top: 2px solid #dee2e6;
        font-weight: 700;
        color: #333;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        display: block;
        margin-bottom: 20px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize charts if we have data
        @if(count($reportData['data']) > 0)
        initializeYearlyCharts();
        @endif
    });

    function initializeYearlyCharts() {
        const yearlyData = @json($reportData['data']);
        
        if (yearlyData.length === 0) return;

        // Bar Chart
        const barCtx = document.getElementById('yearlyBarChart');
        if (barCtx) {
            const labels = yearlyData.map(item => item.year).reverse();
            const incomeData = yearlyData.map(item => item.income).reverse();
            const expenseData = yearlyData.map(item => item.expense).reverse();

            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Kas Masuk',
                        data: incomeData,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: '#28a745',
                        borderWidth: 1
                    }, {
                        label: 'Kas Keluar',
                        data: expenseData,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: '#dc3545',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' +
                                        new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Pie Chart - Total Income vs Expense
        const pieCtx = document.getElementById('yearlyPieChart');
        if (pieCtx) {
            const totalIncome = @json($reportData['summary']['total_income']);
            const totalExpense = @json($reportData['summary']['total_expense']);

            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Total Kas Masuk', 'Total Kas Keluar'],
                    datasets: [{
                        data: [totalIncome, totalExpense],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            '#28a745',
                            '#dc3545'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    }
</script>