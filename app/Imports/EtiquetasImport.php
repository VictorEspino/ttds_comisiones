<?php

namespace App\Imports;

use App\Models\Etiqueta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class EtiquetasImport implements ToModel,WithHeadingRow,WithValidation,WithBatchInserts
{
    use Importable;
    
    public function model(array $row)
    {
        return new Etiqueta([
            'telefono'=>$row['telefono']
        ]);
    }
    public function rules(): array
    {
        return [
            '*.telefono' => ['required','digits:10'],
        ];
    }
    public function batchSize(): int
    {
        return 50;
    }
}
