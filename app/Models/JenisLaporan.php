<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisLaporan extends Model
{
    use HasFactory;
    protected $table = 'jenis_laporans';
    protected $guarded = ['id'];
}
