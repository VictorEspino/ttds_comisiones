<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['user_id',
                            'supervisor_id',
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
                            'user_id_validacion',
                            'carga_id',
                            'lead',
                            'padrino_lead'
                    ];
    use HasFactory;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
