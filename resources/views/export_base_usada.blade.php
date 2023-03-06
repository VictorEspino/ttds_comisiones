<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=base_usada.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Fecha</td>
<td><b>Cliente</td>
<td><b>DN</td>
<td><b>Cuenta</td>
<td><b>Tipo</td>
<td><b>Folio</td>
<td><b>Plan</td>
<td><b>Renta</td>
<td><b>Equipo</td>
<td><b>Plazo</td>
<td><b>Propiedad</td>
<td><b>captura_mesa_control</td>
<td><b>incluida_callidus</td>
</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
	<td>{{$transaccion->fecha}}</td>
	<td>{{$transaccion->cliente}}</td>
	<td>{{$transaccion->dn}}</td>
	<td>{{$transaccion->cuenta}}</td>
	<td>{{$transaccion->tipo}}</td>
	<td>{{$transaccion->folio}}</td>
	<td>{{$transaccion->plan}}</td>
	<td>{{$transaccion->renta}}</td>
	<td>{{$transaccion->equipo}}</td>
	<td>{{$transaccion->plazo}}</td>
	<td>{{$transaccion->propiedad}}</td>
	<td>{{$transaccion->captura_mesa_control}}</td>
	<td>{{$transaccion->incluida_callidus}}</td>
	</tr>
<?php
}
?>
</table>