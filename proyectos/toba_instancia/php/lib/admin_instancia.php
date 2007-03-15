<?php

class admin_instancia
{
	static private $instanciacion;
	protected $id_instancia;
	protected $base;
	protected $proyectos;
	
	private function __construct()
	{
		$this->id_instancia = toba::sesion()->get_id_instancia();
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
}
?>