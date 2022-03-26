<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cartasusuario extends Model
{
    protected $fillable = ['nombre','cantidad','precio'];
}
