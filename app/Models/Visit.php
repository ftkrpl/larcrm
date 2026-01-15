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
}