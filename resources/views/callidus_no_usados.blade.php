<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=callidus_no_usados.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Tipo</td>
<td><b>Periodo</td>
<td><b>Cuenta</td>
<td><b>Contrato</td>
<td><b>Cliente</td>
<td><b>Plan</td>
<td><b>DN</td>
<td><b>Propiedad</td>
<td><b>Modelo</td>
<td><b>Fecha</td>
<td><b>Fecha_baja</td>
<td><b>Plazo</td>
<td><b>Descuento_multirenta</td>
<td><b>Afectacion_comision</td>
<td><b>Comision</td>
<td><b>Renta</td>
<td><b>Tipo_baja</td>
</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
	<td>{{$transaccion->tipo}}</td>
	<td>{{$transaccion->periodo}}</td>
	<td>{{$transaccion->cuenta}}</td>
	<td>{{$transaccion->contrato}}</td>
	<td>{{$transaccion->cliente}}</td>
	<td>{{$transaccion->plan}}</td>
	<td>{{$transaccion->dn}}</td>
	<td>{{$transaccion->propiedad}}</td>
	<td>{{$transaccion->modelo}}</td>
	<td>{{$transaccion->fecha}}</td>
	<td>{{$transaccion->fecha_baja}}</td>
	<td>{{$transaccion->plazo}}</td>
	<td>{{$transaccion->descuento_multirenta}}</td>
	<td>{{$transaccion->afectacion_comision}}</td>
    <td>{{$transaccion->comision}}</td>
    <td>{{$transaccion->renta}}</td>
    <td>{{$transaccion->tipo_baja}}</td>
	</tr>
<?php
}
?>
</table>