<?php
require_once('lib/admin_instancia.php');

class sesion extends toba_sesion
{
	private $id_instancia;
	private $id_proyecto;
	private $id_proyecto_hint;
	private $proyecto_unico = false;

	//-------------------------------------------------------------
	//-- Ventanas toba -------------------------------------
	//-------------------------------------------------------------

	function conf__inicial($datos=null)
	{
		$this->id_instancia = $datos['instancia'];
		if (isset($datos['proyecto'])) {
			$this->id_proyecto = $datos['proyecto'];
			$this->proyecto_unico = true;
		}
		if(isset($datos['proyecto_hint'])){
			$this->id_proyecto_hint = $datos['proyecto_hint'];
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
		if (toba::memoria()->existe_dato_instancia('instancia') &&
				toba::memoria()->existe_dato_instancia('proyecto')) {
			$datos['instancia'] = toba::memoria()->get_dato_instancia('instancia');
			$datos['proyecto'] = toba::memoria()->get_dato_instancia('proyecto');
			$datos['proyecto_unico'] = true;
			// La salida de este metodo termina alimientando la entrada del conf__inicial
			return $datos;
		}
		if (toba::memoria()->existe_dato_instancia('instancia')) {
			$datos['instancia'] = toba::memoria()->get_dato_instancia('instancia');
			$datos['proyecto_hint'] = toba::memoria()->get_dato_instancia('proyecto_hint');
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

	/**
	 * Este ID determina si se editan usuarios/perfiles sobre un proyecto particular (o es multiproyecto si no está definido)
	 */
	function get_id_proyecto()
	{
		return $this->id_proyecto;
	}

	/**
	 * Este ID determina el proyecto a seleccionarse automaticamente en los filtros (cuando es multiproyecto)
	 */
	function get_id_proyecto_hint()
	{
		return $this->id_proyecto_hint;
	}

	function proyecto_esta_predefinido()
	{
		return $this->proyecto_unico;
	}
}
?>