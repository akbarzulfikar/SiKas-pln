<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['approved_by']);
            
            // Drop columns related to status
            $table->dropColumn([
                'status', 
                'approved_by', 
                'approved_at', 
                'notes'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->string('approved_by', 20)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            
            // Re-add foreign key constraint
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }
};