<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$distribuidor->name.".xls");
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
<td>Concepto</td>
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
	<td style="background-color:#f3e848">${{$transaccion->upfront}}</td>
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
if(($distribuidor->detalles->adelanto==1 && $version==1) || $version==2)
{
?>
	<tr>
		<td colspan=20 rowspan=7></td>
		<td><b>Subtotal</td>
		<td>${{number_format($pago->comision_nuevas+$pago->comision_adiciones+$pago->comision_renovaciones+$pago->c_addons,2)}}</td>
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
	@if(intval($pago->pagos_a_cuenta)>0)
	<tr>
		
		<td><b>Pagos a cuenta de comisiones</td>
		<td>-${{number_format($pago->pagos_a_cuenta,2)}}</td>
	</tr>
	@endif
	@if(intval($pago->anticipo_ordinario)>0)
	<tr>
		
		<td><b>Anticipo ordinario</td>
		<td>-${{number_format($pago->anticipo_ordinario,2)}}</td>
	</tr>
	@endif
	@if(!empty($retroactivos))
	<tr>
		
		<td><b>Retroactivos</td>
		<td>${{number_format($pago->retroactivos_reproceso,2)}}</td>
	</tr>
	@endif
	<tr>
		<td><b>Charge Back</td>
		<td>-${{number_format($pago->charge_back,2)}}</td>
	</tr>
	<tr>
		
		<td><b>TOTAL</td>
		<td>${{number_format($pago->total_pago,2)}}</td>
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
	<td><b>Vendedor</td>
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
			<td>{{$transaccion->vendedor}}</td>
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
@if(!empty($query_addons_faltantes))
<br>
<br>
<br>
<span style="color:rgb(10, 71, 161);font-size:40px;">ADDONS CONTROL ESPERADOS NO PAGADOS POR AT&T</span>
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
	<td><b>Folio</td>
	<td><b>Ciudad</td>
	<td><b>Plan</td>
	<td><b>Renta</td>
	<td><b>Equipo</td>
	<td><b>Plazo</td>
	<td><b>Descuento_multirenta</td>
	<td><b>Afectacion_comision</td>
	<td style="background-color:#FF0000;color:#FFFFFF"><b>Renta ADDON CONTROL Faltante</td>
	<td style="background-color:#00FF00;color:#000000"><b>COMISION ADDON CONTROL Faltante</td>
	<td style="background-color:#00FF00;color:#000000"><b>Factor de pago</td>
	</tr>
	<?php
	
	foreach ($query_addons_faltantes as $transaccion) {
		?>
		<tr>
			<td>{{$distribuidor->name}}</td>
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
			<td>{{$transaccion->renta_faltante}}</td>
			@php
			$factor_linea=0;
			$factor_linea=$transaccion->upfront/((($transaccion->renta-$transaccion->renta_faltante)/1.16/1.03)*(1-$transaccion->descuento_multirenta/100)*(1-$transaccion->afectacion_comision/100));
			@endphp
			<td>{{number_format($factor_linea*(($transaccion->renta_faltante/1.16/1.03)*(1-$transaccion->descuento_multirenta/100)*(1-$transaccion->afectacion_comision/100)),2)}}</td>
			<td>{{number_format($factor_linea,2)}}</td>

		</tr>
	<?php
	}
	?>
	</table>
@endif
@if(!empty($retroactivos))
<br>
<br>
<br>
<span style="color:rgb(10, 71, 161);font-size:40px;">RETROACTIVOS PERIODOS ANTERIORES</span>
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
	<td><b>Folio</td>
	<td><b>Ciudad</td>
	<td><b>Plan</td>
	<td><b>Renta</td>
	<td><b>Equipo</td>
	<td><b>Plazo</td>
	<td><b>Descuento_multirenta</td>
	<td><b>Afectacion_comision</td>
	<td style="background-color:#FF0000;color:#FFFFFF"><b>RETROACTIVO</td>
	<td style="background-color:#FF0000;color:#FFFFFF"><b>COMENTARIO</td>
	</tr>
	<?php
	
	foreach ($retroactivos as $transaccion) {
		?>
		<tr>
			<td>{{$transaccion->user_origen->name}}</td>
			<td>{{$transaccion->venta->fecha}}</td>
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
			<td>{{$transaccion->callidus->descuento_multirenta}}</td>
			<td>{{$transaccion->callidus->afectacion_comision}}</td>
			<td>{{$transaccion->retroactivo}}</td>
			<td>{{$transaccion->comentario}}</td>

		</tr>
	<?php
	}
	?>
	</table>
@endif