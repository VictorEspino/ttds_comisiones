<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=transacciones_distribuidor.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<span style="color:rgb(10, 71, 161);font-size:40px;">VENTAS PAGADAS</span>
<br>
<br>
<br>
<table border=1>
<tr style="background-color:#777777;color:#FFFFFF">
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
<td><b>Propiedad Eq</td>
<td><b>Plazo</td>
<td><b>Descuento_multirenta</td>
<td><b>Afectacion_comision</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_Renta</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_Plazo</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_descuento_multirenta</td>
<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_afectacion_comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Comision</td>
<td style="background-color:#0000FF;color:#FFFFFF"><b>Bono</td>

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
	<td>{{$transaccion->ciudad}}</td>
	<td>{{$transaccion->plan}}</td>
	<td>{{$transaccion->renta}}</td>
	<td>{{$transaccion->equipo}}</td>
	<td>{{$transaccion->propiedad}}</td>
	<td>{{$transaccion->plazo}}</td>
	<td>{{$transaccion->descuento_multirenta}}</td>
	<td>{{$transaccion->afectacion_comision}}</td>
	<td style="color:#0000FF">{{$transaccion->c_renta}}</td>
	<td style="color:#0000FF">{{$transaccion->c_plazo}}</td>
	<td style="color:#0000FF">{{$transaccion->c_descuento_multirenta}}</td>
	<td style="color:#0000FF">{{$transaccion->c_afectacion_comision}}</td>
    <td style="color:#0000FF">{{$transaccion->upfront}}</td>
    <td style="color:#0000FF">{{$transaccion->bono}}</td>
	</tr>
<?php
}
?>
</table>
@if(!empty($query_no_pago))
<br>
<br>
<br>
<span style="color:rgb(10, 71, 161);font-size:40px;">COMISIONES PENDIENTES</span>
<br>
<br>
<br>
<table border=1>
	<tr style="background-color:#777777;color:#FFFFFF">
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
	<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_Renta</td>
	<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_Plazo</td>
	<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_descuento_multirenta</td>
	<td style="background-color:#FF0000;color:#FFFFFF"><b>Callidus_afectacion_comision</td>
	<td style="background-color:#0000FF;color:#FFFFFF"><b>Comision</td>
	<td style="background-color:#0000FF;color:#FFFFFF"><b>Bono</td>
	</tr>
	<?php
	
	foreach ($query_no_pago as $transaccion) {
		?>
		<tr>
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
			<td style="color:#0000FF">{{$transaccion->c_renta}}</td>
			<td style="color:#0000FF">{{$transaccion->c_plazo}}</td>
			<td style="color:#0000FF">{{$transaccion->c_descuento_multirenta}}</td>
			<td style="color:#0000FF">{{$transaccion->c_afectacion_comision}}</td>
			<td style="color:#0000FF">{{$transaccion->upfront}}</td>
			<td style="color:#0000FF">{{$transaccion->bono}}</td>
		</tr>
	<?php
	}
	?>
	</table>
@endif