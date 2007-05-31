<?php
class toba_ei_calendario_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "Calendario";		
	}
	
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
	
	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		$eventos['seleccionar_dia']['parametros'] = array($dia);
		$eventos['seleccionar_dia']['comentarios'] = array("Arreglo asociativo con los campos 'dia', 'mes' y 'anio'");
		$eventos['seleccionar_semana']['parametros'] = array($semana);
		$eventos['seleccionar_semana']['comentarios'] = array("Arreglo asociativo con los campos 'semana' y 'anio'");
		$eventos['cambiar_mes']['parametros'] = array($mes);
		$eventos['cambiar_mes']['comentarios'] = array("Arreglo asociativo conteniendo el 'mes' y el 'anio'");
		return $eventos;
	}
}
?>