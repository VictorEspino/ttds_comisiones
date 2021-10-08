<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComisionResidual extends Model
{
    use HasFactory;

    public function callidus()
    {
        return $this->belongsTo(CallidusResidual::class,'callidus_residual_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
