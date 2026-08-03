<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'category_id', 'title', 'author', 'publisher',
        'publish_year', 'isbn', 'synopsis', 'page_count',
        'shelf_location', 'cover_image', 'language',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    // Helper: hitung berapa eksemplar yang statusnya "tersedia"
   public function availableCopiesCount(): int
{
    return $this->copies()->where('status', 'tersedia')->where('condition', 'baik')->count();
}
}