<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaCobranza extends Model
{
    use HasFactory;

    public function venta()
    {
        return $this->belongsTo(Venta::class,'venta_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function callidus()
    {
        return $this->belongsTo(CallidusVenta::class,'callidus_venta_id');
    }
    public function calculo()
    {
        return $this->belongsTo(Calculo::class,'calculo_id');
    }
}
