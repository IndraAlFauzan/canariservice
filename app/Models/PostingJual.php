<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostingJual extends Model
{
    protected $table = 'posting_jual';

    protected $fillable = [
        'burung_id',
        'burung_type',
        'admin_id',
        'harga',
        'deskripsi',
        'status',
    ];

    public function burung()
    {
        return $this->morphTo();
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
