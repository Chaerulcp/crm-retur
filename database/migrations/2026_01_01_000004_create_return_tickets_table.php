<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('invoice_number', 50)->nullable();
            $table->text('reason');
            $table->string('status', 30)->default('Diajukan')->index();
            $table->string('item_condition', 30)->default('Belum Diterima');
            $table->string('refund_method', 50)->nullable();
            $table->string('refund_proof_path')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tracking_token', 64)->nullable()->unique();
            $table->boolean('chat_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_tickets');
    }
};
