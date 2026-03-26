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
        Schema::create('page_view_events', function (Blueprint $table) {
            $table->id();
            $table->timestamp('visited_at');
            $table->string('route_name');
            $table->string('page_key');
            $table->string('path');
            $table->foreignId('plot_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id');
            $table->string('visitor_hash');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('visited_at');
            $table->index('page_key');
            $table->index('plot_id');
            $table->index('session_id');
            $table->index('visitor_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_view_events');
    }
};
