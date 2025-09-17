<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kolam extends Model
{
    use HasFactory;
    protected $table = 'kolams';
    protected $guarded = ['id'];

    public function pemberian_pakan()
    {
        return $this->hasMany(PemberianPakan::class);
    }
    public function panen()
    {
        return $this->hasMany(Panen::class);
    }
    public function bibit()
    {
        return $this->hasMany(Bibit::class);
    }
}
