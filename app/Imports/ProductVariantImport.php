<?php

namespace App\Imports;

use App\Models\ProductVariant;
use App\Models\Variants;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class ProductVariantImport implements
    OnEachRow,
    WithHeadingRow,
    WithChunkReading
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        $data = $row->toArray();

        $variant = Variants::find($data['variant_id']);

        $price = $variant->pack_size * $data['price_per_piece'];

        ProductVariant::updateOrCreate(
            ['id' => $data['id']],
            [
                'serial_no'        => $data['serial_no'],
                'product_id'       => $data['product_id'],
                'variant_id'       => $data['variant_id'],
                'brand_id'         => $data['brand_id'],
                'price'            => $price,
                'price_per_peice'  => $data['price_per_piece'],
                'status'           => $data['status'],
                'stock_status'     => $data['stock_status'],
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}