<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ProductsExport implements FromCollection, WithEvents
{
    protected $productIDs;
    protected $columns;
    protected $columnReplacements = [
        'category_id' => 'category_name',
        'subcategory_id' => 'subcategory_name',
        'brand_id' => 'brand_name',
        'supplier_id' => 'supplier_name',
    ];

    public function __construct(?array $productIDs, array $columns)
    {
        $this->productIDs = $productIDs;
        $this->columns = $columns;
    }

    public function collection()
    {
        $this->addNameFields();
        return $this->fetchProducts();
    }
    protected function addNameFields()
    {
        // Add Product ID as first column if not present
        if (!in_array('id', $this->columns)) {
            array_unshift($this->columns, 'id');
        }

        // Replace IDs with names in columns
        foreach ($this->columnReplacements as $idField => $nameField) {
            if (($key = array_search($idField, $this->columns)) !== false) {
                array_splice($this->columns, $key, 1, $nameField);
            }
        }
    }

    protected function fetchProducts()
    {
        $query = Product::with([
            'productVariants.brand',
            'productVariants.variant',
            'productLidOptions.lidOption',
            'productOptions.size',
            'productOptions.option',
            'brand',
            'productCategory',
            'productSubcategory',
            'supplier',
        ]);

        if (!empty($this->productIDs)) {
            $query->whereIn('id', $this->productIDs);
        }

        // Only fetch selected columns (map id fields to name)
        $columnsToSelect = $this->columns;

        // Replace IDs with names
        foreach ($this->columnReplacements as $idField => $nameField) {
            $columnsToSelect = array_map(fn($col) => $col === $idField ? $nameField : $col, $columnsToSelect);
        }

        return $query->get();
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->processProducts($sheet);
                $this->autoSizeColumns($sheet);
            },
        ];
    }

    protected function processProducts($sheet)
    {
        $products = $this->collection();
        $currentRow = 1;

        foreach ($products as $product) {
            // Write product header
            $this->writeProductHeader($sheet, $product, $currentRow);
            $currentRow++;

            // Write product details
            $currentRow = $this->writeProductDetails($sheet, $product, $currentRow);

            // Write variants
            $currentRow = $this->writeVariants($sheet, $product, $currentRow);

            // Write lid options
            $currentRow = $this->writeLidOptions($sheet, $product, $currentRow);

            // Write product options
            $currentRow = $this->writeProductOptions($sheet, $product, $currentRow);

            // Add spacing between products
            $currentRow += 3;
        }
    }

    protected function writeProductHeader($sheet, $product, $row)
    {
        $sheet->setCellValue("A{$row}", 'PRODUCT: ' . ($product->name ?? 'N/A'));
        $sheet->mergeCells("A{$row}:" . $sheet->getHighestColumn() . $row);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
        ]);
    }

    protected function writeProductDetails($sheet, $product, $startRow)
    {
        // Write headers
        $columnIndex = 'A';
        foreach ($this->columns as $column) {
            $header = $this->getHeaderName($column);
            $sheet->setCellValue($columnIndex . $startRow, $header);
            $columnIndex++;
        }

        // Style headers
        $sheet
            ->getStyle("A{$startRow}:" . Coordinate::stringFromColumnIndex(count($this->columns)) . $startRow)
            ->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF99']],
            ]);

        // Write values
        $startRow++;
        $columnIndex = 'A';
        foreach ($this->columns as $column) {
            $sheet->setCellValue($columnIndex++ . $startRow, $this->getProductValue($product, $column));
        }

        return $startRow + 2;
    }

    protected function getHeaderName($column)
    {
        $specialCases = [
            'id' => 'Product ID', // Changed from 'Id' to 'Product ID'
            'no_of_piece_qty_in_carton' => 'Pieces per Carton',
            'product_video_url' => 'Video URL',
        ];
        return $specialCases[$column] ?? ucwords(str_replace('_', ' ', $column));
    }

    protected function getProductValue($product, $column)
    {
        switch ($column) {
            case 'category_name':
                return optional($product->productCategory)->name ?? 'N/A';
            case 'subcategory_name':
                return optional($product->productSubcategory)->name ?? 'N/A';
            case 'brand_name':
                return optional($product->brand)->name ?? 'N/A';
            case 'supplier_name':
                return optional($product->supplier)->name ?? 'N/A';
            case 'image_path':
                return $product->image_path ? url($product->image_path) : 'N/A';
            default:
                return $product->$column ?? 'N/A';
        }
    }

    protected function writeVariants($sheet, $product, $startRow)
    {
        if ($product->productVariants->isEmpty()) {
            return $startRow;
        }

        // Section header
        $sheet->setCellValue("A{$startRow}", 'VARIANTS');
        $sheet->mergeCells("A{$startRow}:" . $sheet->getHighestColumn() . $startRow);
        $this->styleSectionHeader($sheet, $startRow);
        $startRow++;

        // Table headers
        $headers = ['Variant ID', 'Pack Size', 'Price Per Pice', 'Brand', 'Status'];
        $sheet->fromArray($headers, null, "A{$startRow}");
        $this->styleTableHeader($sheet, $startRow);
        $startRow++;

        // Variant data
        foreach ($product->productVariants as $variant) {
            $data = [
                $variant->id,
                optional($variant->variant)->pack_size ?? 'N/A',
                $variant->price_per_peice ?? 'N/A',
                optional($variant->brand)->name ?? 'N/A',
                $variant->status ?? 'N/A',
            ];
            $sheet->fromArray($data, null, "A{$startRow}");
            $startRow++;
        }

        return $startRow + 1;
    }

    protected function writeLidOptions($sheet, $product, $startRow)
    {
        if ($product->productLidOptions->isEmpty()) {
            return $startRow;
        }

        // Section header
        $sheet->setCellValue("A{$startRow}", 'LID OPTIONS');
        $sheet->mergeCells("A{$startRow}:" . $sheet->getHighestColumn() . $startRow);
        $this->styleSectionHeader($sheet, $startRow);
        $startRow++;

        // Table headers
        $headers = ['Products Lid Options Id', 'Lid Option', 'Price', 'Image'];
        $sheet->fromArray($headers, null, "A{$startRow}");
        $this->styleTableHeader($sheet, $startRow);
        $startRow++;

        // Lid data
        foreach ($product->productLidOptions as $lid) {
            $data = [
                $lid->id,
                optional($lid->lidOption)->name ?? 'N/A',
                $lid->price ?? 'N/A',
                optional($lid->lidOption)->image ? url(optional($lid->lidOption)->image) : 'N/A',
            ];
            $sheet->fromArray($data, null, "A{$startRow}");
            $startRow++;
        }

        return $startRow + 1;
    }

    protected function writeProductOptions($sheet, $product, $startRow)
    {
        if ($product->productOptions->isEmpty()) {
            return $startRow;
        }

        // Section header
        $sheet->setCellValue("A{$startRow}", 'PRODUCT OPTIONS');
        $sheet->mergeCells("A{$startRow}:" . $sheet->getHighestColumn() . $startRow);
        $this->styleSectionHeader($sheet, $startRow);
        $startRow++;

        // Table headers
        $headers = ['Product Options id', 'Size', 'Option', 'Price', 'Status'];
        $sheet->fromArray($headers, null, "A{$startRow}");
        $this->styleTableHeader($sheet, $startRow);
        $startRow++;

        // Options data
        foreach ($product->productOptions as $option) {
            $data = [
                $option->id,
                optional($option->size)->size ?? 'N/A',
                optional($option->option)->name ?? 'N/A',
                $option->options_price ?? 'N/A',
                $option->status ?? 'N/A',
            ];
            $sheet->fromArray($data, null, "A{$startRow}");
            $startRow++;
        }

        return $startRow + 1;
    }

    protected function styleSectionHeader($sheet, $row)
    {
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF8064A2']],
        ]);
    }

    protected function styleTableHeader($sheet, $row)
    {
        $sheet->getStyle("A{$row}:" . $sheet->getHighestColumn() . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF99']],
        ]);
    }

    protected function autoSizeColumns($sheet)
    {
        $maxColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());
        for ($col = 1; $col <= $maxColumn; $col++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }
    }
}