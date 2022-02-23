<?php

namespace App\Imports;

use App\Models\CallidusResidual;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ImportCallidusResidual implements ToModel,WithHeadingRow,WithValidation,WithBatchInserts
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        $fecha_raw=$row['fecha'];
        $fecha=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha_raw);
        $id=session('id_calculo');
        $contrato_anterior=trim($row['contrato']);
        try{
            $contrato_anterior=trim($row['contrato_anterior']);
        }
        catch(\Exception $e)
        {
            $contrato_anterior=trim($row['contrato']);
        }

        return new CallidusResidual([
            'calculo_id'=>$id,            
            'periodo'=> trim($row['periodo']),
            'cuenta'=> trim($row['cuenta']),
            'contrato'=> trim($row['contrato']),
            'contrato_anterior'=> $contrato_anterior=='-1'?trim($row['contrato']):$contrato_anterior,
            'cliente'=>trim($row['cliente']),
            'plan'=> trim($row['plan']),
            'dn'=> trim($row['dn']),
            'propiedad'=> trim($row['propiedad']),
            'modelo'=> trim($row['modelo']),
            'fecha'=> $fecha,
            'plazo'=> $row['plazo'],
            'descuento_multirenta'=> $row['descuento_multirenta']*100,
            'afectacion_comision'=> $row['afectacion_comision']*100,
            'comision'=> $row['comision'],
            'factor_comision'=> $row['factor_comision'],
            'renta'=> $row['renta'],
            'estatus'=> $row['estatus'],
            'marca'=> $row['marca'],
        ]);
    }
    public function rules(): array
    {
        return [
            '*.periodo' => ['required'],
            '*.contrato' => ['required'],
            '*.plan' => ['required'],
            '*.dn' => ['required'],
            '*.fecha' => ['required'],
            '*.plazo' => ['required','numeric'],
            '*.descuento_multirenta' => ['required','numeric'],
            '*.renta' => ['required','numeric'],
            '*.estatus' => ['required'],
            '*.marca' => ['required'],
        ];
    }
    public function batchSize(): int
    {
        return 100;
    }
}
