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

            Schema::table('batch', function (Blueprint $table) {
                // Add total_cost_price column
                $table->string('total_cost_price')->after('batch_name')->nullable();

                // Change data type of total_retail_price column to VARCHAR
                $table->string('total_retail_price')->after('total_cost_price')->nullable();
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch', function (Blueprint $table) {
            // Drop total_cost_price column
            $table->dropColumn('total_cost_price');

            // Drop total_retail_price column
            $table->dropColumn('total_retail_price');
        });
    }
};
