<!-- Monthly Report Table -->
@if(count($reportData['data']) > 0)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-header">
            <tr>
                <th>Bulan/Tahun</th>
                <th>Kas Masuk</th>
                <th>Kas Keluar</th>
                <th>Saldo Bulanan</th>
                <th>Jumlah Transaksi</th>
                <th>Pertumbuhan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['data'] as $index => $monthly)
            <tr>
                <td>
                    <strong>{{ $monthly['date']->format('F Y') }}</strong><br>
                    <small class="text-muted">{{ $monthly['date']->format('M Y') }}</small>
                </td>
                <td class="text-success">
                    <strong>Rp {{ number_format($monthly['income'], 0, ',', '.') }}</strong>
                </td>
                <td class="text-danger">
                    <strong>Rp {{ number_format($monthly['expense'], 0, ',', '.') }}</strong>
                </td>
                <td class="{{ $monthly['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <strong>Rp {{ number_format($monthly['balance'], 0, ',', '.') }}</strong>
                </td>
                <td>
                    <span class="badge bg-primary">{{ $monthly['transaction_count'] }} transaksi</span>
                </td>
                <td>
                    @if($index < count($reportData['data']) - 1)
                        @php
                            $prevMonth = $reportData['data'][$index + 1];
                            $growth = $prevMonth['balance'] != 0 ? (($monthly['balance'] - $prevMonth['balance']) / abs($prevMonth['balance'])) * 100 : 0;
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
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Chart Section for Monthly Report -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="chart-section">
            <h6><i class="fas fa-chart-line"></i> Tren Bulanan</h6>
            <canvas id="monthlyChart" height="100"></canvas>
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
    }

    .chart-section h6 {
        margin-bottom: 15px;
        color: var(--pln-blue);
        font-weight: 600;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize chart if we have data
        @if(count($reportData['data']) > 0)
        initializeMonthlyChart();
        @endif
    });

    function initializeMonthlyChart() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;

        const monthlyData = @json($reportData['data']);
        
        const labels = monthlyData.map(item => {
            const date = new Date(item.date.date);
            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
        }).reverse();

        const incomeData = monthlyData.map(item => item.income).reverse();
        const expenseData = monthlyData.map(item => item.expense).reverse();

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kas Masuk',
                    data: incomeData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: false
                }, {
                    label: 'Kas Keluar',
                    data: expenseData,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: false
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
</script>