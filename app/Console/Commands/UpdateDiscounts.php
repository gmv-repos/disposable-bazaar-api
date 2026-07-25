<?php
namespace App\Console\Commands;

use App\Models\Discount;
use Illuminate\Console\Command;

class UpdateDiscounts extends Command
{
    // The name and signature of the console command
    protected $signature = 'discounts:update-status';

    // The console command description
    protected $description = 'Update discount statuses based on current time';

    // Execute the console command
    public function handle()
    {
        $now = now();

        Discount::where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->update(['is_active' => true]);

        Discount::where('end_time', '<', $now)->update(['is_active' => false]);

        $this->info('Discount statuses updated successfully.');
    }
}
