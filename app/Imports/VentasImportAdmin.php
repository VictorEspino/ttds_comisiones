<?php

namespace App\Imports;

use App\Models\Venta;
use App\Models\Distribuidor;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class VentasImportAdmin implements ToModel,WithHeadingRow,WithValidation,WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    use Importable;

    public function model(array $row)
    {
        $fecha=$row['fecha'];
        $fecha_db=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha);
        $distribuidor=Distribuidor::where('numero_distribuidor',$row['distribuidor'])->get()->first();
        return new Venta([
            'user_id'=> $distribuidor->user_id,
            'cuenta'=> trim($row['cuenta']),
            'cliente'=> $row['cliente'],
            'tipo'=> $row['tipo'],
            'fecha'=> $fecha_db,
            //'propiedad'=> $row['propiedad'],
            'dn'=> trim($row['dn']),
            'plan'=> $row['plan'],
            'folio'=> trim($row['folio']),
            'ciudad'=> $row['ciudad'],
            'plazo'=> $row['plazo'],
            'renta'=> $row['renta'],
            'equipo'=> $row['equipo'],
            'descuento_multirenta'=> $row['descuento_multirenta']*100,
            'afectacion_comision'=> $row['afectacion_comision']*100,
            //'contrato'=> $row['contrato'],
            'validado'=> true,
            'user_id_carga'=> Auth::user()->id,
            'user_id_validacion'=> Auth::user()->id
        ]);
    }
    public function rules(): array
    {
        return [
            '*.distribuidor' =>['required','exists:distribuidors,numero_distribuidor','exists:users,user'],
            '*.dn' => ['required','digits:10','unique:ventas,dn'],
            '*.cuenta' => ['required'],
            '*.cliente' => ['required','max:255'],
            '*.tipo' => ['required',Rule::in(['NUEVA','ADICION','RENOVACION'])],
            '*.fecha' => ['required'],
            //'*.propiedad' => ['required',Rule::in(['NUEVO','PROPIO'])],
            '*.plan' => ['required'],
            '*.plazo' => ['required','numeric',Rule::in(['12','18','24'])],
            '*.renta' => ['required','numeric'],
            '*.equipo' => ['required'],
            '*.descuento_multirenta' => ['required','numeric'],
            '*.afectacion_comision' => ['required','numeric'],
            //'*.contrato' => ['required'],
        ];
    }
    public function batchSize(): int
    {
        return 1000;
    }
}
