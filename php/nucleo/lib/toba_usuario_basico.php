<?php
require_once('toba_usuario.php');

/**
 * Usuario estandar de la instancia
 * @package Centrales
 */
class toba_usuario_basico extends toba_usuario
{
	protected $datos_basicos;
	protected $grupo_acceso;

	/**
	*	Realiza la autentificacion.
	*	@return $value	Retorna TRUE o FALSE de acuerdo al estado de la autentifiacion
	*/
	static function autenticar($id_usuario, $clave, $datos_iniciales=null)
	{
		$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
		if( empty($datos_usuario) ) {
			return false; 		// No existe el usuario
		} else {
			if( $datos_usuario['autentificacion'] == 'md5' ) $clave = md5($clave);
			if( !($datos_usuario['clave'] === $clave) ) {
				return false;	// Clave INCORRECTA!
			}
		}
		return true;
	}
	//----------------------------------------------------------------------------------
	
	function __construct($id_usuario)
	{
		$this->datos_basicos = toba::instancia()->get_info_usuario($id_usuario);
		$this->grupo_acceso = toba::instancia()->get_grupo_acceso( $id_usuario, toba::proyecto()->get_id() );
	}

	/**
	*	Retorna el identificador del usuario
	*/
	function get_id()
	{
		return $this->datos_basicos['id'];
	}

	/**
	*	Retorna el nombre del usuario
	*/
	function get_nombre()
	{
		return $this->datos_basicos['nombre'];
	}
	
	/**
	*	Retorna un array de grupos de acceso para el proyecto actual
	*	@return $value	Retorna un array de grupos de acceso
	*/
	function get_grupo_acceso()
	{
		return $this->grupo_acceso;
	}

	/*
	*	Devuelve el valor contenido en el parametro de usuario especificado.
	*	@param 	$parametro	char	Identificador del parametro de usuario, las opciones validas son 'A','B' o 'C'
	*	@return $value	Retorna el valor del parametro o null si es que no se encuentra seteado.
	*/
	function get_parametro($parametro)
	{
		$parametro = strtolower(trim($parametro));
		if ( !($parametro=='a'||$parametro=='b'||$parametro=='c') ) {
			throw new toba_error("Consulta de parametro de usuario: El parametro '$parametro' es invalido.");	
		}
		//Las opciones correctas son 'a','b' o 'c'
		$nombre_parametro = 'parametro_'. $parametro;	
		if (isset($this->datos_basicos[$nombre_parametro])){
			return $this->datos_basicos[$nombre_parametro];
		}else{
			return null;
		}
	}
}
?>