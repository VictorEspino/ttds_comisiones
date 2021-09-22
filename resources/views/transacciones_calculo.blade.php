<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=transacciones_".$pago.".xls");
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
	<td>{{$transaccion->venta->user->name}}</td>
	<td>{{$transaccion->venta->fecha}}</td>
	<td>{{$transaccion->venta->cliente}}</td>
	<td>{{$transaccion->venta->dn}}</td>
	<td>{{$transaccion->venta->cuenta}}</td>
	<td>{{$transaccion->venta->tipo}}</td>
	<td>{{$transaccion->venta->folio}}</td>
	<td>{{$transaccion->venta->ciudad}}</td>
	<td>{{$transaccion->venta->plan}}</td>
	<td>{{$transaccion->venta->renta}}</td>
	<td>{{$transaccion->venta->equipo}}</td>
	<td>{{$transaccion->venta->plazo}}</td>
	<td>{{$transaccion->venta->descuento_multirenta}}</td>
	<td>{{$transaccion->venta->afectacion_comision}}</td>
    <td style="color:#0000FF">{{$transaccion->upfront}}</td>
    <td style="color:#0000FF">{{$transaccion->bono}}</td>
	<td style="color:#0000FF">{{$transaccion->tipo}}</td>
	<td style="color:#0000FF">{{$transaccion->renta}}</td>
	<td style="color:#0000FF">{{$transaccion->plazo}}</td>
	<td style="color:#0000FF">{{$transaccion->descuento_multirenta}}</td>
	<td style="color:#0000FF">{{$transaccion->afectacion_comision}}</td>
	</tr>
<?php
}
?>
</table>