<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->foreignId('renewal_of_lease_id')
                ->nullable()
                ->after('plot_id')
                ->constrained('leases')
                ->nullOnDelete();
        });

        DB::table('booking_requests')
            ->whereNotNull('notes')
            ->orderBy('id')
            ->chunkById(100, function ($bookingRequests): void {
                foreach ($bookingRequests as $bookingRequest) {
                    preg_match('/\[renewal_of_lease:(\d+)\|[^\]]+\]/', (string) $bookingRequest->notes, $matches);

                    if (! isset($matches[1])) {
                        continue;
                    }

                    $cleanedNotes = trim((string) preg_replace('/\[renewal_of_lease:\d+\|[^\]]+\]/', '', (string) $bookingRequest->notes));

                    DB::table('booking_requests')
                        ->where('id', $bookingRequest->id)
                        ->update([
                            'renewal_of_lease_id' => (int) $matches[1],
                            'notes' => filled($cleanedNotes) ? $cleanedNotes : null,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('renewal_of_lease_id');
        });
    }
};
