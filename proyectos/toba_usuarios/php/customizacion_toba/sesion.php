<?php

class sesion extends toba_sesion
{
	private $id_instancia;
	private $id_proyecto;
	private $proyecto_unico = false;

	//-------------------------------------------------------------
	//-- Ventanas toba -------------------------------------
	//-------------------------------------------------------------

	function conf__inicial($datos=null)
	{
		$this->id_instancia = $datos['instancia'];
		$this->id_proyecto = $datos['proyecto'];
		if(isset($datos['proyecto_unico'])){
			$this->proyecto_unico = true;	
		}
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

	/**
		Este metodo se llama si existe una sesion en la instancia pero el proyecto no esta iniciado
		Intenta recuperar la informacion de inicio, si dispara una excepcion, salta al item de inicializacion de sesion
	*/
	function ini__automatica() 
	{
		if ( toba::memoria()->existe_dato_instancia('instancia') && 
				toba::memoria()->existe_dato_instancia('proyecto')) {
			$datos['instancia'] = toba::memoria()->get_dato_instancia('instancia');
			$datos['proyecto'] = toba::memoria()->get_dato_instancia('proyecto');
			$datos['proyecto_unico'] = true;
			// La salida de este metodo termina alimientando la entrada del conf__inicial
			return $datos;
		}
		// Me faltan parametros para iniciar al proyecto, muestro el item de inicializacion de sesion
		throw new toba_error_ini_sesion('El ID de la INSTANCIA y PROYECTO a editar no esta registrado en la memoria global de la instancia');
	}
	
	//-------------------------------------------------------------
	//-- API para el proyecto -------------------------------------
	//-------------------------------------------------------------
	
	function get_id_instancia()
	{
		return $this->id_instancia;	
	}

	function get_id_proyecto()
	{
		return $this->id_proyecto;	
	}
	
	function proyecto_esta_predefinido()
	{
		return $this->proyecto_unico;	
	}
}
?>