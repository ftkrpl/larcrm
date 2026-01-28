<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Daftarkan semua kolom yang boleh diisi otomatis saat sinkronisasi
    protected $fillable = [
        'branch', 'customerid', 'extrefnbr', 'customername', 
        'status', 'addressline1', 'addressline2', 'priceclass', 
        'tipe1', 'tipe2', 'tipe3', 'admar'
    ];
}