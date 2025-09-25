<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Helpers\NumberToWords;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class PrintController extends Controller
{
    /**
     * Sanitize filename by removing invalid characters
     */
    private function sanitizeFilename($filename)
    {
        // Remove or replace invalid filename characters
        $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);
        $filename = preg_replace('/[^\w\-_\.]/', '-', $filename);
        return $filename;
    }

    public function receipt($id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::with(['category', 'unit', 'creator'])->findOrFail($id);

            // Check authorization
            if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
                abort(403, 'Anda tidak memiliki akses untuk mencetak transaksi ini');
            }

            // Convert number to words
            $amountInWords = ucwords(NumberToWords::convert($transaction->amount));

            $data = [
                'transaction' => $transaction,
                'amountInWords' => $amountInWords
            ];

            // OPTIMIZED FOR RAILWAY: Reduced PDF options untuk mengurangi memory usage
            $pdf = Pdf::loadView('pdf.receipt', $data);
            $pdf->setPaper('a4', 'portrait');

            // Minimal options untuk Railway gratis
            $pdf->setOptions([
                'isHtml5ParserEnabled' => false, // Disable untuk save memory
                'isPhpEnabled' => false,         // Disable untuk security dan memory
                'defaultFont' => 'DejaVu Sans',  // Font yang lebih ringan
                'dpi' => 96,                     // Lower DPI untuk file size lebih kecil
                'debugKeepTemp' => false,
                'chroot' => storage_path(),      // Restrict file access
            ]);

            // Stream instead of download untuk avoid memory issues
            return $pdf->stream('bukti-kas-' . $transaction->transaction_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());

            // Fallback: Return HTML view instead of PDF if generation fails
            return $this->fallbackHtmlView($transaction, $amountInWords, 'receipt');
        }
    }

    public function memo($id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::with(['category', 'unit', 'creator'])->findOrFail($id);

            // Check authorization
            if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
                abort(403, 'Anda tidak memiliki akses untuk mencetak transaksi ini');
            }

            // Only for expense transactions
            if ($transaction->transaction_type !== 'expense') {
                return redirect()->back()->with('error', 'Nota dinas hanya untuk transaksi kas keluar');
            }

            $amountInWords = ucwords(NumberToWords::convert($transaction->amount));

            $data = [
                'transaction' => $transaction,
                'amountInWords' => $amountInWords
            ];

            // OPTIMIZED FOR RAILWAY
            $pdf = Pdf::loadView('pdf.memo', $data);
            $pdf->setPaper('a4', 'portrait');

            $pdf->setOptions([
                'isHtml5ParserEnabled' => false,
                'isPhpEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'debugKeepTemp' => false,
                'chroot' => storage_path(),
            ]);

            return $pdf->stream('nota-dinas-' . $transaction->transaction_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return $this->fallbackHtmlView($transaction, $amountInWords, 'memo');
        }
    }

    /**
     * Fallback HTML view ketika PDF generation gagal
     */
    private function fallbackHtmlView($transaction, $amountInWords, $type)
    {
        $data = [
            'transaction' => $transaction,
            'amountInWords' => $amountInWords,
            'isPrintMode' => true
        ];

        return view('pdf.' . $type, $data)
            ->with('info', 'PDF generation tidak tersedia. Gunakan Print Browser (Ctrl+P) untuk mencetak.');
    }

    /**
     * Stream PDF instead of download (alternative method)
     */
    public function streamReceipt($id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::with(['category', 'unit', 'creator'])->findOrFail($id);

            // Check authorization for regular users
            if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
                abort(403, 'Anda tidak memiliki akses untuk mencetak transaksi ini');
            }

            // Convert number to words
            $amountInWords = ucwords(NumberToWords::convert($transaction->amount));

            $data = [
                'transaction' => $transaction,
                'amountInWords' => $amountInWords
            ];

            // Load PDF view
            $pdf = Pdf::loadView('pdf.receipt', $data);
            $pdf->setPaper('a4', 'portrait');

            // Set PDF options for better rendering
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Courier',
                'dpi' => 150,
                'isRemoteEnabled' => true,
            ]);

            // Stream PDF in browser
            return $pdf->stream('bukti-kas.pdf');
        } catch (\Exception $e) {
            Log::error('Error streaming receipt PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Stream memo PDF instead of download (alternative method)
     */
    public function streamMemo($id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::with(['category', 'unit', 'creator'])->findOrFail($id);

            // Check authorization for regular users
            if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
                abort(403, 'Anda tidak memiliki akses untuk mencetak transaksi ini');
            }

            // Only allow memo for expense transactions
            if ($transaction->transaction_type !== 'expense') {
                return redirect()->back()->with('error', 'Nota dinas hanya untuk transaksi kas keluar');
            }

            // Convert number to words
            $amountInWords = ucwords(NumberToWords::convert($transaction->amount));

            $data = [
                'transaction' => $transaction,
                'amountInWords' => $amountInWords
            ];

            // Load PDF view
            $pdf = Pdf::loadView('pdf.memo', $data);
            $pdf->setPaper('a4', 'portrait');

            // Set PDF options for better rendering
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Courier',
                'dpi' => 150,
                'isRemoteEnabled' => true,
            ]);

            // Stream PDF in browser
            return $pdf->stream('nota-dinas.pdf');
        } catch (\Exception $e) {
            Log::error('Error streaming memo PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Preview receipt in browser (for testing)
     */
    public function previewReceipt($id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::with(['category', 'unit', 'creator'])->findOrFail($id);

            // Check authorization for regular users
            if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
                abort(403, 'Anda tidak memiliki akses untuk melihat transaksi ini');
            }

            // Convert number to words
            $amountInWords = ucwords(NumberToWords::convert($transaction->amount));

            $data = [
                'transaction' => $transaction,
                'amountInWords' => $amountInWords
            ];

            return view('pdf.receipt', $data);
        } catch (\Exception $e) {
            Log::error('Error loading receipt preview: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading preview: ' . $e->getMessage());
        }
    }

    /**
     * Preview memo in browser (for testing)
     */
    public function previewMemo($id)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::with(['category', 'unit', 'creator'])->findOrFail($id);

            // Check authorization for regular users
            if ($user->role === 'user' && $transaction->unit_id !== $user->unit_id) {
                abort(403, 'Anda tidak memiliki akses untuk melihat transaksi ini');
            }

            // Only allow memo for expense transactions
            if ($transaction->transaction_type !== 'expense') {
                return redirect()->back()->with('error', 'Nota dinas hanya untuk transaksi kas keluar');
            }

            // Convert number to words
            $amountInWords = ucwords(NumberToWords::convert($transaction->amount));

            $data = [
                'transaction' => $transaction,
                'amountInWords' => $amountInWords
            ];

            return view('pdf.memo', $data);
        } catch (\Exception $e) {
            Log::error('Error loading memo preview: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading preview: ' . $e->getMessage());
        }
    }
}
