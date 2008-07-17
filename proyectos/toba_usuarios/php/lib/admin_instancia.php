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
	
	
	function chequear_usar_perfiles_propios($id_proyecto, toba_ei_pantalla $pantalla)
	{
		//-- Si es una instalación de producción avisar que los cambios se aplicaran solo a esta instalacion y no al proyecto/personalizacion
		$id_instancia = toba::instancia()->get_id();
		$instancia = toba_modelo_catalogo::instanciacion()->get_instancia($id_instancia);
		$usa_perfiles_propios = $instancia->get_proyecto_usar_perfiles_propios($id_proyecto);		
		if (toba::instalacion()->es_produccion() && ! $usa_perfiles_propios) {
			$msg = 'ATENCION! Al realizar cambios a los perfiles los mismos quedarán disponibles únicamente para la instalación actual.';
			$pantalla->set_descripcion($msg, 'warning');
		}		
	}
	
	function set_usar_perfiles_propios($id_proyecto)
	{
		//-- Si estamos en produccion guardamos un flag indicando que cambio la instancia
		$id_instancia = toba::instancia()->get_id();
		$instancia = toba_modelo_catalogo::instanciacion()->get_instancia($id_instancia);
		$usa_perfiles_propios = $instancia->get_proyecto_usar_perfiles_propios($id_proyecto);
		if (toba::instalacion()->es_produccion() && !$usa_perfiles_propios) {
			$instancia->set_proyecto_usar_perfiles_propios($id_proyecto, true);
		}		
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