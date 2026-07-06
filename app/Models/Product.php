<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    
    protected $fillable = [
        'name', 'slug', 'description', 'price', 
        'stock', 'category', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::updated(function ($product) {
            ActivityLog::create([
                'event' => 'updated',
                'model_type' => self::class,
                'model_id' => $product->id,
                'changes' => json_encode($product->getChanges())
            ]);
        });

        static::deleted(function ($product) {
            ActivityLog::create([
                'event' => 'deleted',
                'model_type' => self::class,
                'model_id' => $product->id,
                'changes' => json_encode(['deleted_at' => $product->deleted_at])
            ]);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}