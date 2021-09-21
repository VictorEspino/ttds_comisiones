<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['user_id',
                            'cuenta',
                            'cliente',
                            'fecha',
                            'tipo',
                            'propiedad',
                            'dn',
                            'plan',
                            'plazo',
                            'renta',
                            'equipo',
                            'descuento_multirenta',
                            'afectacion_comision',
                            'folio',
                            'ciudad',
                            'contrato',
                            'validado',
                            'user_id_carga',
                            'user_id_validacion'
                    ];
    use HasFactory;
}