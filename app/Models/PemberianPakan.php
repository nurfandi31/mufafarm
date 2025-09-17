<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemberianPakan extends Model
{
    use HasFactory;

    protected $table = 'pemberian_pakans';
    protected $guarded = ['id'];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }

    public function pakan()
    {
        return $this->belongsTo(Pakan::class);
    }
    public function bibit()
    {
        return $this->belongsTo(Bibit::class);
    }
}
