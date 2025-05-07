<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    protected $table = 'alamat';

    protected $primaryKey = 'alamatID';

    protected $fillable = [
        'user_id',
        'namaAlamat',
        'namaPenerima',
        'noHpPenerima',
        'alamat',
        'kodePos',
    ];
    public $timestamps = true; 
}
