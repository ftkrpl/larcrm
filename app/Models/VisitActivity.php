<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitActivity extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'visit_id',
        'jenis',
        'kodebarang',
        'kelompok_barang',
        'sample',
        'status',
        'status',
        'result'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
