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
        Schema::create('lease_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('period_no');
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date');
            $table->unsignedBigInteger('amount');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['lease_id', 'period_no']);
            $table->index(['lease_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_periods');
    }
};
