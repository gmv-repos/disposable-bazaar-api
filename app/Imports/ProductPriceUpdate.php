<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Product;

class ProductPriceUpdate implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            if ($key > 0) {
                //Skip first row
                $product = Product::where('code', $row[0])->first();
                if ($product) {
                    $product->current_sale_price = $row[1];
                    $product->save();
                }
            }
        }
    }
}
