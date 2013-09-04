<?php

class toba_aplicacion_modelo_base implements toba_aplicacion_modelo 
{
	protected $permitir_exportar_modelo = true;
	protected $permitir_instalar = true;
	protected $schema_modelo;
	protected $schema_auditoria;
	protected $schema_toba = null;
	
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
	 * @param $default_schema Esquema por defecto del proyecto
	 * 
	 */
	function set_entorno($manejador_interface, toba_modelo_instalacion $instalacion, toba_modelo_instancia $instancia, toba_modelo_proyecto $proyecto, $default_schema='public')
	{
		$this->manejador_interface = $manejador_interface;
		$this->instalacion = $instalacion;
		$this->instancia = $instancia;
		$this->proyecto = $proyecto;
		$db = $instancia->get_db();
		$schema_toba = $instancia->get_id();
		if ($db->existe_schema($schema_toba)) {
			$this->schema_toba = $schema_toba;
		}
		//Si no se harcodeo el schema del proyecto, trata de averiguarlo
		if (! isset($this->schema_modelo)) {
			$encontrado = false;
			$id_def_base = $this->proyecto->construir_id_def_base($this->get_fuente_defecto());
			if ($this->instalacion->existe_base_datos_definida($id_def_base)) {			
				$parametros = $this->instalacion->get_parametros_base($id_def_base);
				if (isset($parametros['schema'])) {
					$this->schema_modelo = $parametros['schema']; 
					$encontrado = true;
				}
			}
			if (! $encontrado) {
				$this->schema_modelo = $default_schema;
			}
		}
		//Construye el schema de la auditoria
		$this->schema_auditoria = $this->schema_modelo.'_auditoria';		 
	}
	
	/**
	 * @return toba_modelo_instalacion
	 */
	function get_instalacion()
	{
		return $this->instalacion;
	}

	/**
	 * @return toba_modelo_instancia
	 */
	function get_instancia()
	{
		return $this->instancia;
	}

	/**
	 * @return toba_modelo_proyecto
	 */
	function get_proyecto()
	{
		return $this->proyecto;
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
		if (file_exists($this->proyecto->get_dir().'/proyecto.ini')) {
			$ini = new toba_ini($this->proyecto->get_dir().'/proyecto.ini');
			if ($ini->existe_entrada('proyecto', 'version')) {
				return new toba_version($ini->get('proyecto', 'version', null, true));
			}
		}
		return $this->instalacion->get_version_actual();
	}
	
	function get_id_base()
	{
		$version = $this->get_version_nueva();		
		return $this->proyecto->get_id().'_'.$version->get_release('_');		
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
		if (! $base->existe_schema($this->schema_modelo)) {
			$base->crear_schema($this->schema_modelo);
			$base->set_schema($this->schema_modelo);				
		}
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
		$reemplazar = $this->manejador_interface->dialogo_simple("Ya existe el modelo de datos, ".
							"Desea reemplazarlo? (borra la base completa y la vuelva a cargar)", 's');
		if (! $reemplazar) {
			return;
		}
		$exportar = $this->permitir_exportar_modelo && $this->manejador_interface->dialogo_simple("Antes de borrar la base. Desea exportar y utilizar su contenido actual en la nueva carga?", 's');
		if ($exportar) {
			//-- Esquema principal
			$archivo = $this->proyecto->get_dir().'/sql/datos_locales.sql';			
			$this->exportar_esquema_base($id_def_base, $this->schema_modelo, $archivo, true);
			//-- Esquema auditoria
			$archivo = $this->proyecto->get_dir().'/sql/datos_auditoria.sql';			
			$this->exportar_esquema_base($id_def_base, $this->schema_auditoria, $archivo, false);			
		}
		
		//--- Borra la base fisicamente
		$this->manejador_interface->mensaje('Borrando modelo actual', false);
		if ($base->existe_schema($this->schema_modelo)) {
			$base->borrar_schema($this->schema_modelo);
			$base->crear_schema($this->schema_modelo);
		}
		$this->manejador_interface->progreso_avanzar();
		$this->manejador_interface->progreso_fin();		
		
		//--- Carga nuevamente el modelo de datos
		$base = $this->instalacion->conectar_base($id_def_base);
		$this->cargar_modelo_datos($base);	
	}
	
	protected function exportar_esquema_base($id_def_base, $esquema, $archivo, $obligatorio)
	{
		$parametros = $this->instalacion->get_parametros_base($id_def_base);
		if (file_exists($archivo)) {
			copy($archivo, $archivo.'.old');
		}
		$comando = "pg_dump -d -a -n $esquema -h {$parametros['profile']} -U {$parametros['usuario']} -f \"$archivo\" {$parametros['base']}";			
		if (! toba_manejador_archivos::es_windows() && $parametros['clave'] != '') {
			$clave = "export PGPASSWORD=".$parametros['clave'].';';
			$comando = $clave.$comando;
		}
		$this->manejador_interface->mensaje("Ejecutando: $comando");
		$salida = array();
		echo exec($comando, $salida, $exito);
		echo implode("\n", $salida);
		if ($obligatorio && $exito > 0) {
			throw new toba_error('No se pudo exportar correctamente los datos');
		}
	}
	
	
	/**
	 * Determina si el modelo de datos se encuentra cargado en una conexión específica
	 * @param toba_db $base
	 */
	function estructura_creada(toba_db $base)
	{
		$tablas = $base->get_lista_tablas(false, $this->schema_modelo);
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
	
	function get_fuente_defecto()
	{
		//--- Se asume que la base a instalar corresponde a la primer fuente
		$fuentes = $this->proyecto->get_indice_fuentes();
		if (empty($fuentes)) {
			throw new toba_error("No existen fuentes definidas");
		}		
		return current($fuentes);		
	}
	
	/**
	 * @param array $datos_servidor Asociativo con los parámetros de conexión a la base
	 */
	function instalar($datos_servidor)
	{
		if (! $this->permitir_instalar) {
			return;
		}
		$version = $this->get_version_nueva();

		$id = $this->proyecto->get_id();
		$this->manejador_interface->titulo("Instalando $id ".$version->__toString());		
		$id_def_base = $this->proyecto->construir_id_def_base($this->get_fuente_defecto());
		
		//--- Chequea si existe la entrada de la base de negocios en el archivo de bases
		if (! $this->instalacion->existe_base_datos_definida($id_def_base)) {
			if (! isset($datos_servidor['base'])) {
				$id_base = $this->get_id_base();
				$datos_servidor['base'] = $id_base;
			}
			//-- Cambia el schema
			$datos_servidor['schema'] = $this->schema_modelo;			

			//-- Agrega la definición de la base
			$this->instalacion->agregar_db($id_def_base, $datos_servidor);
			$this->instalacion->determinar_encoding($id_def_base);
		}
		
		//--- Chequea si existe fisicamente la base creada
		if (! $this->instalacion->existe_base_datos($id_def_base)) {
			$this->instalacion->crear_base_datos($id_def_base, false);
		} 
		
		//--- Chequea si hay un modelo cargado y decide que hacer en tal caso
		$base = $this->instalacion->conectar_base($id_def_base);	
		if (!$this->estructura_creada($base)) {
			$this->cargar_modelo_datos($base);			
		} else {
			$this->regenerar_modelo_datos($base, $id_def_base);
		}
		
		//Actualiza permisos de la base
		$this->proyecto->generar_roles_db();
	}

	function desinstalar()
	{
		$id = $this->proyecto->get_id();
		$this->manejador_interface->titulo("Desinstalando $id");		
		$id_def_base = $this->proyecto->construir_id_def_base($this->get_fuente_defecto());
		
		//--- Chequea si existe la entrada de la base de negocios en el archivo de bases
		if ($this->instalacion->existe_base_datos_definida($id_def_base)) {
			//--- Chequea si existe fisicamente la base creada y la borra
			if ($this->instalacion->existe_base_datos($id_def_base)) {
				$this->manejador_interface->mensaje('Borrando base de datos', false);
				$this->manejador_interface->progreso_avanzar();	
				$this->instalacion->borrar_base_datos($id_def_base);
				$this->manejador_interface->progreso_fin();				
				
			} 			
			$this->instalacion->eliminar_db($id_def_base);
		}	
	}
	
	/**
	 * Crea los triggers, store_procedures y esquema para la auditoría de tablas del sistema
	 * En caso que el schema exista, busca nuevos campos y tablas
	 * @param array $tablas Tablas especificas a auditar
	 * @param string $prefijo_tablas Tomar todas las tablas que tienen este prefijo, si es null se toman todas
	 * @param boolean $con_transaccion Crea el esquema dentro de una transaccion
	 */
	function crear_auditoria($tablas=array(), $prefijo_tablas=null, $con_transaccion=true)
	{
		$fuentes = $this->proyecto->get_indice_fuentes();
		if (empty($fuentes)) {
			return;
		}
		$base = $this->proyecto->get_db_negocio();
		
		//--- Tablas de auditoría
		$auditoria = $base->get_manejador_auditoria($this->schema_modelo, $this->schema_auditoria, $this->schema_toba);
		if (is_null($auditoria)) {		//No existe manejador para el motor en cuestion
			return;
		}
		
		if (empty($tablas)) {
			$auditoria->agregar_tablas($prefijo_tablas);
		} else {
			foreach($tablas as $tabla) {
				$auditoria->agregar_tabla($tabla);
			}
		}
		$this->manejador_interface->mensaje('Creando esquema de auditoria', false);
		$this->manejador_interface->progreso_avanzar();		
		if (! $auditoria->existe()) {
			$auditoria->crear();
		} else {
			$auditoria->migrar();			
		}
		
		$this->manejador_interface->progreso_fin();

		//--- Datos anteriores
		$archivo_datos = $this->proyecto->get_dir().'/sql/datos_auditoria.sql';
		if (file_exists($archivo_datos)) {
			$this->manejador_interface->mensaje('Cargando datos de auditoria', false);			
			$this->manejador_interface->progreso_avanzar();
			$base->ejecutar_archivo($archivo_datos);
			$this->manejador_interface->progreso_fin();
		}
		$this->proyecto->generar_roles_db();
		if ($con_transaccion) {
			$base->cerrar_transaccion();
		}		
	}
			
	/**
	 * Borra los triggers, store_procedures y esquema para la auditoría de tablas del sistema
	 */
	function borrar_auditoria($tablas=array(), $prefijo_tablas=null, $con_transaccion=true)
	{
		$this->manejador_interface->mensaje('Borrando esquema y triggers de auditoria', false);
		$this->manejador_interface->progreso_avanzar();
		$base = $this->proyecto->get_db_negocio();				
		if ($con_transaccion) {
			$base->abrir_transaccion();
		}
		//--- Tablas de auditoría
		$auditoria = $base->get_manejador_auditoria($this->schema_modelo, $this->schema_auditoria, $this->schema_toba);
		if (is_null($auditoria)) {		//No existe manejador para el motor en cuestion
			return;
		}
		
		if (empty($tablas)) {
			$auditoria->agregar_tablas($prefijo_tablas);
		} else {
			foreach($tablas as $tabla) {
				$auditoria->agregar_tabla($tabla);
			}
		}
		$auditoria->eliminar();
		if ($con_transaccion) {
			$base->cerrar_transaccion();
		}
		$this->manejador_interface->progreso_fin();
	}
	
	function purgar_auditoria($tiempo = 0, $tablas=array(), $prefijo_tablas=null, $con_transaccion=true)
	{
		$this->manejador_interface->mensaje('Limpiando las tablas de auditoria', false);
		$this->manejador_interface->progreso_avanzar();
		$base = $this->proyecto->get_db_negocio();
		if ($con_transaccion) {
			$base->abrir_transaccion();			
		}
		
		$auditoria = $base->get_manejador_auditoria($this->schema_modelo, $this->schema_auditoria, $this->schema_toba);
		if (is_null($auditoria)) {	//No existe manejador para el motor en cuestion
			return;
		}
		
		if (empty($tablas)) {
			$auditoria->agregar_tablas($prefijo_tablas);
		} else {
			foreach($tablas as $tabla) {
				$auditoria->agregar_tabla($tabla);
			}
		}		
		$auditoria->purgar($tiempo);
		if ($con_transaccion) {
			$base->cerrar_transaccion();
		}
		$this->manejador_interface->progreso_fin();
	}
	
	
	function migrar_auditoria_2_4($tablas=array(), $prefijo_tablas=null)
	{
		$fuentes = $this->proyecto->get_indice_fuentes();
		if (empty($fuentes)) {
			return;
		}
		$base = $this->proyecto->get_db_negocio();
		$auditoria = $base->get_manejador_auditoria($this->schema_modelo, $this->schema_auditoria, $this->schema_toba);
		if (is_null($auditoria)) {		//No existe manejador para el motor en cuestion
			return;
		}
		
		if (empty($tablas)) {
			$auditoria->agregar_tablas($prefijo_tablas);
		} else {
			foreach($tablas as $tabla) {
				$auditoria->agregar_tabla($tabla);
			}
		}

		$auditoria->migrar_estructura_campos_toba_2_4();
	}
	
	/**
	 * Ejecuta los scripts de migración entre dos versiones específicas del sistema
	 * @param toba_version $desde
	 * @param toba_version $hasta
	 */
	function migrar(toba_version $desde, toba_version $hasta)
	{
		
	}	
	
	/**
	 *  Crea el lenguaje plpgsql unicamente si el mismo aun no existe para la base de datos.
	 */
	function crear_lenguaje_procedural(toba_db $base)
	{
		$base->crear_lenguaje_procedural();
	}
}

?>