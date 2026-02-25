<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Check for duplicate item code
        if (Product::where('item_code', $row['item_code'])->exists()) {
            return null; // Skip duplicate
        }

        return new Product([
            'item_code' => $row['item_code'],
            'name' => $row['name'],
            'category' => $row['category'],
            'unit' => $row['unit'],
            'unit_price' => $row['unit_price'],
            'description' => $row['description'] ?? null,
            'brand' => $row['brand'] ?? null,
            'type' => $row['type'] ?? null,
            'active' => isset($row['active']) ? $row['active'] : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'item_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category' => 'required|in:Product,Service',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'active' => 'boolean',
        ];
    }
}
