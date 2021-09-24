<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallidusVenta extends Model
{
   protected $fillable = ['tipo',
                            'periodo',
                            'cuenta',
                            'contrato',
                            'cliente',
                            'plan',
                            'dn',
                            'propiedad',
                            'modelo',
                            'fecha',
                            'fecha_baja',
                            'plazo',
                            'descuento_multirenta',
                            'afectacion_comision',
                            'comision',
                            'renta',
                            'calculo_id'
                    ];
    use HasFactory;

    public function pagada()
    {
        return($this->hasOne(ComisionVenta::class,'callidus_venta_id'));
    }
}
