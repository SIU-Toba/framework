<?php
/**
 * Clase que permite recuperar parametros de la instancia o el proyecto
 * @package Centrales
 */
class toba_parametros 
{	
	/**
	 * Devuelve un objeto para manipular la instancia
	 * @return mixed
	 */
	static protected function get_instancia() 
	{
		if (PHP_SAPI != 'cli') { 
			return toba::instancia();			
		}
		return toba_modelo_catalogo::instanciacion()->get_instancia(toba_instancia::get_id());				//Por si en algun momento se llega a usar desde el modelo
	}
	
	/**
	 * Devuelve un objeto que permite manipular el proyecto
	 * @param string $proyecto
	 * @return mixed
	 */
	static protected function get_proyecto($proyecto)
	{
		if (PHP_SAPI != 'cli') {
			return toba::proyecto($proyecto);
		}
		return toba_modelo_catalogo::instanciacion()->get_proyecto(self::get_instancia(), $proyecto);		//Por si en algun momento se llega a usar desde el modelo
	}
	
	/**
	 * Devuelve el valor del parametro solicitado
	 * @param string $proyecto
	 * @param string $parametro
	 * @param boolean $obligatorio
	 * @return mixed
	 */
	static function get_redefinicion_parametro($proyecto, $parametro, $obligatorio) 
	{
		$variable_instancia = self::get_instancia()->get_parametro_seccion_proyecto($proyecto, $parametro);
		$variable_proyecto = self::get_proyecto($proyecto)->get_parametro('proyecto', $parametro, $obligatorio);	
		if (! is_null($variable_instancia)) {
			$variable_proyecto = $variable_instancia;
		}
		return $variable_proyecto;
	}
	
	/**
	 * Devuelve el valor de un parametro runtime solicitado
	 * @param string $proyecto
	 * @param string $seccion
	 * @param string $parametro
	 * @param boolean $obligatorio
	 * @return mixed
	 */
	static function get_redefinicion_parametro_runtime($proyecto, $seccion,  $parametro= null, $obligatorio=true)
	{
		$variable_instancia = (! isset($parametro)) ? self::get_instancia()->get_parametro_seccion_proyecto($proyecto, $seccion): self::get_instancia()->get_parametro_seccion_proyecto($proyecto, $parametro);
		$variable_proyecto = toba::proyecto()->get_parametro($seccion, $parametro, $obligatorio);							//Uso el proyecto cargado actualmente, no pido ninguno en particular ya que esto sale del runtime
		if (! is_null($variable_instancia)) {
			$variable_proyecto = $variable_instancia;
		}
		return $variable_proyecto;		
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//								REDEFINICION VARIABLES EN PROYECTO.INI
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	
	/**
	 * Retorna el valor del parametro pwd_largo_minimo para el proyecto en cuestion
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_largo_pwd($proyecto)
	{		
		$largo_clave_instancia = self::get_instancia()->get_largo_minimo_password($proyecto);
		$largo_clave = self::get_proyecto($proyecto)->get_parametro('proyecto', 'pwd_largo_minimo', false);
		if ($largo_clave_instancia != apex_pa_pwd_largo_minimo || is_null($largo_clave)) {				//Si no esta seteado el parametro en proyecto.ini o el seteo de instancia es distinto al default.
			$largo_clave = $largo_clave_instancia;
		} 
		return $largo_clave;
	}

	/**
	 * Retorna el valor del parametro dias_minimos_validez_clave para el proyecto en cuestion
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_clave_validez_minima($proyecto)
	{
		return self::get_redefinicion_parametro($proyecto, 'dias_minimos_validez_clave', false);
	}
	
	/**
	 * Retorna el valor del parametro dias_validez_clave para el proyecto en cuestion
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_clave_validez_maxima($proyecto)
	{
		return self::get_redefinicion_parametro($proyecto, 'dias_validez_clave', false);
	}
	
	/**
	 * Retorna el valor del parametro claves_no_repetidas para el proyecto en cuestion
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_nro_claves_no_repetidas($proyecto) 
	{
		return self::get_redefinicion_parametro($proyecto, 'claves_no_repetidas', false);
	}
	
	
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//									REDEFINICION METADATOS PROYECTO
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * Retorna el tiempo de no interaccion maximo para que se pierda la sesion
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_session_no_interaccion($proyecto)	
	{
		return self::get_redefinicion_parametro_runtime($proyecto, 'sesion_tiempo_no_interac_min');
	}
	
	/**
	 * Retorna el maximo tiempo de sesion posible
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_session_tiempo_maximo($proyecto)
	{
		return self::get_redefinicion_parametro_runtime($proyecto, 'sesion_tiempo_maximo_min');
	}
	
	/**
	 * Retorna la cantidad de intentos de login necesarios antes de tomar una accion
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_intentos_validacion($proyecto)
	{
		return self::get_redefinicion_parametro_runtime($proyecto, 'validacion_intentos');
	}
	
	/**
	 *  Retorna el tipo de accion (codificado numericamente)  a tomar con el usuario si se sobrepasa el limite de intentos de login fallidos
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_debe_bloquear_usuario($proyecto) 
	{
		return self::get_redefinicion_parametro_runtime($proyecto, 'validacion_bloquear_usuario');
	}
	
	/**
	 * Retorna la ventana de tiempo en la que se deben producir los intentos de login fallido para tomar una accion
	 * @param string $proyecto
	 * @return integer
	 */
	static function get_ventana_intentos($proyecto)
	{
		return self::get_redefinicion_parametro_runtime($proyecto, 'validacion_intentos_min');
	}	
}
	