<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=residuales.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Distribuidor</td>
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
<td><b>Plazo</td>
<td><b>Descuento_multirenta</td>
<td><b>Afectacion_comision</td>
<td><b>Renta</td>
<td><b>Estatus</td>
<td><b>Comision</td>
    

</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
    <td>{{$transaccion->user->name}}</td>
	<td>{{$transaccion->callidus->tipo}}</td>
	<td>{{$transaccion->callidus->periodo}}</td>
	<td>{{$transaccion->callidus->cuenta}}</td>
	<td>{{$transaccion->callidus->contrato}}</td>
	<td>{{$transaccion->callidus->cliente}}</td>
	<td>{{$transaccion->callidus->plan}}</td>
	<td>{{$transaccion->callidus->dn}}</td>
	<td>{{$transaccion->callidus->propiedad}}</td>
	<td>{{$transaccion->callidus->modelo}}</td>
	<td>{{$transaccion->callidus->fecha}}</td>
	<td>{{$transaccion->callidus->plazo}}</td>
	<td>{{$transaccion->callidus->descuento_multirenta}}</td>
	<td>{{$transaccion->callidus->afectacion_comision}}</td>
    <td>{{$transaccion->callidus->renta}}</td>
    <td>{{$transaccion->callidus->estatus}}</td>
    <td>{{$transaccion->comision}}</td>
  
	</tr>
<?php
}
?>
</table>