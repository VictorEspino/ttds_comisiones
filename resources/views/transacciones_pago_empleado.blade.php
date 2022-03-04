<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$empleado->name.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<br>
<br>
<br>
<table border=1>
	<tr>
		<td colspan=28 style="font-size:40px;"><center>CÁLCULO DE COMISIONES DE ACTIVACIONES Y RENOVACIONES
		</td>
	</tr>		
<tr style="background-color:#ffffff;color:#ffffff00">
<td>Vendedor</td>
<td>Commission Name</td>	
<td>Periodo / Week</td>
<td>Canal / Direccion / Division</td>
<td>Nombre Distribuidor</td>
<td>Clave Distribuidor</td>
<td>Cuenta</td>
<td>Contrato / Co_id</td>
<td>Nombre del Cliente</td>
<td>Plan / Nombre Plan</td>
<td>Telefono</td>
<td>Propiedad Equipo</td>
<td>Fecha</td>
<td>Razon Movimiento</td>
<td>Plazo Forzoso</td>
<td>Descripción del Servicio</td>
<td>Descuento Multirenta</td>
<td>Renta sin impuestos</td>
<td>Descuento Adicional</td>
<td>Descuento de Servicio</td>
<td style="background-color:#ffee02">Commission&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td style="background-color:#ffee02">Commission Supervisor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td style="background-color:#ffee02">Commission Padrino Lead&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td style="background-color:#ffee02">Padrino Lead&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>Renta</td>
<td>Tipo de Periodo</td>
<td>Esquema (Solo aplica en marcas legado)</td>
<td>Sistema Origen</td>
<td>Marca</td>
<td>Performance</td>
<td>Subcategoria</td>
</tr>
<?php

foreach ($query as $transaccion) {
	?>
	<tr>
	<td>{{$transaccion->vendedor}}</td>
	<td>{{$transaccion->c_tipo}}</td>
	<td>{{$transaccion->c_periodo}}</td>
	<td>DISTRIBUIDORES</td>
	<td>TV TELCO & DATA SOLUTIONS SA DE CV</td>
	<td>108249</td>
	<td>{{$transaccion->c_cuenta}}</td>
	<td>{{$transaccion->c_contrato}}</td>
	<td>{{$transaccion->c_cliente}}</td>
	<td>{{$transaccion->c_plan}}</td>
	<td>{{$transaccion->c_dn}}</td>
	<td>{{$transaccion->c_propiedad}}</td>
	<td>{{$transaccion->fecha}}</td>
	<td>{{$transaccion->tipo}}</td>
	<td>{{$transaccion->c_plazo}}</td>
	<td>EMPRESAS</td>
	<td>{{$transaccion->c_descuento_multirenta}}</td>
	<td>${{number_format($transaccion->c_renta/1.16/1.03,2)}}</td>
	<td>{{$transaccion->c_afectacion_comision}}</td>
	<td>TV TELCO & DATA SOLUTIONS SA DE CV</td>
	<td style="background-color:#f3e848">{{$transaccion->user_id==$empleado->id?$transaccion->upfront:0}}</td>
	<td style="background-color:#f3e848">{{$transaccion->upfront_supervisor}}</td>
	<td style="background-color:#f3e848">{{$transaccion->padrino}}</td>
	<td style="background-color:#f3e848">
		@php
			if($transaccion->padrino>0)
			{
				echo $usuarios[$transaccion->padrino_lead];
			}
		@endphp
	</td>
	<td>${{$transaccion->c_renta}}</td>
	<td>Monthly</td>
	<td>ESPECIAL_442545</td>
	<td>NEXTEL</td>
	<td>IUSACELL</td>
	<td>$0</td>
	<td>{{$transaccion->tipo}}</td>
	</tr>
<?php
}
?>
	<tr>
		<td colspan=19 rowspan=6></td>
		<td><b>Subtotal</td>
		<td colspan=4>${{number_format($pago->comision_nuevas+$pago->comision_adiciones+$pago->comision_renovaciones+$pago->c_addons+$pago->leads,2)}}</td>
	</tr>
	@if(intval($pago->residual)>0)
	<tr>
		
		<td><b>Residual</td>
		<td>${{number_format($pago->residual,2)}}</td>
	</tr>
	@endif
	@if(intval($pago->anticipos_extraordinarios)>0)
	<tr>
		
		<td><b>Anticipo extraordinario</td>
		<td>-${{number_format($pago->anticipos_extraordinarios,2)}}</td>
	</tr>
	@endif
	@if(intval($pago->anticipo_ordinario)>0)
	<tr>
		
		<td><b>Anticipo ordinario</td>
		<td>-${{number_format($pago->anticipo_ordinario,2)}}</td>
	</tr>
	@endif
	<tr>
		
		<td><b>Charge Back</td>
		<td colspan=4>${{number_format($pago->charge_back,2)}}</td>
	</tr>
	<tr>
		
		<td><b>TOTAL</td>
		<td colspan=4>${{number_format($pago->total_pago,2)}}</td>
	</tr>
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