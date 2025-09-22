<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Helpers\NumberToWords;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
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
                'debugKeepTemp' => false,
            ]);
            
            // Sanitize filename
            $cleanTransactionNumber = $this->sanitizeFilename($transaction->transaction_number);
            $filename = 'bukti-kas-' . $cleanTransactionNumber . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Error generating receipt PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function memo($id)
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
                'debugKeepTemp' => false,
            ]);
            
            // Sanitize filename
            $cleanTransactionNumber = $this->sanitizeFilename($transaction->transaction_number);
            $filename = 'nota-dinas-' . $cleanTransactionNumber . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Error generating memo PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
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
            \Log::error('Error streaming receipt PDF: ' . $e->getMessage());
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
            \Log::error('Error streaming memo PDF: ' . $e->getMessage());
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
            \Log::error('Error loading receipt preview: ' . $e->getMessage());
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
            \Log::error('Error loading memo preview: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading preview: ' . $e->getMessage());
        }
    }
}