<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Induk extends Model
{
    use HasFactory;

    protected $table = 'induk';

    protected $fillable = [
        'no_ring',
        'tanggal_lahir',
        'jenis_kelamin',
        'jenis_kenari',
        'keterangan',
        'gambar_induk',
        'admin_id',
    ];

    /**
     * Relasi ke Admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function anak()
    {
        return $this->belongsToMany(Anak::class, 'relasi_anak_induk', 'induk_id', 'anak_id');
    }

    public function relasiInduk()
    {
        return $this->hasMany(RelasiAnakInduk::class);
    }

    public function posting()
    {
        return $this->morphOne(PostingJual::class, 'burung');
    }
}
