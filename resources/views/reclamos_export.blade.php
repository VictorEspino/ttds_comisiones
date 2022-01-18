<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=aclaraciones.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Fecha</td>
<td><b>Cliente</td>
<td><b>Dn</td>
<td><b>Cuenta</td>
<td><b>Tipo</td>
<td><b>Contrato</td>
<td><b>Ciudad</td>
<td><b>Plan</td>
<td><b>Renta</td>
<td><b>Equipo</td>
<td><b>Plazo</td>
<td><b>Descuento_multirenta</td>
<td><b>Afectacion_comision</td>
<td><b>Tipo</td>
<td><b>Razon</td>
<td><b>Monto</td>
</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
<?php try{
?>
	<td>{{$transaccion->venta->fecha}}
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
<?php
	}
    catch(\Exception $e)
	{
?>
	{{$transaccion->callidus->fecha}}</td>
	<td>{{$transaccion->callidus->cliente}}</td>
	<td>{{$transaccion->callidus->dn}}</td>
	<td>{{$transaccion->callidus->cuenta}}</td>
	<td>{{$transaccion->callidus->tipo}}</td>
	<td>{{$transaccion->callidus->contrato}}</td>
	<td></td>
	<td>{{$transaccion->callidus->plan}}</td>
	<td>{{$transaccion->callidus->renta}}</td>
	<td>{{$transaccion->callidus->modelo}}</td>
	<td>{{$transaccion->callidus->plazo}}</td>
	<td>{{$transaccion->callidus->descuento_multirenta}}</td>
	<td>{{$transaccion->callidus->afectacion_comision}}</td>
<?php
	}
?>

    <td>{{$transaccion->tipo}}</td>
    <td>{{$transaccion->razon}}</td>
    <td>{{$transaccion->monto}}</td>
	</tr>
<?php
}
?>
</table>