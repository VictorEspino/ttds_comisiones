<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=transacciones_".$pago.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Distribuidor</td>
<td><b>Fecha</td>
<td><b>Cliente</td>
<td><b>DN</td>
<td><b>Cuenta</td>
<td><b>Tipo</td>
<td><b>Folio</td>
<td><b>Ciudad</td>
<td><b>Plan</td>
<td><b>Renta</td>
<td><b>Equipo</td>
<td><b>Plazo</td>
<td><b>Descuento_multirenta</td>
<td><b>Afectacion_comision</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_renta</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_plazo</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_descuento_multirenta</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_afectacion_comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Bono</td>
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
	<td style="color:#0000FF">{{$pago=="PAGO"?$transaccion->callidus->renta:'0'}}</td>
	<td style="color:#0000FF">{{$pago=="PAGO"?$transaccion->callidus->plazo:'0'}}</td>
	<td style="color:#0000FF">{{$pago=="PAGO"?$transaccion->callidus->descuento_multirenta:'0'}}</td>
	<td style="color:#0000FF">{{$pago=="PAGO"?$transaccion->callidus->afectacion_comision:'0'}}</td>
	<td style="color:#0000FF">{{$transaccion->upfront}}</td>
    <td style="color:#0000FF">{{$transaccion->bono}}</td>
	</tr>
<?php
}
?>
</table>