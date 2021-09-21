<?php

namespace App\Imports;

use App\Models\CallidusVenta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ImportCallidusVentas implements ToModel,WithHeadingRow,WithValidation,WithBatchInserts
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
        $fecha_baja_raw=$row['fecha_baja'];
        if($fecha_baja_raw!="")
        {
            $fecha_baja=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha_baja_raw);
        }
        else
        {
            $fecha_baja=null;
        }
        $id=session('id_calculo');

        return new CallidusVenta([
            'calculo_id'=>$id,
            'tipo'=> $row['tipo'],
            'cliente'=>$row['cliente'],
            'periodo'=> $row['periodo'],
            'cuenta'=> trim($row['cuenta']),
            'contrato'=> trim($row['contrato']),
            'plan'=> $row['plan'],
            'dn'=> trim($row['dn']),
            'propiedad'=> $row['propiedad'],
            'modelo'=> $row['modelo'],
            'fecha'=> $fecha,
            'fecha_baja'=> $fecha_baja,
            'plazo'=> $row['plazo'],
            'descuento_multirenta'=> $row['descuento_multirenta'],
            'afectacion_comision'=> $row['afectacion_comision'],
            'comision'=> $row['comision'],
            'renta'=> $row['renta'],
            'tipo_baja'=> $row['tipo_baja'],
        ]);
    }
    public function rules(): array
    {
        return [
            '*.tipo' => ['required'],
            '*.periodo' => ['required'],
            '*.cuenta' => ['required'],
            '*.contrato' => ['required'],
            '*.plan' => ['required'],
            '*.dn' => ['required','digits:10'],
            '*.propiedad' => ['required'],
            '*.fecha' => ['required'],
            '*.plazo' => ['required','numeric'],
            '*.descuento_multirenta' => ['required','numeric'],
            '*.comision' => ['required'],
            '*.renta' => ['required','numeric'],
        ];
    }
    public function batchSize(): int
    {
        return 100;
    }
}
