<?php

use SIU\AraiJsonMigrator\Entities\Person;
use SIU\AraiJsonMigrator\Entities\Account;
use SIU\AraiJsonMigrator\Util\Documento;

/**
 * Clase que contiene la lógica de administración de la aplicación, es utiliza por los comandos
 * @package Centrales
 * @subpackage Modelo
 */
class toba_aplicacion_modelo_base implements toba_aplicacion_modelo 
{
	protected $permitir_exportar_modelo = true;
	protected $forzar_reemplazar_modelo = false;
	protected $permitir_instalar = true;
	protected $permitir_determinar_encoding_bd = true;
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
		if (isset($parametros['schema'])) {
			unset($parametros['schema']);
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
		$base->retrasar_constraints();
		$this->crear_estructura($base);
		$this->cargar_datos($base);
		$base->cerrar_transaccion();			
	}
	
	/**
	 * @todo No esta soportada la exportación de los datos en Windows cuando el usuario de postgres requiere clave
	 */
	function regenerar_modelo_datos($base, $id_def_base)
	{
		$reemplazar = $this->forzar_reemplazar_modelo || $this->manejador_interface->dialogo_simple("Ya existe el modelo de datos, ".
							"Desea reemplazarlo? (borra la base completa y la vuelva a cargar)", 's');
		if (! $reemplazar) {
			return;
		}
		$exportar = $this->permitir_exportar_modelo && $this->manejador_interface->dialogo_simple("Antes de borrar la base. Desea exportar y utilizar su contenido actual en la nueva carga?", 's');
		if ($exportar) {
			$dir_arranque = $this->proyecto->get_dir(). '/sql';
			toba_manejador_archivos::crear_arbol_directorios($dir_arranque);
			//-- Esquema principal
			$archivo = $dir_arranque.'/datos_locales.sql';			
			$this->exportar_esquema_base($id_def_base, $archivo, true, $this->schema_modelo);
			//-- Esquema auditoria
			$archivo = $dir_arranque.'/datos_auditoria.sql';			
			$this->exportar_esquema_base($id_def_base, $archivo, false, $this->schema_auditoria);
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
	
	protected function exportar_esquema_base($id_def_base,  $archivo, $obligatorio, $esquema=null)
	{
		$parametros = $this->instalacion->get_parametros_base($id_def_base);
		if (file_exists($archivo)) {
			copy($archivo, $archivo.'.old');
		}
		
		$esquema =  (! is_null($esquema)) ? " -n $esquema " : '';
		$comando = "pg_dump -a --disable-triggers $esquema -h {$parametros['profile']} -U {$parametros['usuario']}  -p {$parametros['puerto']} -f \"$archivo\"  {$parametros['base']}";			
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
			if (! isset($datos_servidor['schema'])) {
				$datos_servidor['schema'] =  $this->schema_modelo;			
			}			
			//-- Agrega la definición de la base
			$this->instalacion->agregar_db($id_def_base, $datos_servidor);
			if ($this->permitir_determinar_encoding_bd) {
				$this->instalacion->determinar_encoding($id_def_base);
			}			
		}
				
		//--- Chequea si existe fisicamente la base creada
		if (! $this->instalacion->existe_base_datos($id_def_base)) {
			$this->instalacion->crear_base_datos($id_def_base, false);
		} 
		//--- Chequea si hay un modelo cargado y decide que hacer en tal caso
		$base = $this->instalacion->conectar_base($id_def_base);	
		if (isset($datos_servidor['schema'])) {
			$this->schema_modelo = $datos_servidor['schema'];
		}
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
	 * @param string $fuente Indica que se va a procesar solo una de las fuentes del proyecto
	 * @param boolean $guardar_datos Indica que se deben guardar y restaurar los datos actuales luego del proceso
	 * @param boolean $fuerza_eliminacion_triggers Indica que se deben eliminar los triggers a toda costa
	 */
	function crear_auditoria($tablas=array(), $prefijo_tablas=null, $con_transaccion=true, $fuente=null, $lista_schemas = array(), $guardar_datos=false, $fuerza_eliminacion_triggers=false)
	{
		if (! is_null($fuente)) {
			$fuentes = array($fuente);
		} else {
			$fuentes = $this->proyecto->get_indice_fuentes();
		}
		toba_logger::instancia()->var_dump($fuentes, 'fuentes activas');
		if (empty($fuentes)) {
			return;
		}
		
		//Recorro los schemas de las fuentes del proyecto
		$schemas = array();
		foreach($fuentes as $id_fuente) {			
			$schemas[$id_fuente] = aplanar_matriz(toba_info_editores::get_schemas_fuente($this->proyecto->get_id(), $id_fuente), 'schema');
		}		
		
		if (! is_null($fuente) && !empty($lista_schemas)) {
			$aux = array_intersect($schemas[$fuente], $lista_schemas);
			if ($aux !== false && ! empty($aux)) {
				$schemas[$fuente] = $aux;
			}
		}
		toba_logger::instancia()->var_dump($schemas, 'schemas de fuentes');		
				
		//--- Tablas de auditoría
		$this->manejador_interface->mensaje('Creando auditoria', true);
		$dir_arranque = $this->proyecto->get_dir(). '/sql';
		toba_manejador_archivos::crear_arbol_directorios($dir_arranque);
		$archivo = $dir_arranque . '/datos_auditoria.sql';
		$bases = array();
		foreach($fuentes as $fuente) {
			try {
				$bases[$fuente] = $this->proyecto->get_db_negocio($fuente);	
				if ($guardar_datos) {
					$id_def_base = $this->proyecto->construir_id_def_base($fuente);		//Guarda los datos actuales de auditoria
					$this->exportar_esquema_base($id_def_base, $archivo, false, " '*_auditoria' ");
				}			
				// Hace la migracion y restauracion de datos
				if ($con_transaccion) {
					$bases[$fuente]->abrir_transaccion();
				}		
				$this->procesar_schemas_fuente($bases[$fuente], $schemas[$fuente], false, $tablas, $prefijo_tablas, 'crear', 0, $fuerza_eliminacion_triggers);
			} catch (toba_error_db $e) {
				if (isset($bases[$fuente])) {
					$bases[$fuente]->abortar_transaccion();			//Si hay algun error hace revert y anula el objeto de la bd
					unset($bases[$fuente]);
				}
			}
		}

		$this->proyecto->generar_roles_db();
		if ($con_transaccion) {
			foreach($fuentes as $fuente) {
				if (isset($bases[$fuente])) {					//Cierra la transaccion en aquellas bases presentes y sin errores
					$bases[$fuente]->cerrar_transaccion();
				}
			}
		}
	}
	
	/**
	 * Borra los triggers, store_procedures y esquema para la auditoría de tablas del sistema
	 */
	function borrar_auditoria($tablas=array(), $prefijo_tablas=null, $con_transaccion=true)
	{
		$this->manejador_interface->mensaje('Borrando esquema y triggers de auditoria', true);		
		$fuentes = $this->proyecto->get_indice_fuentes();
		toba_logger::instancia()->var_dump($fuentes, 'fuentes activas');
		if (empty($fuentes)) {
			return;
		}
		
		$schemas = array();
		foreach($fuentes as $fuente) {			
			$schemas[$fuente] = aplanar_matriz(toba_info_editores::get_schemas_fuente($this->proyecto->get_id(), $fuente), 'schema');
		}
		toba_logger::instancia()->var_dump($schemas, 'schemas de fuentes');			
		foreach($fuentes as $fuente) {
			try {
				$base= $this->proyecto->get_db_negocio($fuente);
				$this->procesar_schemas_fuente($base, $schemas[$fuente], $con_transaccion, $tablas, $prefijo_tablas, 'eliminar');
				unset($base);
			} catch (toba_error_db $e) {
				if (isset($base)) {
					$base->abortar_transaccion();			//Si hay algun error hace revert y anula el objeto de la bd
					unset($base);
				}
			}	
		}
	}
	
	function purgar_auditoria($tiempo = 0, $tablas=array(), $prefijo_tablas=null, $con_transaccion=true)
	{
		$this->manejador_interface->mensaje('Limpiando las tablas de auditoria', true);	
		$fuentes = $this->proyecto->get_indice_fuentes();
		toba_logger::instancia()->var_dump($fuentes, 'fuentes activas');
		if (empty($fuentes)) {
			return;
		}
		
		$schemas = array();
		foreach($fuentes as $fuente) {			
			$schemas[$fuente] = aplanar_matriz(toba_info_editores::get_schemas_fuente($this->proyecto->get_id(), $fuente), 'schema');
		}
		toba_logger::instancia()->var_dump($schemas, 'schemas de fuentes');
		foreach($fuentes as $fuente) {
			try {
				$base= $this->proyecto->get_db_negocio($fuente);
				$this->procesar_schemas_fuente($base, $schemas[$fuente], $con_transaccion, $tablas, $prefijo_tablas, 'purgar', $tiempo);
				unset($base);
			} catch (toba_error_db $e) {
				if (isset($base)) {
					$base->abortar_transaccion();			//Si hay algun error hace revert y anula el objeto de la bd
					unset($base);
				}
			}				
		}
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
	
	/**
	 *  Procesa los cambios para cada schema de la fuente 
	 * @param toba_db $db
	 * @param array $schemas
	 * @param boolean $con_transaccion
	 * @param array $tablas
	 * @param string $prefijo
	 * @param string $accion
	 * @param integer $tiempo
	 * @param boolean $fuerza_eliminacion_triggers
	 */
	protected function procesar_schemas_fuente($db, $schemas, $con_transaccion, $tablas, $prefijo, $accion, $tiempo=0, $fuerza_eliminacion_triggers=false)
	{		
		if ($con_transaccion) {
			$db->abrir_transaccion();
		}

		foreach($schemas as $schema)  {
			$this->manejador_interface->mensaje('Procesando schema ' . $schema, false);
			$logs = $schema . '_auditoria';
			$auditoria = $db->get_manejador_auditoria($schema, $logs, $this->schema_toba);
			if (! is_null($auditoria)) {		//Existe manejador para el motor en cuestion
				$auditoria->set_triggers_eliminacion_forzada($fuerza_eliminacion_triggers);
				$this->procesar_accion_schema($auditoria, $tablas, $prefijo, $accion, $tiempo);
				unset($auditoria);
			}			
			$this->manejador_interface->progreso_fin();
		}
		
		if ($con_transaccion) {
			$db->cerrar_transaccion();
		}
	}		
	
	/**
	 * Agrega las tablas y toma la accion correspondiente sobre las mismas
	 * @param toba_auditoria_tablas_postgres $auditoria
	 * @param array $tablas
	 * @param string $prefijo
	 * @param string $accion
	 * @param integer $tiempo
	 */
	protected function procesar_accion_schema($auditoria, $tablas, $prefijo, $accion, $tiempo) 
	{
		if (empty($tablas)) {
			$auditoria->agregar_tablas($prefijo);
		} else {
			foreach($tablas as $tabla) {
				$auditoria->agregar_tabla($tabla);
			}
		}
		
		$this->manejador_interface->progreso_avanzar();
		switch ($accion) {
			case 'eliminar' : 
				$auditoria->eliminar();
				break;
			case 'purgar': 
				$auditoria->purgar($tiempo);
				break;
			default:
				if (! $auditoria->existe()) {
					$auditoria->crear();
				} else {
					$auditoria->migrar();			
				}
				break;			
		}
		$this->manejador_interface->progreso_avanzar();
	}

	/**
	 * @return array
	 * 		array(
	 * 			array(
	 * 				'usuario' => 'prueba',
	 *				'nombres' => 'Juan',
	 *				'apellidos' => 'Perez',
	 *				'nombre_completo' => 'Juan Perez',
	 *				'bloqueado' => '1|0',
	 *				'emails' => 'prueba@siu.edu.ar',
	 *				'clave' => 'sha("123456")',
	 *				'autentificacion' => 'crypt|sha',
	 * 			),
	 * 			array(
	 * 				'usuario' => 'prueba1',
	 *				'nombres' => 'Jose',
	 *				'apellidos' => 'Aimar',
	 *				'nombre_completo' => 'Jose Aimar',
	 *				'bloqueado' => '1|0',
	 *				'emails' => 'prueba1@siu.edu.ar',
	 *				'clave' => 'sha("12345678")',
	 *				'autentificacion' => 'crypt|sha',
	 * 			),
	 * 			...,
	 * 		)
	 *
     */
	public function getDatosUsuarios($tokens = array())
	{
		$db_arai = $this->get_instancia()->get_db();
		$sql = "SELECT  DISTINCT
						u.usuario,
						u.clave,
						u.nombre,
						u.email,
						u.autentificacion,
						CASE
						  WHEN lower(u.autentificacion) = 'bcrypt' THEN 'crypt'
						  WHEN lower(u.autentificacion) = 'sha256' OR lower(u.autentificacion) = 'sha512' THEN 'sha'
						  ELSE lower(u.autentificacion)
						END autentificacion_arai,
						u.bloqueado
				FROM apex_usuario u
				JOIN apex_usuario_proyecto up ON (u.usuario = up.usuario AND up.proyecto = :toba_proyecto) ";
		$sentencia = $db_arai->sentencia_preparar($sql);
		$datosUsuariosToba = $db_arai->sentencia_consultar($sentencia, array('toba_proyecto' => $this->get_proyecto()->get_id()));

		$datosUsuarios = array();
		foreach($datosUsuariosToba as $clave => $datosUsuarioToba) {
			$nombresApellidos = $this->getNombresApellidos($datosUsuarioToba['nombre'], $tokens);
			$datosUsuarios[$clave] = array(
				'usuario' => $datosUsuarioToba['usuario'],
				'nombres' => trim($nombresApellidos['nombres']),
				'apellidos' => trim($nombresApellidos['apellidos']),
				'nombre_completo' => trim($datosUsuarioToba['nombre']),
				'bloqueado' => strval($datosUsuarioToba['bloqueado']),
				'emails' => trim($datosUsuarioToba['email']) != "" ? trim($datosUsuarioToba['email']) : NULL,
				'clave' => $datosUsuarioToba['clave'],
				'autentificacion' => $datosUsuarioToba['autentificacion_arai'],
			);
		}

		return $datosUsuarios;
	}

	/**
	 * @param array $datosUsuario
	 * @return Person
	 */
	public function generatePerson(array $datosUsuario)
	{
		/* @var Person $person */
		$person = new Person();
		// nombre y apellido
		if (isset($datosUsuario['nombres'])) {
			$person->setGivenName($datosUsuario['nombres']);
		}
		if (isset($datosUsuario['apellidos'])) {
			$person->setSn($datosUsuario['apellidos']);
		}
		if (isset($datosUsuario['nombre_completo'])) {
			$person->setCn($datosUsuario['nombre_completo']);
		}
		// persona bloqueada
		if (isset($datosUsuario['bloqueado'])) {
			$person->setBloqueada($datosUsuario['bloqueado']);
		}
		// Email de la persona
		if (!empty($datosUsuario['emails'])) {
			$person->setMail($datosUsuario['emails']);
		}

		return $person;
	}

	/**
	 * @param array $datosUsuario
	 * @param Person $person
	 * @return Account
     */
	public function generateAccountApp(array $datosUsuario, Person $person)
	{
		/* @var Account $account */
		$account = new Account();

		if (isset($datosUsuario['usuario'])) {
			$account->setUid($datosUsuario['usuario']);
			$account->setUniqueIdentifier($datosUsuario['usuario']);
		}

		// Setear identificacion de la aplicacion
		$this->setearAplicacion($account);

		if (isset($datosUsuario['clave'])) {
			$account->setPassword($datosUsuario['clave']);
		}
		if (isset($datosUsuario['autentificacion'])) {
			$account->setPasswordAlgorithm($datosUsuario['autentificacion']);
		}
		if (isset($person)) {
			$account->setPerson($person);
		}

		return $account;
	}

	/**
	 * @param Account $account
     */
	public function setearAplicacion(Account $account)
	{
		$appUniqueId = null;
		if ($this->instalacion->vincula_arai_usuarios()) {
			$appUniqueId = \SIUToba\Framework\Arai\RegistryHooksProyectoToba::getAppUniqueId();
		}
		if (isset($appUniqueId)) {
			$account->setAppUniqueId($appUniqueId);
		} else {
			$nombreProyecto = $this->get_proyecto()->get_parametro('proyecto', 'nombre', false);
			$account->setAppName(utf8_e_seguro(isset($nombreProyecto)?$nombreProyecto:$this->get_proyecto()->get_id()));
		}
	}

	private function getNombresApellidos($dato, $tokens)
	{
		$apellidos = $nombres = $dato;						//Inicializo para el caso que no exista separación		
		$separador = (! empty($tokens)) ? $tokens['separador'] : " ";
		$nombre_partes = explode($separador, $dato);
		$cant_partes = count($nombre_partes);
		
		if ($nombre_partes !== false && $cant_partes > 1) {
			if (! empty($tokens)) {
				$nombres = $nombre_partes[$tokens['nombre']];
				$apellidos = $nombre_partes[$tokens['apellido']];
			} else {
				//Parte el nombre/apellido, siempre dandole mas palabras al nombre
				/*$nombres = $apellidos = "";
				for ($i = 0; $i < $cant_partes; $i++) {
					if ($i < $cant_partes / 2) {
						$nombres .= $nombre_partes[$i]." ";
					} else {
						$apellidos .= $nombre_partes[$i]." ";
					}
				}*/
				$limite = ceil($cant_partes / 2);
				$nombres = implode(' ' , array_slice($nombre_partes, 0 , $limite, true));				//Le asigno al nombre la primera mitad
				$apellidos = implode(' ' , array_slice($nombre_partes, $limite, null, true));				//Todo lo que resta es apellido
			}
		}
		
		return array(
			'nombres' => $nombres,
			'apellidos' => $apellidos,
		);
	}

}
?>