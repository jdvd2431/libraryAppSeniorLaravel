<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'return_date'
    ];

    /**
     * Define la relación con el libro prestado.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Define la relación con el usuario que realizó el préstamo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
