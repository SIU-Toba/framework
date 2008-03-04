<?php

class sesion extends toba_sesion
{
	private $id_instancia;

	//-- Ventanas toba -------------------------------------

	function conf__inicial($datos=null)
	{
		/* Esquema multiinstancia
		if(!isset($datos)) {
			$datos = toba::memoria()->get_parametro('instancia');
			if( !isset($datos) ) {
				throw new toba_error('Error: No fue especificada la INSTANCIA a editar.');				
			}
		}
		$this->id_instancia = $datos;
		*/
		$this->id_instancia = toba::instancia()->get_id();
	}

	function conf__final()
	{
		// Me abrieron desde el ADMIN
		if ( toba::memoria()->existe_dato_instancia('instancia') ) {
			echo toba_js::ejecutar('window.close();');
		}
	}

	function conf__activacion()
	{
		toba_contexto_info::set_db( admin_instancia::ref()->db() );
	}

	function ini__automatica() 
	{
		if ( toba::memoria()->existe_dato_instancia('instancia') ) {
			return toba::memoria()->get_dato_instancia('instancia');
		}
		throw new toba_error_ini_sesion('El ID de la instancia a editar no esta registrado en la memoria global de la instancia');
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