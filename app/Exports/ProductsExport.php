<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::select([
            'item_code',
            'name', 
            'category',
            'unit',
            'unit_price',
            'description',
            'brand',
            'type',
            'active'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Item Code',
            'Name',
            'Category',
            'Unit',
            'Unit Price',
            'Description',
            'Brand',
            'Type',
            'Active'
        ];
    }
}
