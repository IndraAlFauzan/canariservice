<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['name'];
    protected $table = 'admin';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function induk()
    {
        return $this->hasMany(Induk::class);
    }
    public function anak()
    {
        return $this->hasMany(Anak::class);
    }
}
