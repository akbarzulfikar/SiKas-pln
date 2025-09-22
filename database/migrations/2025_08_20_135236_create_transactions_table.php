<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('transaction_id', 30)->primary(); // TRX_UP3LGS_20241201_001
            $table->string('transaction_number', 30)->unique(); // Display number
            $table->date('transaction_date');
            $table->enum('transaction_type', ['income', 'expense']);
            $table->string('category_id', 15);
            $table->string('unit_id', 10);
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('evidence_file', 255)->nullable();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->string('created_by', 20);
            $table->string('approved_by', 20)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('restrict');
            $table->foreign('unit_id')->references('unit_id')->on('units')->onDelete('restrict');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('transaction_date');
            $table->index('transaction_type');
            $table->index('category_id');
            $table->index('unit_id');
            $table->index('status');
            $table->index('created_by');
            $table->index(['transaction_date', 'unit_id']);
            $table->index(['transaction_type', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};