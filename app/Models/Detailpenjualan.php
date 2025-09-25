<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailpenjualan extends Model
{
    use HasFactory;
    protected $table = 'detailpenjualans';
    protected $guarded = ['id'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'item_id');
    }

    public function kuliner()
    {
        return $this->belongsTo(Kuliner::class, 'item_id');
    }

    public function panen()
    {
        return $this->belongsTo(Panen::class, 'item_id');
    }
}
