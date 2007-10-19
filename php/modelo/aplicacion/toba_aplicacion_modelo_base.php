<?php

class toba_aplicacion_modelo_base implements toba_aplicacion_modelo 
{
	protected $manejador_interface;
	
	/**
	 * @var toba_modelo_instalacion
	 */	
	protected $instalacion;

	/**
	 * @var toba_modelo_instancia
	 */
	protected $instancia;
	
	/**
	 * @var toba_modelo_proyecto
	 */
	protected $proyecto;
	
	/**
	 * Inicialización de la clase en el entorno consumidor
	 * @param toba_modelo_instalacion $instalacion Representante de la instalación de toba como un todo
	 * @param toba_modelo_instancia $instancia Representante de la instancia actualmente utilizada
	 * @param toba_modelo_proyecto $proyecto Representante del proyecto como un proyecto toba (sin la lógica de admin. de la aplicación)
	 */
	function set_entorno($manejador_interface, toba_modelo_instalacion $instalacion, toba_modelo_instancia $instancia, toba_modelo_proyecto $proyecto)
	{
		$this->manejador_interface = $manejador_interface;
		$this->instalacion = $instalacion;
		$this->instancia = $instancia;
		$this->proyecto = $proyecto;
	}
	
	/**
	 * Retorna la versión actualmente instalada de la aplicación (puede no estar migrada)
	 * @return toba_version
	 */
	function get_version_actual()
	{
		return $this->get_version_nueva();
	}

	/**
	 * Retorna la versión a la cual se debe migrar la aplicación (si ya esta migrada debería ser igual a la 'version_actual')
	 * @return toba_version
	 */	
	function get_version_nueva()
	{
		if (file_exists($this->proyecto->get_dir().'/VERSION')) {
			return new toba_version(file_get_contents($this->get_dir().'/VERSION'));
		} else {
			return $this->instalacion->get_version_actual();
		}
	}

	/**
	 * Toma como motor predefinido el mismo que el de la instalacion de toba
	 */
	function get_servidor_defecto()
	{
		$parametros = $this->instancia->get_parametros_db();
		if (isset($parametros['base'])) {
			unset($parametros['base']);
		}
		return $parametros;
	}	
	
	/**
	 * Determina si el modelo de datos se encuentra cargado en una conexión específica
	 * @param toba_db $base
	 */
	function estructura_creada(toba_db $base)
	{
		$tablas = $base->get_lista_tablas();
		return ! empty($tablas);
	}
	
	function crear_estructura(toba_db $base)
	{
		$estructura = $this->proyecto->get_dir().'/sql/estructura.sql';
		if (file_exists($estructura)) {
			$base->ejecutar_archivo($estructura);
		}
	}
	
	function cargar_datos_basicos(toba_db $base)
	{
		$datos = $this->proyecto->get_dir().'/sql/datos_basicos.sql';
		if (file_exists($datos)) {
			$base->ejecutar_archivo($datos);
		}		
	}
	
	/**
	 * @param array $datos_servidor Asociativo con los parámetros de conexión a la base
	 */
	function instalar($datos_servidor)
	{
		$version = $this->get_version_nueva();
		$fuentes = $this->proyecto->get_indice_fuentes();
		if (empty($fuentes)) {
			return;
		}
		$this->manejador_interface->titulo("Instalando proyecto de Referencia de Toba ".$version->__toString());		
		//--- Se asume que la base a instalar corresponde a la primer fuente
		$id_def_base = $this->proyecto->construir_id_def_base(current($fuentes));
		
		//--- Chequea si existe la entrada de la base de negocios en el archivo de bases
		if (! $this->instalacion->existe_base_datos_definida($id_def_base)) {
			if (! isset($datos_servidor['base'])) {
				$id_base = $this->proyecto->get_id().'_'.$version->get_string_partes();
				$datos_servidor['base'] = $id_base;
			}
			//-- Agrega la definición de la base
			$this->instalacion->agregar_db($id_def_base, $datos_servidor);
		}
		
		//--- Chequea si existe fisicamente la base creada
		if (! $this->instalacion->existe_base_datos($id_def_base)) {
			$this->instalacion->crear_base_datos($id_def_base);
		}
		
		$base = $this->instalacion->conectar_base($id_def_base);	
		$base->abrir_transaccion();		
		$base->retrazar_constraints();
		//--- Creación de la estructura
		if (! $this->estructura_creada($base)) {
			$this->manejador_interface->mensaje('Creando estructura', false);
			$this->manejador_interface->progreso_avanzar();			
			$this->crear_estructura($base);
			$this->manejador_interface->progreso_fin();
			
			$this->manejador_interface->mensaje('Cargando datos básicos', false);
			$this->manejador_interface->progreso_avanzar();			
			$this->cargar_datos_basicos($base);
			$this->manejador_interface->progreso_fin();			
		} else {
			$this->manejador_interface->mensaje('Ya esta creada la estructura de datos.');
		}
		$base->cerrar_transaccion();
	}


	/**
	 * Ejecuta los scripts de migración entre dos versiones específicas del sistema
	 * @param toba_version $desde
	 * @param toba_version $hasta
	 */
	function migrar(toba_version $desde, toba_version $hasta)
	{
		
	}
		
}

?>