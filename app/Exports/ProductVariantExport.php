<?php

namespace App\Exports;

use App\Models\ProductVariant;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductVariantExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading
{
    protected array $productIds;

    public function __construct(array $productIds = [])
    {
        $this->productIds = $productIds;
    }

    public function query()
    {
        return ProductVariant::query()
            ->with(['product', 'variant', 'brand'])
            ->when(!empty($this->productIds), function ($q) {
                $q->whereIn('product_id', $this->productIds);
            })
            ->select(
                'id',
                'serial_no',
                'product_id',
                'variant_id',
                'brand_id',
                'price',
                'price_per_peice',
                'status',
                'stock_status'
            );
    }

    public function map($pv): array
    {
        return [
            $pv->id,
            $pv->serial_no,
            $pv->product_id,
            $pv->product->name ?? '',
            $pv->variant_id,
            $pv->variant->pack_size ?? '',
            $pv->brand_id,
            $pv->brand->name ?? '',
            $pv->price,
            $pv->price_per_peice,
            $pv->status,
            $pv->stock_status,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'serial_no',
            'product_id',
            'product_name',
            'variant_id',
            'variant_name',
            'brand_id',
            'brand_name',
            'price',
            'price_per_piece',
            'status',
            'stock_status',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}