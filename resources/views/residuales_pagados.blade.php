<?php
$nombre_archivo="residuales";
try
{
	$nombre_archivo=$nombre_archivo.'_'.$distribuidor->name;
}
catch (\Exception $e)
{
	;
}
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$nombre_archivo.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border=1>
<tr>
<td>Concepto</td>
<td>Periodo / Week</td>
<td>Canal / Direccion / Division</td>
<td>Nombre Distribuidor</td>
<td>Clave Distribuidor</td>
<td>PuntoVenta</td>
<td>Cuenta</td>
<td>Contrato / Co_id</td>
<td>Nombre del Cliente</td>
<td>Plan / Nombre Plan</td>
<td>Telefono</td>
<td>Propiedad Equipo</td>
<td>Modelo / Modelos</td>
<td>Fecha Movimiento</td>
<td>Razon Movimiento</td>
<td>Dias Alta  & Baja</td>
<td>Plazo Forzoso</td>
<td>Descuento MultiRenta</td>
<td>Renta</td>
<td>Commission</td>
<td>Tipo de Periodo</td>
<td>Estatus</td>
<td>Marca</td>
<td>Esquema (Solo aplica en marcas legado)</td>
<td>Sistema Origen</td>
<td>Distribuidor</td>
</tr>
<?php
$total=0;
foreach ($query as $transaccion) {
	$total=$total+$transaccion->comision;
	?>
	<tr>
<td>RESIDUALES</td>
<td>{{$transaccion->callidus->periodo}}</td>
<td>DISTRIBUIDORES</td>
<td>TV TELCO & DATA SOLUTIONS SA DE CV</td>
<td>108249</td>
<td>TV TELCO & DATA SOLUTIONS SA DE CV</td>
<td>{{$transaccion->callidus->cuenta}}</td>
<td>{{$transaccion->callidus->contrato}}</td>
<td>{{$transaccion->callidus->cliente}}</td>
<td>{{$transaccion->callidus->plan}}</td>
<td>{{$transaccion->callidus->dn}}</td>
<td>{{$transaccion->callidus->propiedad}}</td>
<td>{{$transaccion->callidus->modelo}}</td>
<td>{{$transaccion->callidus->fecha}}</td>
<td></td>
<td>0</td>
<td>{{$transaccion->callidus->plazo}}</td>
<td>{{$transaccion->callidus->descuento_multirenta}}</td>
<td>{{$transaccion->callidus->renta}}</td>
<td>{{$transaccion->comision}}</td>
<td>Monthly</td>
<td>{{$transaccion->callidus->estatus}}</td>
<td>{{$transaccion->callidus->marca}}</td>
<td>ESPECIAL_442545</td>
<td>IUSACELL</td>
<td>{{$transaccion->user->name}}</td>
</tr>
<?php
}
?>
<tr>
	<td colspan=18></td>
	<td><b>Subtotal Residual</td>
	<td><b>{{$total}}</td>
	</tr>
</table>