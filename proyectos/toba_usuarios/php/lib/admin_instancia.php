<?php

class admin_instancia
{
	static private $instanciacion;
	protected $id_instancia;
	protected $base;
	protected $proyectos;
	
	function __construct()
	{
		if ( toba_editor::activado() && toba_editor::get_proyecto_cargado() == 'toba_usuarios' ) {
			//Si la fuente del proyecto se utiliza desde el editor (levantar metadatos, etc) tengo que cargar
			// la instancia editada de otra manera...
			$this->id_instancia = toba_editor::get_id_instancia_activa();
		} else {
			$this->id_instancia = toba::sesion()->get_id_instancia();
		}
		$datos = toba_instancia::get_datos_instancia($this->id_instancia);
		$this->base = $datos['base'];
		$this->proyectos = explode(',', $datos['proyectos']);
		$this->proyectos = array_map('trim', $this->proyectos);
	}

	function validar_estructura_instancia()
	{
		
	}

	function get_lista_proyectos()
	{
		return $this->proyectos;
	}

	function get_id_db()
	{
		return $this->base;
	}
	
	function db()
	{
		return toba_dba::get_db($this->base);
	}
	
	static function ref($recargar=false)
	{
		if (!isset(self::$instanciacion) || $recargar ) {
			self::$instanciacion = new admin_instancia();	
		}
		return self::$instanciacion;	
	}
	
	//------------------------------------------------------------
	//-- Manejo del bloqueo de IPs
	//------------------------------------------------------------
	
	function get_lista_ips_rechazadas()
	{
		$sql = "SELECT momento, ip FROM apex_log_ip_rechazada;";
		return toba::db()->consultar($sql);
	}

	function get_lista_usuarios_bloqueados($estado)
	{
		/*No se bloquea el usuario toba pero que pasa si se modifica 
		el id del mismo en el ABM de usuarios??*/
		$sql = "SELECT 
					usuario, nombre 
				FROM 
					apex_usuario 
				WHERE 
						bloqueado = '$estado'
					AND	usuario <> 'toba';";
		return toba::db()->consultar($sql);
	}
	
	function eliminar_bloqueo($ip)
	{
		$sql = "DELETE FROM apex_log_ip_rechazada WHERE ip = '$ip';";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_bloqueos()
	{
		$sql = "DELETE FROM apex_log_ip_rechazada;";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_bloqueo_usuario($usuario)
	{
		$sql = "UPDATE apex_usuario SET bloqueado = 0 WHERE usuario = '$usuario';";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_bloqueo_usuarios()
	{
		$sql = "UPDATE apex_usuario SET bloqueado = 0 WHERE bloqueado = 1;";
		toba::db()->ejecutar($sql);	
	}
	
	function agregar_bloqueo_usuario($usuario)
	{
		$sql = "UPDATE apex_usuario SET bloqueado = 1 WHERE usuario = '$usuario';";
		toba::db()->ejecutar($sql);
	}	
	
	function agregar_bloqueo_usuarios()
	{
		$sql = "UPDATE apex_usuario SET bloqueado = 1 WHERE bloqueado = 0;";
		toba::db()->ejecutar($sql);	
	}
	
}
?>