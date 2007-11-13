<?php

class toba_aplicacion_comando_base implements toba_aplicacion_comando
{
	/**
	 * toba_aplicacion_modelo_base
	 */
	protected $modelo;
	protected $manejador_interface;
	
	function set_entorno($manejador_interface, toba_aplicacion_modelo $modelo)
	{
		$this->manejador_interface = $manejador_interface;
		$this->modelo = $modelo;
	}
	
	/**
	 * Crea la base de negocios del proyecto
	 */
	function opcion__instalar()
	{
		$parametros = $this->modelo->get_servidor_defecto();
		$this->modelo->instalar($parametros);
	}

	/**
	 * Migra una instalación previa del proyecto
	 */	
	function opcion__migrar()
	{
		$desde = $this->modelo->get_version_actual();
		$hasta = $this->modelo->get_version_nueva();
		$this->modelo->migrar($desde, $hasta);
	}
		
}

?>