<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\ProductVariant;
use App\Models\Variants;
use App\Models\LidOption;
use App\Models\ProductLidOption;
use App\Models\ProductOption;
use App\Models\ProductSize;
use App\Models\Option;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    protected $currentProductId = null;

    public function collection(Collection $rows)
    {
        $expectingHeaders = false;
        $expectingData = false;
        $currentSection = null;
        $headers = null;
        $sectionHeaders = [null];

        foreach ($rows as $row) {
            if (isset($row[0]) && strpos($row[0], 'PRODUCT:') === 0) {
                $expectingHeaders = true;
                $currentSection = 'product';
                $headers = null;
                $sectionHeaders = null;
                continue;
            }

            if (isset($row[0]) && $row[0] === 'VARIANTS') {
                $currentSection = 'variants';
                $expectingHeaders = true;
                $sectionHeaders = null;
                continue;
            }

            if (isset($row[0]) && $row[0] === 'LID OPTIONS') {
                $currentSection = 'lid_options';
                $expectingHeaders = true;
                $sectionHeaders = null;
                continue;
            }

            if (isset($row[0]) && $row[0] === 'PRODUCT OPTIONS') {
                $currentSection = 'product_options';
                $expectingHeaders = true;
                $sectionHeaders = null;
                continue;
            }

            if ($expectingHeaders) {
                $sectionHeaders = $row->toArray();
                $expectingHeaders = false;
                $expectingData = true;
                continue;
            }

            if ($expectingData) {
                $dataRow = $row->toArray();
                if (!empty(array_filter($dataRow))) {
                    if ($currentSection === 'product') {
                        $this->processProductData($sectionHeaders, $dataRow);
                        $expectingData = false;
                    } elseif ($currentSection === 'variants') {
                        $this->processVariantData($sectionHeaders, $dataRow);
                    } elseif ($currentSection === 'lid_options') {
                        $this->processLidOptionData($sectionHeaders, $dataRow);
                    } elseif ($currentSection === 'product_options') {
                        $this->processProductOptionData($sectionHeaders, $dataRow);
                    }
                } else {
                    if ($currentSection === 'product') {
                        $expectingData = false;
                    }
                }
                continue;
            }
        }
    }

    protected function processProductData(array $headers, array $dataRow)
    {
        $data = array_combine($headers, $dataRow);

        if (!isset($data['Name']) || empty($data['Name'])) {
            return;
        }

        $productId = $data['Product ID'] ?? null;

        if ($productId && Product::where('id', $productId)->exists()) {
            $product = Product::find($productId);
        } else {
            $product = new Product();
        }

        $nameFields = [
            'Category Name' => ['field' => 'category_id', 'model' => ProductCategory::class],
            'Subcategory Name' => ['field' => 'subcategory_id', 'model' => ProductSubCategory::class],
            'Brand Name' => ['field' => 'brand_id', 'model' => Brand::class],
            'Supplier Name' => ['field' => 'supplier_id', 'model' => Supplier::class],
        ];

        foreach ($data as $header => $value) {
            if (array_key_exists($header, $nameFields)) {
                $field = $nameFields[$header]['field'];
                $modelClass = $nameFields[$header]['model'];
                if (!empty($value)) {
                    $model = $modelClass::firstOrCreate(['name' => $value]);
                    $product->$field = $model->id;
                }
            } else {
                $field = $this->mapHeaderToField($header);
                if ($field && in_array($field, $product->getFillable())) {
                    $product->$field = $value;
                }
            }
        }

        $product->save();
        $this->currentProductId = $product->id;
    }

    protected function processVariantData(array $headers, array $dataRow)
    {
        if (!$this->currentProductId) {
            return;
        }

        $data = array_combine($headers, $dataRow);

        $variantId = $data['Variant ID'] ?? null;
        $packSize = $data['Pack Size'] ?? null;
        $pricePerPiece = $data['Price Per Piece'] ?? null;
        $brandName = $data['Brand'] ?? null;
        $status = $data['Status'] ?? null;

        if (empty($packSize) || empty($pricePerPiece)) {
            return;
        }

        $variantId = $variantId ? (int) $variantId : null;

        if (
            $variantId &&
            ProductVariant::where('id', $variantId)->where('product_id', $this->currentProductId)->exists()
        ) {
            $variant = ProductVariant::where('id', $variantId)->where('product_id', $this->currentProductId)->first();
        } else {
            $variant = new ProductVariant();
            $variant->product_id = $this->currentProductId;
        }

        $variantRecord = Variants::firstOrCreate(['pack_size' => $packSize]);
        $variant->variant_id = $variantRecord->id;

        if (!empty($brandName)) {
            $brand = Brand::firstOrCreate(['name' => $brandName]);
            $variant->brand_id = $brand->id;
        }

        $variant->price = $packSize * $pricePerPiece;

        if (!empty($status)) {
            $variant->status = $status;
        }

        $variant->save();
    }

    protected function processLidOptionData(array $headers, array $dataRow)
    {
        if (!$this->currentProductId) {
            return;
        }

        $data = array_combine($headers, $dataRow);

        $productLidOptionId = $data['Products Lid Options Id'] ?? null;
        $lidOptionName = $data['Lid Option'] ?? null;
        $price = $data['Price'] ?? null;
        $image = $data['Image'] ?? null;

        if (empty($lidOptionName)) {
            return;
        }

        $lidOption = LidOption::where('name', $lidOptionName)->first();
        if ($lidOption) {
            if (!empty($image) && $image !== 'N/A') {
                $lidOption->image = $image;
            }
            $lidOption->save();
        } else {
            $lidOption = new LidOption();
            $lidOption->name = $lidOptionName;
            if (!empty($image) && $image !== 'N/A') {
                $lidOption->image = $image;
            }
            $lidOption->save();
        }

        if (
            $productLidOptionId &&
            ProductLidOption::where('product_id', $this->currentProductId)->where('id', $productLidOptionId)->exists()
        ) {
            $productLidOption = ProductLidOption::where('product_id', $this->currentProductId)
                ->where('id', $productLidOptionId)
                ->first();
            $productLidOption->lid_option_id = $lidOption->id;
            $productLidOption->save();
        } else {
            $productLidOption = new ProductLidOption();
            $productLidOption->product_id = $this->currentProductId;
            $productLidOption->lid_option_id = $lidOption->id;
            $productLidOption->save();
        }

        if (!empty($price) && $price !== 'N/A') {
            $productLidOption->price = $price;
        }
        // No additional save needed here as it’s handled in the if-else block
    }

    protected function processProductOptionData(array $headers, array $dataRow)
    {
        if (!$this->currentProductId) {
            return;
        }

        $data = array_combine($headers, $dataRow);

        $productOptionId = $data['Product Option Id'] ?? null;
        $size = $data['Size'] ?? null;
        $optionName = $data['Option'] ?? null;
        $price = $data['Price'] ?? null;
        $status = $data['Status'] ?? null;

        if (empty($size) || empty($optionName)) {
            return;
        }

        $sizeRecord = ProductSize::where('size', $size)->first();
        if (!$sizeRecord) {
            $sizeRecord = new ProductSize();
            $sizeRecord->size = $size;
            $sizeRecord->save();
        }

        $optionRecord = Option::where('name', $optionName)->first();
        if (!$optionRecord) {
            $optionRecord = new Option();
            $optionRecord->name = $optionName;
            $optionRecord->save();
        }

        $existingProductOption = $productOptionId ? ProductOption::where('id', $productOptionId)->first() : null;

        if ($existingProductOption) {
            $productOption = $existingProductOption;
        } else {
            $productOption = new ProductOption();
            $productOption->product_id = $this->currentProductId;
        }

        $productOption->size_id = $sizeRecord->id;
        $productOption->option_id = $optionRecord->id;

        if (!empty($price) && $price !== 'N/A') {
            $productOption->options_price = $price;
        }

        if (!empty($status)) {
            $productOption->status = $status;
        }

        $productOption->save();
    }

    protected function mapHeaderToField($header)
    {
        $specialMappings = [
            'Product ID' => 'id',
            'Pieces per Carton' => 'no_of_piece_qty_in_carton',
            'Video URL' => 'product_video_url',
        ];

        if (isset($specialMappings[$header])) {
            return $specialMappings[$header];
        }

        return strtolower(preg_replace('/\s+/', '_', $header));
    }
}
