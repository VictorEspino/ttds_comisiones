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
<td><b>distribuidor</td>
<td><b>mesa_control</td>
<td><b>numero_contrato</td>
<td><b>acuerdo</td>
</tr>
<?php

foreach ($query as $transaccion) {
	$distribuidor='';
	$mesa_control='';
	if($transaccion->distribuidor!=0)
	{
		$distribuidor=$usuarios[$transaccion->distribuidor];
	}
	if($transaccion->mesa_control!=0)
	{
		$mesa_control=$usuarios[$transaccion->mesa_control];
	}
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
	<td>{{$distribuidor}}</td>
	<td>{{$mesa_control}}</td>
	<td>{{$transaccion->numero_contrato}}</td>
	<td>{{$transaccion->acuerdo}}</td>
	</tr>
<?php
}
?>
</table>