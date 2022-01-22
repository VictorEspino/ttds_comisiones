<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=charge_back_distribuidor.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<span style="color:rgb(10, 71, 161);font-size:40px;">CHARGE-BACK</span>
<br>
<br>
<br>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
<td><b>Vendedor</td>
<td><b>Fecha</td>
<td><b>Cliente</td>
<td><b>DN</td>
<td><b>Cuenta</td>
<td><b>Tipo</td>
<td><b>Folio/Contrato</td>
<td><b>Ciudad</td>
<td><b>Plan</td>
<td><b>Renta</td>
<td><b>Equipo</td>
<td><b>Propiedad Eq</td>
<td><b>Plazo</td>
<td><b>Descuento_multirenta</td>
<td><b>Afectacion_comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Bono</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Fecha Baja</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Tipo Baja</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Charge Back</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Cargo Equipo</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Aplicado a</td>


</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
	<td>{{$transaccion->vendedor}}</td>
	<td>{{$transaccion->fecha}}</td>
	<td>{{$transaccion->cliente}}</td>
	<td>{{$transaccion->dn}}</td>
	<td>{{$transaccion->cuenta}}</td>
	<td>{{$transaccion->tipo}}</td>
	<td>{{$transaccion->contrato}}</td>
	<td>{{$transaccion->ciudad}}</td>
	<td>{{$transaccion->plan}}</td>
	<td>{{$transaccion->renta}}</td>
	<td>{{$transaccion->equipo}}{{$transaccion->modelo}}</td>
    <td>{{$transaccion->propiedad}}</td>
	<td>{{$transaccion->plazo}}</td>
	<td>{{$transaccion->descuento_multirenta}}</td>
	<td>{{$transaccion->afectacion_comision}}</td>
    <td style="color:#0000FF">{{$transaccion->upfront}}</td>
    <td style="color:#0000FF">{{$transaccion->bono}}</td>
	<td style="color:#0000FF">{{$transaccion->fecha_baja}}</td>
	<td style="color:#0000FF">{{$transaccion->tipo_baja}}</td>
	<td style="color:#0000FF">{{$transaccion->charge_back}}</td>
	<td style="color:#0000FF">{{$transaccion->cargo_equipo}}</td>
    <td>{{$transaccion->name}}</td>
    
	</tr>
<?php
}
?>
</table>