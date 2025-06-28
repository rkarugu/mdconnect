<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LocumShift;
use Carbon\Carbon;

class ExpireLocumShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shifts:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire locum shifts whose end time has passed without being attended to';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $now = Carbon::now();

        // Expire shifts that are still open or confirmed but whose end time has passed
        $expiredCount = LocumShift::whereIn('status', ['open', 'confirmed'])
            ->where('end_datetime', '<', $now)
            ->update(['status' => 'expired']);

        $this->info($expiredCount.' shift(s) expired.');
    }
}
