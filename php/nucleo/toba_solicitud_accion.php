<?php

/**
 * Solicitud que utiliza un script php plano para su salida, no tiene acceso al esquema 
 * de menu, tipo de pagina, vinculos o componentes.
 * 
 * @package Centrales
 */
class toba_solicitud_accion extends toba_solicitud
{

	function __construct($info)
	{
		$this->info = $info;
		parent::__construct(toba::memoria()->get_item_solicitado(), toba::usuario()->get_id());
	}	
	
	function procesar()
	{
		$accion = $this->info['basica']['item_act_accion_script'];
		if (trim($accion) != '') {
			include($accion);
		} else {
			throw new toba_error_def("La solicitud_accion requiere la definici�n de un archivo php plano para ejecutar");
		}
	}
	
}

?>