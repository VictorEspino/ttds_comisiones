<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoACuenta extends Model
{
    use HasFactory;
    protected $fillable=['aplicado'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}
