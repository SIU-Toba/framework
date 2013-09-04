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
	
	/**
	 * Elimina datos de auditoria en un rango de tiempo 
	 */
	function opcion__purgar_auditoria()
	{
		$tiempo = $this->manejador_interface->dialogo_ingresar_texto('Ingrese el periodo de datos a mantener (meses)', false);
		$this->modelo->purgar_auditoria($tiempo);
	}
	
	/**
	 * Hace compatible la estructura del esquema con los cambios en la version 2.4.0
	 */
	function opcion__migrar_auditoria_2_4()
	{
		$this->modelo->migrar_auditoria_2_4();				//Modifico la estructura de las tablas
		$this->modelo->crear_auditoria();					//Regenero los triggers y SPs
	}
}

?>