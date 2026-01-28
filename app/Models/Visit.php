<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    // Pastikan ini ada agar data bisa disimpan
    protected $guarded = [];
    
    protected $fillable = [
        'kodesales',
        'kodecust',
        'kodeacum',
        'namacust',
        'namapic',
        'jabatanpic',
        'status',
        'visit_date',
        'notes'];

    public function activities()
    {
        return $this->hasMany(VisitActivity::class);
    }

    public function customer()
    {
        // Asumsi di tabel visits ada kolom 'customerid' yang merujuk ke 'customerid' di tabel customers
        return $this->belongsTo(Customer::class, 'kodeacum', 'customerid');
    }
}