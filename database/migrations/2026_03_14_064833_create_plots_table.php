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
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type');
            $table->decimal('length', 8, 2);
            $table->decimal('width', 8, 2);
            $table->decimal('area_square_meters', 10, 2);
            $table->string('floor_level')->nullable();
            $table->string('location_note')->nullable();
            $table->unsignedBigInteger('base_price_monthly');
            $table->unsignedBigInteger('base_price_yearly');
            $table->string('status')->default('available');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['market_id', 'area_id']);
            $table->index(['status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
