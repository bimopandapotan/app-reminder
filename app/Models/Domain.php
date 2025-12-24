<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Domain extends Model
{
    use HasFactory;
    
    protected $dates = ['tgl_expired'];

    public function getTglExpiredAttribute($value)
    {
        return Carbon::parse($value);
    }

    protected $table = 'domain'; 

    protected $fillable = [
        'nama_domain',
        'tgl_expired',
        'telepon',
        'nama_perusahaan',
        'nominal',
        'status_berlangganan'
    ];

    public function transaksiDomain()
    {
        return $this->hasMany(TransaksiDomain::class, 'domain_id');
    }
}
