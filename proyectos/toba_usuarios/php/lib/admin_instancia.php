<?php

class admin_instancia
{
	static private $instanciacion;
	protected $id_instancia;
	protected $base;
	protected $proyectos;
	
	private function __construct()
	{
		$this->id_instancia = toba::instancia()->get_id();
		$datos = toba_instancia::get_datos_instancia($this->id_instancia);
		$this->base = $datos['base'];
	}

	function validar_estructura_instancia()
	{
		
	}

	static function get_proyecto_defecto()
	{
		$proyecto = toba::memoria()->get_dato_instancia('proyecto');
		if (isset($proyecto)) {
			return $proyecto;
		}
		$proyectos = toba::manejador_sesiones()->get_proyectos_activos();
		if (count($proyectos) > 1) {
			foreach ($proyectos as $proyecto) {
				if ($proyecto != toba::proyecto()->get_id()) {
					return $proyecto;
				}
			}
		}
	}

	function get_id_db()
	{
		return $this->base;
	}
	
	function db()
	{
		return toba::instancia()->get_db();
	}
	
	static function ref($recargar=false)
	{
		if (!isset(self::$instanciacion) || $recargar ) {
			self::$instanciacion = new admin_instancia();	
		}
		return self::$instanciacion;	
	}
	
	
	static function chequear_usar_perfiles_propios($id_proyecto, toba_ei_pantalla $pantalla)
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
	
	static function set_usar_perfiles_propios($id_proyecto)
	{
		//-- Si estamos en produccion guardamos un flag indicando que cambio la instancia
		$id_instancia = toba::instancia()->get_id();
		$instancia = toba_modelo_catalogo::instanciacion()->get_instancia($id_instancia);
		$usa_perfiles_propios = $instancia->get_proyecto_usar_perfiles_propios($id_proyecto);
		if (toba::instalacion()->es_produccion()) {
			if (!$usa_perfiles_propios) {
				$instancia->set_proyecto_usar_perfiles_propios($id_proyecto, true);
			}
			//-- Re-Compilamos los metadatos de perfiles 
			$instancia->get_proyecto($id_proyecto)->compilar_metadatos_generales_grupos_acceso(true);
		}		
	}
	
	//------------------------------------------------------------
	//-- Manejo del bloqueo de IPs
	//------------------------------------------------------------
	
	static function get_lista_ips_rechazadas()
	{
		$schema_logs = toba::instancia()->get_db()->get_schema(). '_logs';
		$sql = "SELECT momento, ip FROM $schema_logs.apex_log_ip_rechazada;";
		return toba::db()->consultar($sql);
	}

	static function get_lista_usuarios_bloqueados($estado)
	{
		$estado = quote($estado);
		$sql = "SELECT 
					usuario, nombre 
				FROM 
					apex_usuario 
				WHERE 
						bloqueado = $estado";
		return toba::db()->consultar($sql);
	}
	
	function eliminar_bloqueo($ip)
	{
		$ip = quote($ip);
		$schema_logs = toba::instancia()->get_db()->get_schema(). '_logs';
		$sql = "DELETE FROM $schema_logs.apex_log_ip_rechazada WHERE ip = $ip";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_bloqueos()
	{
		$schema_logs = toba::instancia()->get_db()->get_schema(). '_logs';
		$sql = "DELETE FROM $schema_logs.apex_log_ip_rechazada;";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_bloqueo_usuario($usuario)
	{
		$usuario = quote($usuario);
		$sql = "UPDATE apex_usuario SET bloqueado = 0 WHERE usuario = $usuario";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_bloqueo_usuarios()
	{
		$sql = 'UPDATE apex_usuario SET bloqueado = 0 WHERE bloqueado = 1';
		toba::db()->ejecutar($sql);	
	}
	
	function agregar_bloqueo_usuario($usuario)
	{
		$usuario = quote($usuario);
		$sql = "UPDATE apex_usuario SET bloqueado = 1 WHERE usuario = $usuario";
		toba::db()->ejecutar($sql);
	}	
	
	function agregar_bloqueo_usuarios()
	{
		$sql = 'UPDATE apex_usuario SET bloqueado = 1 WHERE bloqueado = 0';
		toba::db()->ejecutar($sql);	
	}
	
}
?>