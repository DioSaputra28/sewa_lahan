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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('provider_project_slug')->nullable();
            $table->string('provider_order_id')->unique();
            $table->string('provider_status')->nullable();
            $table->string('provider_payment_method')->nullable();
            $table->text('provider_payment_number')->nullable();
            $table->unsignedBigInteger('provider_fee')->nullable();
            $table->unsignedBigInteger('provider_total_payment')->nullable();
            $table->timestamp('provider_expired_at')->nullable();
            $table->timestamp('provider_completed_at')->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('failure_code')->nullable();
            $table->text('failure_message')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['provider', 'provider_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
