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
        Schema::create('page_view_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('route_name');
            $table->string('page_key');
            $table->foreignId('plot_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('total_views');
            $table->unsignedBigInteger('unique_visitors');
            $table->timestamps();

            $table->unique(['date', 'page_key', 'plot_id'], 'page_view_daily_summary_unique');
            $table->index('date');
            $table->index(['date', 'page_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_view_daily_summaries');
    }
};
