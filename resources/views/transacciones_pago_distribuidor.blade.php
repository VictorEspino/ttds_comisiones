<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=transacciones_distribuidor.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>distribuidor</td>
<td><b>fecha</td>
<td><b>cliente</td>
<td><b>dn</td>
<td><b>cuenta</td>
<td><b>tipo</td>
<td><b>folio</td>
<td><b>ciudad</td>
<td><b>plan</td>
<td><b>renta</td>
<td><b>equipo</td>
<td><b>plazo</td>
<td><b>descuento_multirenta</td>
<td><b>afectacion_comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>bono</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>C_tipo</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>C_renta</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>C_plazo</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>C_descuento_multirenta</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>C_afectacion_comision</td>
</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
	<td>{{\App\Models\User::find($transaccion->user_id)->name}}</td>
	<td>{{$transaccion->fecha}}</td>
	<td>{{$transaccion->cliente}}</td>
	<td>{{$transaccion->dn}}</td>
	<td>{{$transaccion->cuenta}}</td>
	<td>{{$transaccion->tipo}}</td>
	<td>{{$transaccion->folio}}</td>
	<td>{{$transaccion->ciudad}}</td>
	<td>{{$transaccion->plan}}</td>
	<td>{{$transaccion->renta}}</td>
	<td>{{$transaccion->equipo}}</td>
	<td>{{$transaccion->plazo}}</td>
	<td>{{$transaccion->descuento_multirenta}}</td>
	<td>{{$transaccion->afectacion_comision}}</td>
    <td style="color:#0000FF">{{$transaccion->upfront}}</td>
    <td style="color:#0000FF">{{$transaccion->bono}}</td>
	<td style="color:#0000FF">{{$transaccion->c_tipo}}</td>
	<td style="color:#0000FF">{{$transaccion->c_renta}}</td>
	<td style="color:#0000FF">{{$transaccion->c_plazo}}</td>
	<td style="color:#0000FF">{{$transaccion->c_descuento_multirenta}}</td>
	<td style="color:#0000FF">{{$transaccion->c_afectacion_comision}}</td>
	</tr>
<?php
}
?>
</table>