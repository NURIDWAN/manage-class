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
        Schema::table('class_funds', function (Blueprint $table) {
            $table->integer('cash_in_total')->default(0)->after('total_balance');
            $table->integer('cash_out_total')->default(0)->after('cash_in_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_funds', function (Blueprint $table) {
            $table->dropColumn(['cash_in_total', 'cash_out_total']);
        });
    }
};

