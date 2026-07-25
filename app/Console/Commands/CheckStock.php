<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Product;
use App\Models\Notification;

class CheckStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check product stock and notify if stock is 0';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::whereColumn('stock_alert', '<=', 'available_quantity')
            ->where(function ($query) {
                $query
                    ->whereHas('notifications', function ($query) {
                        $query->where('is_read', 1);
                    })
                    ->orWhereDoesntHave('notifications');
            })
            ->get();

        foreach ($products as $product) {
            $message = 'Product "' . $product->name . '" is out of stock.';

            $url = 'admin/product/details?product_id=' . $product->id;

            Notification::create([
                'type' => 'stock_alert',
                'product_id' => $product->id,
                'message' => $message,
                'url' => $url,
            ]);
        }

        $this->info('Stock check completed.');
    }
}
