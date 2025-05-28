<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusInduk extends Model
{
    protected $table = 'status_induk';

    protected $fillable = [

        'status_induk',
    ];

    public $timestamps = false;

    /**
     * Relasi ke model RelasiAnakInduk
     */
    public function relasiAnakInduk()
    {
        return $this->hasMany(RelasiAnakInduk::class, 'status_induk_id');
    }
}
