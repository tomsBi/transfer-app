<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('creditor_account_id');
            $table->uuid('debtor_account_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('reference');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('creditor_account_id')->references('id')->on('accounts');
            $table->foreign('debtor_account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
