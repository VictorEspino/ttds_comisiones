<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;
    protected $fillable=['nombre','puesto','region','activo','user_id','numero_empleado','cuota_unidades','aduana_nuevas','fecha_ingreso'];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
