<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. Existing sponsorships get the frequency their last
     * real (non-zero) payment period suggests: up to 45 days is monthly,
     * anything longer yearly. Those without one stay empty. The id a
     * sponsorship had in Portugal Zoófilo, kept as a "[PZ apad N]" marker in
     * the notes by the import, becomes the previous_id column.
     */
    public function up(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->string('payment_frequency', 10)->nullable()->after('send_newsletter');
            $table->string('previous_id', 30)->nullable()->after('pet_id');
        });

        DB::table('sponsorships')->where('notes', 'like', '%[PZ apad %')->get(['id', 'notes'])
            ->each(function (object $sponsorship): void {
                if (preg_match('/\[PZ apad (\d+)\]/', (string) $sponsorship->notes, $matches) === 1) {
                    DB::table('sponsorships')->where('id', $sponsorship->id)->update(['previous_id' => $matches[1]]);
                }
            });

        $lastPayments = DB::table('sponsorship_payments')
            ->whereNull('deleted_at')
            ->where('payment_value', '>', 0)
            ->orderBy('payment_date')
            ->get(['sponsorship_id', 'start_date', 'end_date'])
            ->keyBy('sponsorship_id');

        foreach ($lastPayments as $sponsorshipId => $payment) {
            $days = Carbon::parse($payment->start_date)->diffInDays(Carbon::parse($payment->end_date));

            DB::table('sponsorships')->where('id', $sponsorshipId)->update([
                'payment_frequency' => $days <= 45 ? 'monthly' : 'yearly',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->dropColumn(['payment_frequency', 'previous_id']);
        });
    }
};
