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
            $table->text('ai_analysis_result')->nullable()->after('reason');
            $table->integer('ai_fraud_score')->nullable()->after('ai_analysis_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_tickets', function (Blueprint $table) {
            $table->dropColumn(['ai_analysis_result', 'ai_fraud_score']);
        });
    }
};
