<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelasiAnakInduk extends Model
{
    protected $table = 'relasi_anak_induk';

    protected $fillable = [
        'anak_id',
        'induk_id',
        'status_induk_id'
    ];

    public $timestamps = true;

    // Jika kamu ingin buat relasi balik:
    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }

    public function induk()
    {
        return $this->belongsTo(Induk::class);
    }

    public function statusInduk()
    {
        return $this->belongsTo(StatusInduk::class, 'status_induk_id');
    }
}
