<?php

class RDIServicioRecursoPersonalReciboSueldo extends RDIServicioRecursoPersonal
{
	function getTipo()
	{
		return RDITipos::RECIBOSUELDO;
	}
	
	function getParametrosObligatorios()
	{
		$parametros = parent::getParametrosObligatorios();
		//$parametros['rdirprs:legajo'] = 'legajo';
		return $parametros;
	}
	
	function getParametrosOpcionales()
	{
		$parametros = parent::getParametrosOpcionales();
        $parametros['rdirprs:numeroLegajo'] = 'numeroLegajo';
        $parametros['rdirprs:numeroLiquidacion'] = 'numeroLiquidacion';
        $parametros['rdirprs:descripcionLiquidacion'] = 'descripcionLiquidacion';
        $parametros['rdirprs:numeroRecibo'] = 'numeroRecibo';        
		$parametros['rdirprs:anio'] = 'anio';
		$parametros['rdirprs:mes'] = 'mes';
		$parametros['rdirprs:firmado'] = 'firmado';
		//$parametros['rdirprs:firmaEmpleador'] = 'firmaEmpleador';
		//$parametros['rdirprs:firmaEmpleado'] = 'firmaEmpleado';
		return $parametros;
	}
	
	function getNombre($parametros)
	{
		return $parametros['numeroLegajo'] .' - '. $parametros['numeroRecibo'];
	}
}