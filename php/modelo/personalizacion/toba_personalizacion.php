<?php
/**
 * Esta clase implementa los comandos de personalizacion
 * @package Centrales
 * @subpackage Personalizacion
 */	
class toba_personalizacion {
	// Constantes de configuración de personalización
	const archivo_ini			= 'personalizacion.ini';
	const dir_personalizacion	= 'personalizacion';

	// Directorios y archivos generados
	const dir_nuevos		= 'nuevos/';
	const dir_modificados	= 'modificados/';
	const dir_borrados		= 'borrados/';
	const dir_tablas		= 'tablas/';
	const dir_componentes	= 'componentes/';
	const dir_metadatos_xml		= 'metadatos/';
	const dir_metadatos_originales = 'metadatos_originales/';
	const dir_logs			= 'logs/';
	const dir_php			= 'php/';
	const dir_www           = 'www/';
	const nombre_plan		= 'plan.xml';
	const template_archivo_componente	= 'comp_%id%.xml';
	const template_archivo_tabla		= 'tabla_%id%.xml';

	// Nombres de variables en el .ini
	const iniciada			= 'iniciada';
	const schema_original	= 'schema_original';
	const schema_temporal	= 'schema_personalizado';
	const conf_chequeados	= 'conflictos_chequeados';

	// Estados de los registros
	const registro_inserted = 1;
	const registro_updated	= 2;
	const registro_deleted	= 3;

	// Cadena que representa nulos en los xml de exportación
	const nulo = '###$$$NULL$$$###';

	/**
	 * @var toba_modelo_proyecto
	 */
	protected $proyecto;
	/**
	 * @var toba_db_postgres7
	 */
	protected $db;
	protected $dir;
	protected $ini;

	protected $dir_tablas;
	protected $dir_componentes;
	protected $dir_metadatos;

	/**
	 * @var consola
	 */
	protected $consola;

	protected static $registro_conflictos;
	protected static $instancia;

	protected $modo_ejecucion_transcaccional = false;
	
	/**
	 * @return toba_registro_conflictos
	 */
	static function get_registro_conflictos()
	{
		if (!isset(self::$registro_conflictos)) {
			self::$registro_conflictos = new toba_registro_conflictos();
		}

		return self::$registro_conflictos;
	}
	
	
	static function get_personalizacion_iniciada($proyecto) 
	{
		$path_proyecto = toba::instancia()->get_path_proyecto($proyecto);
		$path_pers = $path_proyecto.'/'.toba_personalizacion::dir_personalizacion;
		$ini_path = $path_pers.'/personalizacion.ini';
		if (is_file($ini_path)) {
			$ini = new toba_ini($ini_path);
			return $ini->get_datos_entrada('iniciada') == 'si';
		} else {
			return false;
		}
	}

	function  __construct(toba_modelo_proyecto $proyecto, $consola = null)
	{
		$this->proyecto = $proyecto;
		$this->db		= $this->proyecto->get_db();
		$this->consola	= $consola;
		$this->init_dirs();
		$this->cargar_ini();
	}

	protected function clonar_schema_windows($schema_viejo, $schema_nuevo, $profile, $base, $user, $pass, $port)
	{
		$temp_dir = $this->proyecto->get_dir(). '/temp';
		$salida = toba_manejador_archivos::path_a_windows($temp_dir.'/dump.sql');
		$bat = "
			@echo off
			SET PGUSER=$user
			SET PGPASSWORD=$pass
			pg_dump -h $profile -p $port --inserts --no-owner -x -n $schema_viejo $base  -f $salida				
			psql -h $profile -p $port -c \"ALTER SCHEMA $schema_viejo RENAME TO $schema_nuevo\" $base
			psql -h $profile -d $base -p $port -f $salida
		";
		$bat_file = $temp_dir.'/clonar_schema.bat';
		file_put_contents($bat_file, $bat);
		system('cmd /c "'.$bat_file.'"');
		unlink($bat_file);
		unlink($salida);
	}
	
	protected function clonar_schema_linux($schema_viejo, $schema_nuevo, $profile, $base, $user, $pass, $port)
	{
		$temp_dir = $this->proyecto->get_dir(). '/temp';
		$salida = $temp_dir.'/dump.sql';
		
		$sh  = "export PGUSER=$user\n";
		$sh .= "export PGPASSWORD=$pass\n";
		$sh .= "pg_dump -h $profile -p $port --inserts --no-owner -x -n $schema_viejo $base -f $salida\n";
		$sh .= "psql -h $profile -p $port -c \"ALTER SCHEMA $schema_viejo RENAME TO $schema_nuevo\" $base\n";
		$sh .= "psql -h $profile -d $base -p $port -f $salida\n";

		$sh_file = $temp_dir.'/clonar_schema.sh';
		
		file_put_contents($sh_file, $sh);
		chmod($sh_file, 0755);
		exec($sh_file);
		//unlink($sh_file);
		unlink($salida);
	}
	
	protected function clonar_schema($schema_viejo, $schema_nuevo)
	{
		$params = $this->db->get_parametros();
		$profile = $params['profile'];
		$base = $params['base'];
		$puerto = (isset($params['puerto']) && trim($params['puerto'] != '')) ? $params['puerto'] : '5432';
		$usuario = $params['usuario'];
		$clave = $params['clave'];
		
		if (toba_manejador_archivos::es_windows()) {
			$this->clonar_schema_windows($schema_viejo, $schema_nuevo, $profile, $base, $usuario, $clave, $puerto);
		} else {
			$this->clonar_schema_linux($schema_viejo, $schema_nuevo, $profile, $base, $usuario, $clave, $puerto);
		}
	}
   
    
	function iniciar()
	{
		$schema_o = $this->get_schema_original();										//Pide Schema Original desde configuracion		
		$schema_t = $this->get_schema_personalizacion();
		$this->db->set_schema('public');
		$this->kill_schemas($schema_t);
		$this->set_schema_original($schema_o);
		$this->set_iniciada(true);
		$this->ini->guardar();
	}

	function desactivar()
	{
		if ($this->iniciada()) {
			$this->set_iniciada(false);
			$this->ini->guardar();
		}
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------//
	//									OPERACIONES									     //
	//------------------------------------------------------------------------------------------------------------------------------------------------------//	
	function exportar()
	{
		if (!$this->iniciada()) {
			throw  new  toba_error("PERSONALIZACION: Debe iniciar la personalización antes de exportarla");
		}		
		$this->crear_directorios();
		$this->consola->mensaje('Generando esquema alterno..');
		$this->generar_schema_diff();		//Genero el schema con los metadatos originales para hacer el diff		
		$this->consola->mensaje('Calculando diferenciales..');
		$this->exportar_tablas();
		$this->exportar_componentes();	//Aca hay que asegurarse que se agregue la clase del componente como descripcion
		$this->consola->mensaje('Restaurando entorno de trabajo..');
		$this->restaurar_schema_trabajo();
	}

	/**
	 * Chequea los posibles conflictos para la importacion y los guarda en un archivo de log
	 */
	function chequear_conflictos()
	{
		$importador_tablas =  new toba_importador_tablas($this->dir_tablas.self::nombre_plan, $this->db);					
		$importador_componentes =  new  toba_importador_componentes($this->dir_componentes.self::nombre_plan, $this->db);		
		
		//Ejecuto todo dentro de una transaccion destinada a abortarse, 
		//esto me permite resolver algunas cuestiones temporales que el chequeo a puro registro no.
		$this->db->abrir_transaccion();		
		
		//Analizo los conflictos
		$this->analizar_conflictos($importador_tablas);
		$this->analizar_conflictos($importador_componentes);
		
		//Aborto la transaccion
		$this->db->abortar_transaccion();
		
		//Guardo un archivo con el log de los conflictos
		$path_log = $this->dir.'logs/conflictos.log';
		$reg_conflictos = self::get_registro_conflictos();
		if ($this->consola) {
			$this->consola->mensaje($reg_conflictos->get_reporte($path_log));
		} else {
			return $reg_conflictos;
		}
	}

	/**
	 * Importa una personalizacion, tiene 2 modos de accion:
	 * Transaccion Global, se importa todo o nada.
	 * Transaccion a nivel de componente, se importa solo lo que no da error.
	 */
	function aplicar()
	{
		if (!$this->existe()) {
			throw  new  toba_error("PERSONALIZACION: No existe la carpeta de personalización");
		}
		
		//Instancio ambos importadores
		$importador_tablas = new toba_importador_tablas($this->dir_tablas.self::nombre_plan, $this->db);
		$importador_componentes =  new  toba_importador_componentes($this->dir_componentes.self::nombre_plan, $this->db);		
		
		//Empiezo haciendo el chequeo de conflictos para los componentes
		//En una transaccion destinada a abortar
		//------------------------------------------------------------------------------------------//
		$this->db->abrir_transaccion();
		$this->analizar_conflictos($importador_tablas, false);
		$this->analizar_conflictos($importador_componentes, false);
		$this->db->abortar_transaccion();
		//------------------------------------------------------------------------------------------//
		
		//Comienzo la importacion propiamente dicha		
		if ($this->ejecutar_en_transaccion_global()) {			
			$this->db->abrir_transaccion();					
			$this->db->retrasar_constraints();	//Retraso los triggers para evitar problemas de fk
		}		
		
		//Aplico la personalizacion a tablas y componentes
		try {
			$this->aplicar_cambios($importador_tablas);
			$this->aplicar_cambios($importador_componentes);
		} catch (toba_error_db $e) {
			$this->db->abortar_transaccion();				//Hubo problemas de SQL, aborto todo
			if ($this->consola) {
				$this->consola->mensaje("Ocurrio un error en la importacion \n");
			}
		} catch(toba_error_usuario $e) {
			$this->db->abortar_transaccion();				//El usuario decidio no continuar, saco mensaje por pantalla
			if ($this->consola) {
				$this->consola->mensaje($e->getMessage());
			}
		}
				
		if ($this->db->transaccion_abierta() && $this->ejecutar_en_transaccion_global()) {
			$this->db->cerrar_transaccion();				//Cierro la transaccion si aun esta abierta. Esto es, se ejecuto sin problemas
		}
	}

	//-------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//										PROCESOS										      //
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------//
	protected function exportar_tablas()
	{
		$schema_o = $this->get_schema_original();
		$schema_a = $this->get_schema_personalizacion();
		
		$rec =  new toba_recuperador_tablas($this->proyecto, $schema_a, $schema_o);
		$tablas = $rec->get_data();
		$diff = $tablas->get_diferentes();
		
		$generador =  new  toba_pers_xml_generador_tablas();
		$generador->init_plan($this->dir_tablas . toba_personalizacion::nombre_plan);
		$generador->generar_tablas($this->dir_tablas, $diff);
		$generador->finalizar_plan();
		$this->get_db()->set_schema($schema_o);
	}
	
	protected function exportar_componentes()
	{
		$plan_nombre = $this->dir_componentes.toba_personalizacion::nombre_plan;
		$schema_o = $this->get_schema_original();
		$schema_a = $this->get_schema_personalizacion();

		$rec =  new  toba_recuperador_componentes($this->proyecto, $schema_a, $schema_o);
		$datos = $rec->get_data();

		$unicos_o = $datos->get_unicos($schema_o);
		$diff = $datos->get_diferentes();
		$unicos_a = $datos->get_unicos($schema_a);
		
		$generador =  new  toba_pers_xml_generador_componentes();
		$generador->init_plan($plan_nombre);
		$generador->generar_componentes_borradas($this->dir_componentes, $unicos_o);
		$generador->generar_componentes_modificadas($this->dir_componentes, $diff);
		$generador->generar_componentes_nuevas($this->dir_componentes, $unicos_a);				
		$generador->finalizar_plan();
		$this->get_db()->set_schema($schema_o);
	}
	

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * @param toba_importador $importador
	 * @param boolean $exportar_a_archivo 
	 */
	protected function analizar_conflictos($importador, $exportar_a_archivo = true)
	{
		while ($tarea = $importador->get_siguiente_tarea()) {
			$tarea->registrar_conflictos($exportar_a_archivo);
		}		
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------//	
	/**
	 * @param toba_importador $importador 
	 */
	protected function aplicar_cambios($importador)
	{
		$importador->rewind();							//Reposiciono el iterador al comienzo debido al chequeo de conflictos
		while ($tarea = $importador->get_siguiente_tarea()) {
			if ($this->ejecutar_en_transaccion_global()) {		//Si hay una transaccion global se lo informo a la tarea
				$tarea->set_ejecuta_transaccion_global();
			}
			$tarea->ejecutar($this->consola);
		}		
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------//
	//								METODOS AUXILIARES									      //
	//------------------------------------------------------------------------------------------------------------------------------------------------------//	
	function get_db()
	{
		return $this->db;
	}

	function get_proyecto()
	{
		return $this->proyecto;
	}

	/**
	 * @return string Devuelve el directorio de personalizacion de este proyecto
	 */
	function get_dir()
	{
		return $this->dir;
	}

	function get_dir_metadatos()
	{
		return $this->dir_metadatos;
	}

	function get_schema_original()
	{		
		if ($this->ini->existe_entrada(self::schema_original)) {
			$schema =  $this->ini->get_datos_entrada(self::schema_original);
		} else {
			$schema = $this->get_schema_instalacion();
			if (is_null($schema)) {
				$schema =  $this->db->get_schema();											//Fallback en el actual de la bd
			}
		}
		return $schema;
	}

	function get_schema_personalizacion()
	{
		return $this->ini->get_datos_entrada(self::schema_temporal);
	}

	protected function existe()
	{
		return is_dir($this->dir);
	}

	private function kill_schemas($nombre)
	{
		$nombre_t = $nombre . '_logs';
		$this->db->ejecutar("DROP SCHEMA IF EXISTS $nombre_t CASCADE;");			//Si existe schema previo de personalizacion lo borramos.
		$this->db->ejecutar("DROP SCHEMA IF EXISTS $nombre CASCADE;");
	}
	
	protected function cargar_ini()
	{
		$path = $this->dir . self::archivo_ini;
		if (!is_file($path)) {
			throw  new toba_error("PERSONALIZACION: El archivo de personalizacion '$path' no existe");
		}

		$this->ini =  new  toba_ini($path);
	}

	protected function set_iniciada($iniciada)
	{
		$iniciada = ($iniciada) ? 'si' : 'no';
		$this->ini->agregar_entrada(self::iniciada, $iniciada);
	}

	protected function iniciada()
	{
		return $this->ini->get_datos_entrada(self::iniciada) == 'si';
	}

	protected function cambiar_schema_proyecto($schema)
	{
		$instalacion = $this->proyecto->get_instalacion();
		$instancia = $this->proyecto->get_instancia();
		$params = $instalacion->get_parametros_base($instancia->get_ini_base());
		$params['schema'] = $schema;
		$instalacion->actualizar_db($instancia->get_ini_base(), $params);
	}

	protected function get_schema_instalacion()
	{
		$instalacion = $this->proyecto->get_instalacion();
		$instancia = $this->proyecto->get_instancia();
		$params = $instalacion->get_parametros_base($instancia->get_ini_base());
		return (isset($params['schema'])) ? $params['schema'] : null;
	}
	
	protected function set_schema_original($schema)
	{
		if (is_null($schema)) {
			$this->ini->eliminar_entrada(self::schema_original);
		} else {
			$this->ini->agregar_entrada(self::schema_original, $schema);
		}
	}

	protected function init_dirs()
	{
		$this->dir = $this->proyecto->get_dir().'/'.self::dir_personalizacion.'/';
		$this->dir_metadatos = $this->dir . self::dir_metadatos_xml;
		
		$this->dir_tablas = $this->dir_metadatos . self::dir_tablas;
		$this->dir_componentes = $this->dir_metadatos . self::dir_componentes;
	}

	protected function crear_directorios()
	{	
		toba_manejador_archivos::crear_arbol_directorios($this->dir);

		toba_manejador_archivos::crear_arbol_directorios($this->dir_tablas);

		toba_manejador_archivos::crear_arbol_directorios($this->dir_componentes);
		
		toba_manejador_archivos::crear_arbol_directorios($this->dir_componentes.self::dir_nuevos);
		
		toba_manejador_archivos::crear_arbol_directorios($this->dir_componentes.self::dir_modificados);
		
		toba_manejador_archivos::crear_arbol_directorios($this->dir_componentes.self::dir_borrados);		
	}		
	
	function set_ejecucion_con_transaccion_global()
	{
		$this->modo_ejecucion_transcaccional = true;
	}
	
	function ejecutar_en_transaccion_global()
	{
		return $this->modo_ejecucion_transcaccional;
	}
	
	protected function generar_schema_diff()
	{
		$schema_o = $this->get_schema_original();
		$schema_logs_o = $schema_o . '_logs';
		
		$schema_t = $this->get_schema_personalizacion();
		$schema_logs_t = $schema_t . '_logs';		
		
		$this->get_db()->abrir_transaccion();
		try {
			//1.-  Renombrar el schema actual, al schema personalizado para que no rompa luego
			$this->get_db()->renombrar_schema($schema_logs_o, $schema_logs_t);
			$this->get_db()->renombrar_schema($schema_o, $schema_t);

			//2.-  Indicarle al proyecto cual es el directorio de carga de los metadatos que debe usar
			$this->get_proyecto()->get_instancia()->set_dir_carga_proyecto($this->proyecto->get_id(), self::dir_metadatos_originales);
			
			//3.- Realizar la carga de la instancia, re-creando previamente el schema original que consta en bases.ini
			$this->get_proyecto()->get_instancia()->crear_schema();
			$this->get_db()->retrasar_constraints();
			$this->get_proyecto()->get_instancia()->cargar_autonomo();
			$this->get_db()->cerrar_transaccion();
			$this->get_db()->set_schema($schema_o);
		} catch (toba_error_db $e) {
			$this->get_db()->abortar_transaccion();
			toba_logger::instancia()->error($e->getMessage());
			throw new toba_error_usuario('Hubo un inconveniente al intentar exportar la personalización, revise el log');
		}				
	}
	
	protected function restaurar_schema_trabajo()
	{
		$schema_o = $this->get_schema_original();
		$schema_logs_o = $schema_o . '_logs';
		
		$schema_t = $this->get_schema_personalizacion();
		$schema_logs_t = $schema_t . '_logs';	
		
		$this->get_db()->abrir_transaccion();
		try {
			//Elimino los schemas con los metadatos originales
			$this->kill_schemas($schema_o);			
			
			//Renombro los schemas de metadatos personalizados para que pueda seguir trabajando
			$this->get_db()->renombrar_schema($schema_logs_t, $schema_logs_o);
			$this->get_db()->renombrar_schema($schema_t, $schema_o);
			
			$this->get_db()->cerrar_transaccion();
		} catch (toba_error_db $e) {
			$this->get_db()->abortar_transaccion();
			toba_logger::instancia()->error($e->getMessage());
			throw new toba_error_usuario('Hubo un inconveniente al intentar restaurar la instancia de trabajo, revise el log');
		}	
	}
}
?>
