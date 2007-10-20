<?php

class toba_aplicacion_modelo_base implements toba_aplicacion_modelo 
{
	protected $permitir_exportar_modelo = true;
	
	/**
	 * @var toba_proceso_gui
	 */
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
			return new toba_version(file_get_contents($this->proyecto->get_dir().'/VERSION'));
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
	
	
	function cargar_modelo_datos($base)
	{
		$base->abrir_transaccion();		
		$base->retrazar_constraints();
		$this->crear_estructura($base);
		$this->cargar_datos($base);
		$base->cerrar_transaccion();			
	}
	
	/**
	 * @todo No esta soportada la exportación de los datos en Windows cuando el usuario de postgres requiere clave
	 */
	function regenerar_modelo_datos($base, $id_def_base)
	{
		if (! $this->permitir_exportar_modelo) {
			$this->manejador_interface->mensaje('Ya existe un modelo de datos del proyecto cargado previamente.');
			return;
		}
		$reemplazar = $this->manejador_interface->dialogo_simple("Ya existe el modelo de datos, ".
							"¿Desea reemplazarlo? (borra la base completa y la vuelva a cargar)", 's');
		if (! $reemplazar) {
			return;
		}
		$exportar = $this->manejador_interface->dialogo_simple("¿Desea exportar los datos actuales?", 's');
		if ($exportar) {
			$parametros = $this->instalacion->get_parametros_base($id_def_base);
			$archivo = $this->proyecto->get_dir().'/sql/datos_locales.sql';
			if (toba_manejador_archivos::es_windows()) {
				$comando = "pg_dump -d -a -h {$parametros['profile']} -U {$parametros['usuario']} -f \"$archivo\" {$parametros['base']}";
			} else {
				$clave = '';
				if ($parametros['clave'] != '') {
					$clave = "export PGPASSWORD=".$parametros['clave'].';';
				}					
				$comando = $clave."pg_dump -d -a -h {$parametros['profile']} -U {$parametros['usuario']} -f '$archivo' {$parametros['base']}";
			}
			$this->manejador_interface->mensaje("Ejecutando: $comando");
			$salida = array();
			echo exec($comando, $salida, $exito);
			echo implode("\n", $salida);
			if ($exito > 0) {
				throw new toba_error('No se pudo exportar correctamente los datos');
			}
		}
		
		//--- Borra la base fisicamente
		$this->manejador_interface->mensaje('Borrando base actual', false);
		$base->destruir();
		unset($base);
		$this->instalacion->borrar_base_datos($id_def_base);
		$this->instalacion->crear_base_datos($id_def_base);
		$this->manejador_interface->progreso_avanzar();
		$this->manejador_interface->progreso_fin();		
		
		//--- Carga nuevamente el modelo de datos
		$base = $this->instalacion->conectar_base($id_def_base);
		$this->cargar_modelo_datos($base);	

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
			$this->manejador_interface->mensaje('Creando estructura', false);
			$this->manejador_interface->progreso_avanzar();	
			$base->ejecutar_archivo($estructura);
			$this->manejador_interface->progreso_fin();
		}
	}

	
	function cargar_datos(toba_db $base)
	{
		$locales =  $this->proyecto->get_dir().'/sql/datos_locales.sql';
		if (file_exists($locales)) {
			$this->manejador_interface->mensaje('Cargando datos locales', false);			
			$this->manejador_interface->progreso_avanzar();			
			$base->ejecutar_archivo($locales);			
			$this->manejador_interface->progreso_fin();
		} else {
			$datos = $this->proyecto->get_dir().'/sql/datos_basicos.sql';
			if (file_exists($datos)) {
				$this->manejador_interface->mensaje('Cargando datos básicos', false);
				$base->ejecutar_archivo($datos);
				$this->manejador_interface->progreso_fin();				
			}			
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
		$id = $this->proyecto->get_id();
		$this->manejador_interface->titulo("Instalando $id ".$version->__toString());		
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
		//--- Chequea si hay un modelo cargado y decide que hacer en tal caso
		$base = $this->instalacion->conectar_base($id_def_base);	
		if (!$this->estructura_creada($base)) {
			$this->cargar_modelo_datos($base);			
		} else {
			$this->regenerar_modelo_datos($base, $id_def_base);
		}
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