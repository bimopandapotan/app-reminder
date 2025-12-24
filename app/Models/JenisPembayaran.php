<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JenisPembayaran extends Model
{
    use HasFactory;

    public function getTanggalJatuhTempoAttribute($value)
    {
        return Carbon::parse($value);
    }

    protected $table = 'tb_jenis_pembayaran';
    
    protected $fillable = [
        'jenis_pembayaran',
        'status',
        'tanggal_jatuh_tempo',
        'telepon_id',
    ];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_jenis_pembayaran');
    }

    public function telepon()
    {
        return $this->belongsTo(Telepon::class, 'telepon_id');
    }

}