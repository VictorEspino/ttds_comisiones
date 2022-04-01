<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retroactivo extends Model
{
    use HasFactory;
    public function user_origen()
    {
        return $this->belongsTo(User::class,'user_origen_id');
    }
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    public function callidus()
    {
        return $this->belongsTo(CallidusVenta::class,'callidus_id');
    }
}
