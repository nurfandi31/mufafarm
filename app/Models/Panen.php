<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panen extends Model
{
    use HasFactory;
    protected $table = 'panens';
    protected $guarded = ['id'];

    public function bibit()
    {
        return $this->belongsTo(Bibit::class, 'bibit_id', 'id');
    }
}
