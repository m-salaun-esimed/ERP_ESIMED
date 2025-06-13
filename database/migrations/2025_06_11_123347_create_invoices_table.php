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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_status_id')->constrained('invoice_statuses');
            $table->string('invoice_number')->unique();
            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('payment_type')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('footer_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
