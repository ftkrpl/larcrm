<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // Daftarkan semua kolom yang boleh diisi otomatis saat sinkronisasi
    protected $fillable = [
        'branch', 'source', 'customerid', 'kodecust', 'extrefnbr', 'customername', 
        'status', 'addressline1', 'addressline2', 'priceclass', 
        'tipe1', 'tipe2', 'tipe3', 'admar'
    ];
}