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
        Schema::table('return_tickets', function (Blueprint $table) {
            $table->text('ai_summary')->nullable()->after('ai_fraud_score');
            $table->string('ai_sentiment', 50)->nullable()->after('ai_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_tickets', function (Blueprint $table) {
            $table->dropColumn(['ai_summary', 'ai_sentiment']);
        });
    }
};
