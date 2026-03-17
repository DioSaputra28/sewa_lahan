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
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('provider_project_slug')->nullable();
            $table->string('provider_order_id');
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('request_amount');
            $table->unsignedBigInteger('fee')->nullable();
            $table->unsignedBigInteger('total_payment')->nullable();
            $table->text('payment_number')->nullable();
            $table->text('checkout_url')->nullable();
            $table->text('redirect_url')->nullable();
            $table->boolean('qris_only')->default(false);
            $table->boolean('is_sandbox')->default(false);
            $table->string('status')->default('created');
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->text('last_error_message')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index(['provider', 'provider_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_attempts');
    }
};
