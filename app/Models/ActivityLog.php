<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'proses_produksi_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
    ];

    public $timestamps = false;

    // Relasi ke Proses Produksi (Banyak log untuk 1 proses)
    // untuk mengambil data pada proses_produksi_id
    public function prosesProduksi()
    {
        return $this->belongsTo(ProsesProduksi::class, 'proses_produksi_id', 'id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
