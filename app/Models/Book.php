<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'isbn',
        'pages',
        'category',
        'stock'
    ];

    /**
     * Define la relación con los préstamos de este libro.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Define la relación con la categoría del libro.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
