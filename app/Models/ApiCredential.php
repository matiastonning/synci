<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_credentials';

    protected $fillable = [
        'user_id', 'destination_type', 'source_type', 'token1', 'token2', 'type', 'active', 'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
