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
        $id_carga=session('id_carga');

        return new Venta([
            'user_id'=> $distribuidor->user_id,
            'cuenta'=> trim($row['cuenta']),
            'cliente'=> trim($row['cliente']),
            'tipo'=> trim($row['tipo']),
            'fecha'=> $fecha_db,
            'propiedad'=> '-', //DEBE AGREGARSE AL LAYOUT
            'dn'=> trim($row['dn']),
            'plan'=> trim($row['plan']),
            'folio'=> trim($row['folio']),
            'ciudad'=> trim($row['ciudad']),
            'plazo'=> trim($row['plazo']),
            'renta'=> trim($row['renta']),
            'equipo'=> trim($row['equipo']),
            'descuento_multirenta'=> $row['descuento_multirenta']*100,
            'afectacion_comision'=> $row['afectacion_comision']*100,
            'contrato'=> trim($row['folio']),
            'validado'=> true,
            'user_id_carga'=> Auth::user()->id,
            'user_id_validacion'=> Auth::user()->id,
            'carga_id'=>$id_carga,
        ]);
    }
    public function rules(): array
    {
        return [
            '*.distribuidor' =>['required','exists:distribuidors,numero_distribuidor','exists:users,user'],
            '*.dn' => ['required','digits:10'],
            '*.cuenta' => ['required'],
            '*.cliente' => ['required','max:255'],
            '*.tipo' => ['required',Rule::in(['NUEVA','ADICION','RENOVACION'])],
            '*.fecha' => ['required'],
            '*.folio'=>['required','numeric'],
            '*.propiedad' => ['required',Rule::in(['NUEVO','PROPIO'])],
            '*.plan' => ['required'],
            '*.plazo' => ['required','numeric',Rule::in(['12','18','24'])],
            '*.renta' => ['required','numeric'],
            '*.descuento_multirenta' => ['required','numeric'],
            '*.afectacion_comision' => ['required','numeric'],
        ];
    }
    public function batchSize(): int
    {
        return 50;
    }
}
