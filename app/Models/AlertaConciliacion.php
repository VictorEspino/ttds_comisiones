<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaConciliacion extends Model
{
    use HasFactory;

    public function callidus()
    {
        return $this->belongsTo(CallidusVenta::class,'callidus_venta_id');
    }
    public function residual()
    {
        return $this->belongsTo(CallidusResidual::class,'callidus_residual_id');
    }
}
