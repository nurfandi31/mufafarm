<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualans';
    protected $guarded = ['id'];

    public function panen()
    {
        return $this->belongsTo(Panen::class, 'panen_id');
    }

    public function items()
    {
        return $this->hasMany(Detailpenjualan::class, 'penjualan_id');
    }
}
