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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('lease_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('term_type');
            $table->unsignedInteger('duration');
            $table->unsignedBigInteger('agreed_price');
            $table->unsignedBigInteger('deposit_amount')->default(0);
            $table->string('status')->default('active');
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('renewal_of_lease_id')->nullable()->constrained('leases')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['plot_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
