<?php
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
	const dir_metadatos		= 'metadatos/';
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
	

	function  __construct(toba_modelo_proyecto $proyecto, $consola = null)
	{
		$this->proyecto = $proyecto;
		$this->db		= $this->proyecto->get_db();
		$this->consola	= $consola;
		$this->init_dirs();
		$this->cargar_ini();
	}

	function iniciar()
	{
		$schema_o = $this->get_schema_original();
		$schema_t = $this->get_schema_personalizacion();

		$this->db->set_schema('public');	// en este schema insertamos la funcion
		$this->db->ejecutar("DROP SCHEMA IF EXISTS $schema_t CASCADE;");
		$this->db->clonar_schema($schema_o, $schema_t);

		// se cambia el schema del proyecto para que todos los cambios sean sobre el nuevo schema
		$this->cambiar_schema_proyecto($schema_t);
		$this->set_schema_original($schema_o);

		$this->db->set_schema($schema_t);
		$this->set_iniciada(true);
		$this->ini->guardar();
	}

	function exportar()
	{
		if (!$this->iniciada()) {
			throw  new  toba_error("PERSONALIZACION: Debe iniciar la personalización antes de exportarla");
		}
		$this->crear_directorios();

		$this->exportar_tablas();
		$this->exportar_componentes();
	}

	function chequear_conflictos()
	{
		$this->conflictos_tablas();
		$this->conflictos_componentes();
		$path_log = $this->dir.'logs/conflictos.log';
		$reg_conflictos = self::get_registro_conflictos();
		if ($this->consola) {
			$this->consola->mensaje($reg_conflictos->get_reporte($path_log));
		} else {
			return $reg_conflictos;
		}
	}

	function aplicar()
	{
		if (!$this->existe()) {
			throw  new  toba_error("PERSONALIZACION: No existe la carpeta de personalización");
		}

		$this->aplicar_tablas();
		$this->aplicar_componentes();
	}


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
			return $this->ini->get_datos_entrada(self::schema_original);
		} else {
			return $this->db->get_schema();
		}
	}

	function get_schema_personalizacion()
	{
		return $this->ini->get_datos_entrada(self::schema_temporal);
	}


	protected function existe()
	{
		return is_dir($this->dir);
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
		$this->dir_metadatos = $this->dir . self::dir_metadatos;
		
		$dirs = array(
			'tablas' => $this->dir_metadatos . self::dir_tablas,
			'componentes' => $this->dir_metadatos . self::dir_componentes
		);
		$this->dir_tablas = $dirs['tablas'];
		$this->dir_componentes = $dirs['componentes'];

	}

	protected function crear_directorios()
	{	
		toba_manejador_archivos::crear_arbol_directorios($this->dir);

		toba_manejador_archivos::crear_arbol_directorios($this->dir_tablas);

		toba_manejador_archivos::crear_arbol_directorios($this->dir_componentes);
		$sub_dirs = array(	$this->dir_componentes.self::dir_nuevos
							, $this->dir_componentes.self::dir_modificados
							, $this->dir_componentes.self::dir_borrados);

		foreach ($sub_dirs as $sub_dir) {
			toba_manejador_archivos::crear_arbol_directorios($sub_dir);
		}
	}

	//*************************************************************************
	//TABLAS
	//*************************************************************************

	protected function exportar_tablas()
	{
		$schema_o = $this->get_schema_original();
		$schema_a = $this->get_schema_personalizacion();
		$rec =  new toba_recuperador_tablas($this->proyecto, $schema_a, $schema_o);
		$tablas = $rec->get_data();
		
		$generador =  new  toba_pers_xml_generador_tablas();
		$generador->init_plan($this->dir_tablas . toba_personalizacion::nombre_plan);
		$generador->generar_tablas($this->dir_tablas, $tablas->get_diferentes());
		$generador->finalizar_plan();
	}

	/**
	 * @param toba_registro_conflictos $conflictos
	 */
	protected function conflictos_tablas()
	{
		$importador = new toba_importador_tablas($this->dir_tablas.self::nombre_plan, $this->db);
		while ($tarea = $importador->get_siguiente_tarea()) {
			$tarea->registrar_conflictos();
		}
	}

	protected function aplicar_tablas()
	{
		$importador = new toba_importador_tablas($this->dir_tablas.self::nombre_plan, $this->db);
		while ($tarea = $importador->get_siguiente_tarea()) {
			$tarea->ejecutar($this->consola);
		}
	}


	//*************************************************************************
	//COMPONENTES
	//*************************************************************************

	protected function exportar_componentes()
	{
		$schema_o = $this->get_schema_original();
		$schema_a = $this->get_schema_personalizacion();

		$rec =  new  toba_recuperador_componentes($this->proyecto, $schema_a, $schema_o);
		$datos = $rec->get_data();

		$generador =  new  toba_pers_xml_generador_componentes();
		$generador->init_plan($this->dir_componentes.toba_personalizacion::nombre_plan);
		$generador->generar_componentes_borradas($this->dir_componentes, $datos->get_unicos($schema_o));
		$generador->generar_componentes_modificadas($this->dir_componentes, $datos->get_diferentes());
		$generador->generar_componentes_nuevas($this->dir_componentes, $datos->get_unicos($schema_a));
		$generador->finalizar_plan();
	}

	protected function conflictos_componentes()
	{
		$importador =  new  toba_importador_componentes($this->dir_componentes.self::nombre_plan, $this->db);
		while ($tarea = $importador->get_siguiente_tarea()) {
			$tarea->registrar_conflictos();
		}
	}

	protected function aplicar_componentes()
	{
		$importador =  new  toba_importador_componentes($this->dir_componentes.self::nombre_plan, $this->db);

		while ($tarea = $importador->get_siguiente_tarea()) {
			$tarea->ejecutar($this->consola);
		}
	}
}
?>
