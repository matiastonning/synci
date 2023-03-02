<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'to', 'from', 'body', 'subject', 'confirmation_code', 'confirmation_url', 'headers', 'sender_ip', 'user_id', 'uuid'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
