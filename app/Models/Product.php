<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use SoftDeletes, Searchable;

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'unit',
        'unit_price',
        'description',
        'brand',
        'type',
        'active',
        'primary_image_id'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function primaryImage()
    {
        return $this->belongsTo(ProductPhoto::class, 'primary_image_id');
    }

    public function imagePhotos()
    {
        return $this->hasMany(ProductPhoto::class)
                    ->where(function($query) {
                        $query->where('photo_path', 'LIKE', '%.jpg')
                              ->orWhere('photo_path', 'LIKE', '%.jpeg')
                              ->orWhere('photo_path', 'LIKE', '%.png')
                              ->orWhere('photo_path', 'LIKE', '%.gif');
                    })
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc');
    }

    public function getPrimaryImageAttribute()
    {
        // If a primary image is set, return it
        if ($this->primary_image_id && $this->primaryImage) {
            return $this->primaryImage;
        }
        
        // Otherwise, return the first image photo that belongs to this product
        return $this->imagePhotos()->first();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'item_code' => $this->item_code,
            'name' => $this->name,
            'category' => $this->category,
            'unit' => $this->unit,
            'description' => $this->description,
            'brand' => $this->brand,
            'type' => $this->type,
        ];
    }
}
