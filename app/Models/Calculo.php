<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculo extends Model
{
    use HasFactory;

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}
