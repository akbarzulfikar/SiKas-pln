<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->string('category_id', 15)->primary(); // KM001, KK001
            $table->string('category_name', 100);
            $table->enum('transaction_type', ['income', 'expense']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by', 20);
            $table->timestamps();

            // Foreign key
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('restrict');
            
            // Indexes
            $table->index('transaction_type');
            $table->index('is_active');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};