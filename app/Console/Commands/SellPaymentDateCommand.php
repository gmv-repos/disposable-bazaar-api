<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sell;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SellPaymentDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sells:paymentDate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sell Payment Date Notify';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sells = Sell::where('payment_date', '<=', Carbon::today())
            ->where(function ($query) {
                $query
                    ->whereHas('notifications', function ($query) {
                        $query->where('is_read', '=', 1);
                    })
                    ->orWhereDoesntHave('notifications');
            })
            ->get();

        foreach ($sells as $sell) {
            $message = 'Sell "' . $sell->invoice_id . '" has payment date.';
            $url = route('sell.invoice') . '?id=' . $sell->id;

            Notification::create([
                'type' => 'sell_payment_date',
                'record_id' => $sell->id,
                'message' => $message,
                'url' => $url,
            ]);
        }

        $this->info('Payment Dates check Success');
    }
}
