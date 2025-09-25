<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuliner extends Model
{
    use HasFactory;
    protected $table = 'kuliners';
    protected $guarded = ['id'];

    public function panen()
    {
        return $this->belongsTo(Panen::class);
    }
}
