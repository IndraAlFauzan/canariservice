<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anak extends Model
{
    use HasFactory;

    protected $table = 'anak';

    protected $fillable = [
        'no_ring',
        'tanggal_lahir',
        'jenis_kelamin',
        'jenis_kenari',
        'gambar_anak',
    ];

    public function ayah()
    {
        return $this->belongsToMany(Induk::class, 'relasi_anak_induk', 'anak_id', 'induk_id')
            ->withPivot('status_induk_id')
            ->wherePivot('status_induk_id', 1);
    }

    public function ibu()
    {
        return $this->belongsToMany(Induk::class, 'relasi_anak_induk', 'anak_id', 'induk_id')
            ->withPivot('status_induk_id')
            ->wherePivot('status_induk_id', 2);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    // Di model Anak:
    public function relasiInduk()
    {
        return $this->hasMany(RelasiAnakInduk::class);
    }

    public function posting()
    {
        return $this->morphOne(PostingJual::class, 'burung');
    }
}
