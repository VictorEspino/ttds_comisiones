<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=alertas.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Distribudor</td>
<td><b>Contrato</td>
<td><b>Cuenta</td>
<td><b>Fecha</td>
<td><b>Dn</td>
<td><b>Cliente</td>
<td><b>Plan</td>
<td><b>Renta</td>
<td><b>Periodos medidos</td>
<td><b>Alerta</td>
</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
	<td>{{$transaccion->user_id=="1"?'No identificado':$transaccion->user->name}}</td>
	<td>{{$transaccion->callidus->contrato}}</td>
    <td>{{$transaccion->callidus->cuenta}}</td>
    <td>{{$transaccion->callidus->fecha}}</td>
	<td>{{$transaccion->callidus->dn}}</td>
	<td>{{$transaccion->callidus->cliente}}</td>
	<td>{{$transaccion->callidus->plan}}</td>
	<td>{{$transaccion->callidus->renta}}</td>
    <td>{{$transaccion->medidos}}</td>
	<td>{{$transaccion->alerta}}</td>
	</tr>
<?php
}
?>
</table>