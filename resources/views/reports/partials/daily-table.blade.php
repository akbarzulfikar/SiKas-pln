<!-- Daily Report Table -->
@if(count($reportData['data']) > 0)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-header">
            <tr>
                <th>Tanggal</th>
                <th>Kas Masuk</th>
                <th>Kas Keluar</th>
                <th>Saldo Harian</th>
                <th>Jumlah Transaksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['data'] as $daily)
            <tr>
                <td>
                    <strong>{{ $daily['date']->format('d/m/Y') }}</strong><br>
                    <small class="text-muted">{{ $daily['date']->format('l') }}</small>
                </td>
                <td class="text-success">
                    <strong>Rp {{ number_format($daily['income'], 0, ',', '.') }}</strong>
                </td>
                <td class="text-danger">
                    <strong>Rp {{ number_format($daily['expense'], 0, ',', '.') }}</strong>
                </td>
                <td class="{{ $daily['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <strong>Rp {{ number_format($daily['balance'], 0, ',', '.') }}</strong>
                </td>
                <td>
                    <span class="badge bg-primary">{{ $daily['transaction_count'] }} transaksi</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-info" data-date="{{ $daily['date']->format('Y-m-d') }}" onclick="showDailyDetail(this.dataset.date)">
                        <i class="fas fa-eye"></i> Detail
                    </button>
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
@else
<div class="empty-state">
    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">Tidak Ada Data</h5>
    <p class="text-muted">Tidak ada transaksi ditemukan untuk periode yang dipilih.</p>
</div>
@endif

<!-- Detail Modal (will be used later) -->
<div class="modal fade" id="dailyDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi Harian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dailyDetailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
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

<script>
    function showDailyDetail(date) {
        // This function will be implemented later for showing transaction details
        alert('Detail transaksi untuk tanggal ' + date + ' akan ditampilkan di step selanjutnya!');
    }
</script>