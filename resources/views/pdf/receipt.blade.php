<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Kas {{ $transaction->transaction_type == 'income' ? 'Penerimaan' : 'Pengeluaran' }}</title>
    <style>
        @page {
            margin: 20mm;
            size: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .no-header {
            text-align: right;
            font-size: 13px;
            margin-bottom: 4vh;
            font-weight: normal;
        }
        .company-header {
            text-align: left;
            margin-bottom: 5vh;
        }
        .company-main {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 0.5vh;
        }
        .company-sub {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 0.3vh;
        }
        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 5vh 0;
            letter-spacing: 1px;
        }
        .kas-kecil {
            text-align: left;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            margin-bottom: 25px;
            flex-grow: 1;
        }
        .content-table td, .content-table th {
            border: 1px solid #000;
            padding: 15px 12px;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
            line-height: 1.4;
        }
        .content-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f8f8f8;
        }
        .col-uraian {
            width: 50%;
        }
        .col-nomor {
            width: 18%;
            text-align: center;
        }
        .col-mata-uang {
            width: 12%;
            text-align: center;
        }
        .col-jumlah {
            width: 20%;
            text-align: right;
        }
        .row-total .col-jumlah, .row-total .col-mata-uang {
            font-weight: bold;
        }
        .row-total .col-uraian {
            text-align: right;
            font-weight: bold;
        }
        .row-terbilang {
            font-size: 14px;
        }
        .row-terbilang .col-uraian {
            text-align: left;
            padding: 15px 12px;
        }
        .location-date {
            text-align: right;
            margin-top: 25px;
            margin-bottom: 40px;
            font-size: 14px;
        }
        .signature-section {
            margin-top: 40px;
            padding-top: 0;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 20px 10px;
            border: none;
            height: 120px;
        }
        .signature-title {
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: normal;
        }
        .signature-name {
            font-size: 14px;
            font-weight: normal;
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .signature-placeholder {
            font-size: 14px;
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .signature-cell {
            position: relative;
            height: 120px;
        }
        
        /* Responsive adjustments */
        @media print {
            body {
                font-size: 13px;
            }
            .content-table td, .content-table th {
                padding: 12px 10px;
                font-size: 13px;
            }
            .signature-table td {
                height: 100px;
            }
            .kas-kecil {
                margin-bottom: 20px;
            }
            .content-table {
                margin-top: 20px;
                margin-bottom: 20px;
            }
            .location-date {
                margin-top: 20px;
                margin-bottom: 30px;
            }
            .signature-section {
                margin-top: 30px;
            }
        }
        
        /* For larger paper sizes */
        @media (min-height: 11in) {
            body {
                font-size: 15px;
            }
            .content-table td, .content-table th {
                padding: 18px 15px;
                font-size: 15px;
            }
            .company-main {
                font-size: 18px;
            }
            .document-title {
                font-size: 18px;
            }
            .signature-table td {
                height: 140px;
            }
            .kas-kecil {
                margin-bottom: 30px;
            }
            .content-table {
                margin-top: 30px;
                margin-bottom: 30px;
            }
            .location-date {
                margin-top: 30px;
                margin-bottom: 50px;
            }
            .signature-section {
                margin-top: 50px;
            }
        }
        
        /* For smaller paper sizes */
        @media (max-height: 10in) {
            .signature-table td {
                height: 100px;
            }
            .kas-kecil {
                margin-bottom: 20px;
            }
            .content-table {
                margin-top: 20px;
                margin-bottom: 20px;
            }
            .location-date {
                margin-top: 20px;
                margin-bottom: 30px;
            }
            .signature-section {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="no-header">
        No: {{ $transaction->transaction_number }}
    </div>

    <div class="company-header">
        <div class="company-main">PT. PLN (PERSERO)</div>
        <div class="company-sub">UID Aceh</div>
        <div class="company-sub">{{ $transaction->unit->unit_name }}</div>
    </div>

    <div class="document-title">
        BUKTI KAS / BANK {{ strtoupper($transaction->transaction_type == 'income' ? 'PENERIMAAN' : 'PENGELUARAN') }}
    </div>

    <div class="kas-kecil">
        Kas kecil
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th class="col-uraian">Uraian</th>
                <th class="col-nomor">Nomor Dokumen</th>
                <th class="col-mata-uang">Mata uang</th>
                <th class="col-jumlah">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-uraian">
                    {{ $transaction->category->category_name }}<br>
                    {{ $transaction->description ?: 'Transaksi kas ' . $transaction->unit->unit_name }}<br>
                    {{ $transaction->transaction_date->format('d M Y') }}
                </td>
                <td class="col-nomor">{{ $transaction->transaction_number }}</td>
                <td class="col-mata-uang">IDR</td>
                <td class="col-jumlah">{{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="row-total">
                <td class="col-uraian">Jumlah :</td>
                <td class="col-nomor"></td>
                <td class="col-mata-uang">IDR</td>
                <td class="col-jumlah">{{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="row-terbilang">
                <td colspan="4" class="col-uraian">
                    Terbilang: {{ strtoupper($amountInWords) }} RUPIAH
                </td>
            </tr>
        </tbody>
    </table>

    <div class="location-date">
        LANGSA, {{ strtoupper($transaction->transaction_date->format('d F Y')) }}
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="signature-title">Mengetahui:</div>
                    <div class="signature-name">GUSTHAMA DICKY ZACHRANDY</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-title">Menyetujui:</div>
                    <div class="signature-name">ZULHAM ZA</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-title">{{ $transaction->transaction_type == 'income' ? 'Menerima:' : 'Menerima:' }}</div>
                    <div class="signature-placeholder">(.....................)</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>