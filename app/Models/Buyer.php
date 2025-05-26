<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'photo'];
    protected $table = 'buyer';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
