<?php
class toba_ei_calendario_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "Calendario";		
	}
	

	function get_nombre_instancia_abreviado()
	{
		return "calendario";	
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
		$eventos['seleccionar_dia']['parametros'] = array('$dia');
		$eventos['seleccionar_dia']['comentarios'] = array("Indica que el usuario selecciono un d�a puntual del calendario","@param array \$dia Asociativo con los campos 'dia', 'mes' y 'anio'");
		$eventos['seleccionar_semana']['parametros'] = array('$semana');
		$eventos['seleccionar_semana']['comentarios'] = array("Indica que el usuario selecciono una semana puntual del calendario","@param array \$semana Asociativo con los campos 'semana' y 'anio'");
		$eventos['cambiar_mes']['parametros'] = array('$mes');
		$eventos['cambiar_mes']['comentarios'] = array("Indica que el usuario cambio el mes/a�o actual","@param array \$mes Asociativo conteniendo el 'mes' y el 'anio'");
		return $eventos;
	}
	
	function get_comentario_carga()
	{
		return array(
			"Permite cambiar la configuraci�n del calendario previo a la generaci�n de la salida",
			"El formato de carga a trav�s del m�todo set_datos es un recordset con columnas dia y contenido",
		);
	}		
}
?>