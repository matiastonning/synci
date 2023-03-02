<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'source_id', 'external_id', 'status',
        'amount', 'currency',
        'description_long', 'description_short', 'category', 'reference',
        'merchant_category', 'merchant_name',
        'payment_date', 'booking_date', 'uuid'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function transferLog(): HasMany
    {
        return $this->hasMany(TransferLog::class);
    }
}
