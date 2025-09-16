<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemberianPakan extends Model
{
    use HasFactory;

    protected $table = 'pemberian_pakans';
    protected $guarded = 'id';
}
