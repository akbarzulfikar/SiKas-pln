<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes untuk optimasi query reporting (yang penting saja)
        
        // Index untuk transaction_date (paling sering difilter)
        if (!$this->indexExists('transactions', 'transactions_transaction_date_index')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index('transaction_date');
            });
        }
        
        // Index untuk category_id (sering di-join)
        if (!$this->indexExists('transactions', 'transactions_category_id_index')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index('category_id');
            });
        }
        
        // Index untuk unit_id (sering di-join)  
        if (!$this->indexExists('transactions', 'transactions_unit_id_index')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index('unit_id');
            });
        }
        
        // Index untuk created_by (user_id)
        if (!$this->indexExists('transactions', 'transactions_created_by_index')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index('created_by');
            });
        }
        
        // Composite index untuk query laporan yang sering digunakan
        if (!$this->indexExists('transactions', 'transactions_date_category_unit_index')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index(['transaction_date', 'category_id', 'unit_id'], 'transactions_date_category_unit_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['category_id']); 
            $table->dropIndex(['unit_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['transaction_date', 'category_id', 'unit_id']);
        });
    }
    
    /**
     * Check if index exists
     */
    private function indexExists($table, $index): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table}");
            foreach ($indexes as $idx) {
                if ($idx->Key_name === $index) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
};