<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  'type', 'identifier', 'name', 'active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->hasOne(Source::class);
    }

    public function connectionLinks()
    {
        return $this->hasMany(ConnectionLink::class);
    }

    // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($destination) { // before delete() method call this
            $destination->connectionLink()->delete();
        });
    }
}
