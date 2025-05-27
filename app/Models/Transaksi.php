<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaksiID';

    protected $fillable = [
        'pembeliID',
        'penitipID',
        'alamatID',
        'tipe_transaksi',
        'status',
        'waktu_transaksi'
    ];

    public $timestamps = true;

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksiID', 'transaksiID');
    }

    public function penjadwalan()
    {
        return $this->hasOne(Penjadwalan::class, 'transaksiID');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeliID');
    }


}
