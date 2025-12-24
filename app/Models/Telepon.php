<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telepon extends Model
{
    use HasFactory;

    protected $table = 'telepons';

    protected $fillable = [
        'nama',
        'nomor_telepon',
        'status',
    ];

    public function jenisPembayaran()
    {
        return $this->hasMany(JenisPembayaran::class, 'telepon_id');
    }

    public function reminder()
    {
        return $this->hasMany(Reminder::class, 'telepon_id');
    }
}
