<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallidusResidual extends Model
{
    use HasFactory;
    
    protected $fillable = [ 
            'calculo_id',
            'periodo',
            'cuenta',
            'contrato',
            'cliente',
            'plan',
            'dn',
            'propiedad',
            'modelo',
            'fecha',
            'plazo',
            'descuento_multirenta',
            'afectacion_comision',
            'comision',
            'factor_comision',
            'renta',
            'estatus',
            'marca',
    ];
}
