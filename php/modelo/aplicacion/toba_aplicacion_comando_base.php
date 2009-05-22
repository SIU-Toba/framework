<?php

class toba_aplicacion_comando_base implements toba_aplicacion_comando
{
	/**
	 * toba_aplicacion_modelo_base
	 */
	protected $modelo;
	
	/**
	 * @var toba_mock_proceso_gui
	 */
	protected $manejador_interface;
	
	function set_entorno($manejador_interface, toba_aplicacion_modelo $modelo)
	{
		$this->manejador_interface = $manejador_interface;
		$this->modelo = $modelo;
	}
	
	/**
	 * Crea la base de negocios del proyecto
	 */
	function opcion__instalar($parametros)
	{
		$base = $this->modelo->get_servidor_defecto();
		$this->modelo->instalar($base);
	}

	/**
	 * Migra una instalacion previa del proyecto
	 */	
	function opcion__migrar($parametros)
	{
		$desde = $this->modelo->get_version_actual();
		$hasta = $this->modelo->get_version_nueva();
		$this->modelo->migrar($desde, $hasta);
	}

	/**
	 * Crea o actualiza el esquema de auditoria sobre las tablas del negocio
	 */
	function opcion__crear_auditoria()
	{
		$this->modelo->crear_auditoria();
	}	
	
	/**
	 * Borra el esquema de auditoria
	 */
	function opcion__borrar_auditoria()
	{
		$this->modelo->borrar_auditoria();
	}		
		
}

?>