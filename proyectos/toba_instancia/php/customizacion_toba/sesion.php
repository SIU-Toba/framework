<?php

class sesion extends toba_sesion
{
	private $id_intancia;

	function conf__inicial($datos=null)
	{
		if(!isset($datos)) {
			$datos = toba::memoria()->get_parametro('instancia');
			if( !isset($datos) ) {
				throw new toba_error('Error: No fue especificada la INSTANCIA a editar.');				
			}
		}
		$this->id_instancia = $datos;
	}

	function conf__activacion()
	{
		toba_contexto_info::set_db( admin_instancia::ref()->db() );
	}
	
	//-- API para el proyecto -------------------------------------
	
	function set_id_instancia($instancia)
	{
		$this->id_instancia = $instancia;	
	}

	function get_id_instancia()
	{
		return $this->id_instancia;	
	}
}
?>