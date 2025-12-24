<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $table = 'tb_reminder';

    protected $fillable = [
        'tentang_reminder',
        'keterangan',
        'tanggal_reminder',
        'status',
        'status_pelaksanaan',
        'telepon_id',
    ];

    public function telepon()
    {
        return $this->belongsTo(Telepon::class, 'telepon_id');
    }
}
