<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Dinas - {{ $transaction->transaction_number }}</title>
    <style>
        @page {
            margin: 1in;
            size: auto;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            font-weight: normal;
            color: rgba(200, 200, 200, 0.3);
            z-index: -1;
            pointer-events: none;
            font-family: Arial, sans-serif;
            letter-spacing: 10px;
        }

        .header {
            text-align: left;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 11pt;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .unit-name {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        .document-title {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            text-align: center;
        }

        .memo-number {
            text-align: center;
            font-size: 11pt;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .memo-details {
            margin-bottom: 10px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table td {
            border: none;
            padding: 4px 0;
            vertical-align: top;
        }

        .detail-label {
            width: 80px;
            font-size: 11pt;
        }

        .detail-separator {
            width: 20px;
            text-align: center;
            font-size: 11pt;
        }

        .detail-value {
            font-size: 11pt;
        }

        .content {
            margin: 0 0 15px 0;
            text-align: justify;
            line-height: 1.6;
            margin-left: 100px;
            /* Sejajar dengan isi Hal */
        }

        .content p {
            margin-bottom: 15px;
            text-indent: 0;
        }

        .amount-text {
            font-weight: bold;
        }

        .signature {
            text-align: right;
            margin-top: 40px;
            margin-right: 0;
        }

        .signature-name {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 100px;
        }

        /* Responsive adjustments */
        @media print {
            body {
                font-size: 11pt;
            }

            .content {
                line-height: 1.6;
            }
        }

        @media (min-height: 11in) {
            body {
                font-size: 11pt;
            }

            .company-name,
            .unit-name,
            .document-title {
                font-size: 11pt;
            }
        }
    </style>
</head>

<body>
    <!-- Watermark -->
    <div class="watermark">SALINAN</div>

    <div class="header">
        <div class="company-name">PT PLN (PERSERO)</div>
        <div class="unit-name">{{ strtoupper($transaction->unit->unit_name) }}</div>
        <div class="document-title">NOTA DINAS</div>
        <div class="memo-number">
            Nomor: {{ str_pad(substr($transaction->transaction_id, -4), 4, '0', STR_PAD_LEFT) }}/KEU.01.02/{{ strtoupper(str_replace(' ', '', $transaction->unit->unit_name)) }}/{{ $transaction->transaction_date->format('Y') }}
        </div>
    </div>


    <div class="memo-details">
        <table class="detail-table">
            <tr>
                <td class="detail-label">Kepada</td>
                <td class="detail-separator">:</td>
                <td class="detail-value">Yth. Manager</td>
            </tr>
            <tr>
                <td class="detail-label">Dari</td>
                <td class="detail-separator">:</td>
                <td class="detail-value">{{ strtoupper($transaction->creator->position ?: 'ASMAN KEU DAN UMUM') }}</td>
            </tr>
            <tr>
                <td class="detail-label">Tanggal</td>
                <td class="detail-separator">:</td>
                <td class="detail-value">{{ $transaction->transaction_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="detail-label">Sifat</td>
                <td class="detail-separator">:</td>
                <td class="detail-value">Segera - Biasa</td>
            </tr>
            <tr>
                <td class="detail-label">Lampiran</td>
                <td class="detail-separator">:</td>
                <td class="detail-value">{{ $transaction->evidence_file ? '1 Lembar' : '-' }}</td>
            </tr>
            <tr>
                <td class="detail-label">Hal</td>
                <td class="detail-separator">:</td>
                <td class="detail-value">Permohonan Pembayaran</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>
            Mohon dapat disetujui pembayaran biaya {{ $transaction->category->category_name }}
            @if($transaction->description)
            untuk {{ $transaction->description }}
            @endif
            di Kantor PT PLN (Persero) {{ $transaction->unit->unit_name }}
            tanggal {{ $transaction->transaction_date->format('d F Y') }}
            sebesar <span class="amount-text">Rp {{ number_format($transaction->amount, 0, ',', '.') }},-</span>
            ({{ $amountInWords }} rupiah).
        </p>

        <p>
            Demikian disampaikan, atas persetujuannya diucapkan terima kasih.
        </p>
    </div>

    <div class="signature">
        <div class="signature-name">ZULHAM ZA</div>
    </div>
</body>

</html>