<?php
require_once('toba_interface_usuario.php');

class toba_usuario implements toba_interface_usuario
{
	static private $instancia;

	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_usuario();	
		}
		return self::$instancia;	
	}

	protected function __construct()
	{
		if (! self::cargado() ) {
			$_SESSION['toba']['usuario']['id'] = 'publico';
			$_SESSION['toba']['usuario']['nombre'] = 'Usuario Publico';
			$_SESSION['toba']['usuario']['grupo_acceso'] = array();
		}
	}	

	function cargado()
	{
		return isset($_SESSION['toba']['usuario']);	
	}

	/**
	*	Carga los datos basicos del usuario
	*/
	function cargar($id_usuario, $clave=null)
	{
		$this->autenticar($id_usuario, $clave);
		$_SESSION['toba']['usuario'] = toba_instancia::get_info_usuario($id_usuario);
	}
	
	/**
	*	Autentifica al usuario
	*/
	function autenticar($id_usuario, $clave)
	{
		$datos_usuario = toba_instancia::get_info_autenticacion($id_usuario);
		if( empty($datos_usuario) ) {
			// El usuario no existe	
			throw new toba_excepcion_login("La combinacion usuario/clave es incorrecta.");
		}
		if (!isset($clave)) {
			if (!(toba_proyecto::instancia()->get_parametro('validacion_debug'))) {
				throw new toba_excepcion_login("Es necesario ingresar la clave.");	
			}
		} else {
			if( $datos_usuario['autentificacion'] == 'md5' ) $clave = md5($clave);
			if( !($datos_usuario['clave'] === $clave) ) {
				// Error de login
				throw new toba_excepcion_login("La combinacion usuario/clave es incorrecta.");
			}
		}
	}

	/**
	*	Retorna el identificador del usuario
	*/
	function get_id()
	{
		if($this->cargado()){
			return $_SESSION['toba']['usuario']['id'];
		} else {
			throw new toba_excepcion_usuario("No hay un usuario cargado!");
		}
	}

	/**
	*	Retorna el nombre del usuario
	*/
	function get_nombre()
	{
		if($this->cargado()){
			return $_SESSION['toba']['usuario']['nombre'];
		}else{
			throw new toba_excepcion_usuario("No hay un usuario cargado!");
		}
	}
	
	/**
	*	Retorna el identificador del grupo de acceso para el PROYECTO ACTUAL
	*/
	function get_grupo_acceso()
	{
		if($this->cargado()){
			$proyecto = toba_proyecto::get_id();
			if (!isset($_SESSION['toba']['usuario']['grupo_acceso'][$proyecto])) {
				$datos_grupo_acceso = toba_instancia::get_grupo_acceso( $this->get_id(), $proyecto );
				if(empty($datos_grupo_acceso)){
					throw new toba_excepcion_login("El usuario '".$this->get_id()."' no pertenece un grupo de acceso del proyecto '$proyecto'. ACCESO DENEGADO");
				}
				$_SESSION['toba']['usuario']['grupo_acceso'][$proyecto] = $datos_grupo_acceso[0]['grupo_acceso'];
			}
			return $_SESSION['toba']['usuario']['grupo_acceso'][$proyecto];
		}else{
			throw new toba_excepcion_usuario("No hay un usuario cargado!");
		}
	}

	function get_fecha_vencimiento()
	{
		if(isset($_SESSION['toba']['usuario']['fecha_vencimiento'])){
			return $_SESSION['toba']['usuario']['fecha_vencimiento'];
		}else{
			return null;
		}		
	}
	
	function get_horario_permitido()
	{
		if(isset($_SESSION['toba']['usuario']['horario_permitido'])){
			return $_SESSION['toba']['usuario']['horario_permitido'];
		}else{
			return null;
		}		
	}

	function get_dias_semana_permitidos()
	{
		if(isset($_SESSION['toba']['usuario']['dias_permitidos'])){
			return $_SESSION['toba']['usuario']['dias_permitidos'];
		}else{
			return null;
		}		
	}
		
	function get_ip_permitida()
	{
		if(isset($_SESSION['toba']['usuario']['ip_permitida'])){
			return $_SESSION['toba']['usuario']['ip_permitida'];
		}else{
			return null;
		}		
	}	
}
?>