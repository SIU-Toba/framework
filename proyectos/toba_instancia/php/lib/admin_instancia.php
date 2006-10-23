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
	
	static function ref()
	{
		if (!isset(self::$instanciacion)) {
			self::$instanciacion = new admin_instancia();	
		}
		return self::$instanciacion;	
	}
}
?>