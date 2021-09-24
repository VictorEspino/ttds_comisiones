<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=pagos.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Distribuidor</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Nuevas</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Renta Nuevas</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Comision Nuevas</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Bono Nuevas</td>   
<td style="background-color:#0000FF;color:#FFFFFF"><b>Adiciones</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Renta Adiciones</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Comision Adiciones</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Bono Adiciones</td> 
<td style="background-color:#0000FF;color:#FFFFFF"><b>Renovaciones</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Renta Renovaciones</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Comision Renovaciones</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Nuevas Pendientes</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Renta Nuevas Pendiente</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Comision Nuevas Pendiente</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Bono Nuevas Pendiente</td>   
<td style="background-color:#FF0000;color:#FFFFFF"><b>Adiciones Pendientes</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Renta Adiciones Pendiente</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Comision Adiciones Pendiente</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Bono Adiciones Pendiente</td> 
<td style="background-color:#FF0000;color:#FFFFFF"><b>Renovaciones Pendientes</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Renta Renovaciones Pendiente</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Comision Renovaciones Pendiente</td>
<td style="background-color:#018501;color:#FFFFFF"><b>Anticipo Pendientes</td>
<td style="background-color:#018501;color:#FFFFFF"><b>Residual</td>
<td style="background-color:#852001;color:#FFFFFF"><b>Charge-Back</td>
<td style="background-color:#852001;color:#FFFFFF"><b>Anticipos extraordinarios</td>
<td style="background-color:#018501;color:#FFFFFF"><b>Retroactivos</td>
<td style="background-color:#018501;color:#FFFFFF"><b>PAGO</td>
</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
	<td>{{$transaccion->user->name}}</td>
    <td>{{$transaccion->nuevas}}</td>
    <td>{{$transaccion->renta_nuevas}}</td>
    <td>{{$transaccion->comision_nuevas}}</td>
    <td>{{$transaccion->bono_nuevas}}</td>
    <td>{{$transaccion->adiciones}}</td>
    <td>{{$transaccion->renta_adiciones}}</td>
    <td>{{$transaccion->comision_adiciones}}</td>
    <td>{{$transaccion->bono_adiciones}}</td>
    <td>{{$transaccion->renovaciones}}</td>
    <td>{{$transaccion->renta_renovaciones}}</td>
    <td>{{$transaccion->comision_renovaciones}}</td>
    <td>{{$transaccion->nuevas_no_pago}}</td>
    <td>{{$transaccion->nuevas_renta_no_pago}}</td>
    <td>{{$transaccion->nuevas_comision_no_pago}}</td>
    <td>{{$transaccion->nuevas_bono_no_pago}}</td>
    <td>{{$transaccion->adiciones_no_pago}}</td>
    <td>{{$transaccion->adiciones_renta_no_pago}}</td>
    <td>{{$transaccion->adiciones_comision_no_pago}}</td>
    <td>{{$transaccion->adiciones_bono_no_pago}}</td>
    <td>{{$transaccion->renovaciones_no_pago}}</td>
    <td>{{$transaccion->renovaciones_renta_no_pago}}</td>
    <td>{{$transaccion->renovaciones_comision_no_pago}}</td>
    <td>{{$transaccion->anticipo_no_pago}}</td>
    <td>{{$transaccion->residual}}</td>
    <td>{{$transaccion->charge_back}}</td>
    <td>{{$transaccion->anticipos_extraordinarios}}</td>
    <td>{{$transaccion->retroactivos_reproceso}}</td>
    <td><b>{{$transaccion->total_pago}}</b></td>

	</tr>
<?php
}
?>
</table>