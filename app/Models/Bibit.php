<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bibit extends Model
{
    use HasFactory;
    protected $table = 'bibits';
    protected $guarded = ['id'];

    public function pemberian_pakan()
    {
        return $this->hasMany(PemberianPakan::class);
    }
    public function panen()
    {
        return $this->hasMany(Panen::class);
    }

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }
}
