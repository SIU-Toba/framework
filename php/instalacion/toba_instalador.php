<?php
require_once('modelo/lib/toba_proceso_gui.php');
require_once('nucleo/toba_nucleo.php');

class toba_instalador
{
	static protected $indice_archivos;	
	protected $progreso;

	function __construct($progreso=null)
	{
		if (! isset($progreso)) {
			$progreso = new toba_mock_proceso_gui();
		}
		self::$indice_archivos = toba_nucleo::get_indice_archivos();
		unset(self::$indice_archivos['toba']);	//Para que el logger se de cuenta de que esta en la consola
		spl_autoload_register(array('toba_instalador', 'cargador_clases'));
		self::cargar_includes_basicos();
		$this->set_progreso($progreso);
	}
	
	function set_conexion($base)
	{
	    toba_modelo_instalacion::set_conexion_externa($base);
	}
	
	
	function set_progreso($progreso)
	{
		if (isset($progreso)) {
			$this->progreso = $progreso;
		}
	}
	
	function grabar_logs()
	{
		$dir_logs = toba_modelo_instalacion::dir_base()."/logs_comandos";
		toba_logger::instancia()->set_directorio_logs($dir_logs);
		toba_logger::instancia()->guardar_en_archivo('comandos.log');		
	}
	
	//--------------------------------------------------------------
	// Carga de clases
	//--------------------------------------------------------------
	
	static function cargador_clases($clase)
	{
		if(isset(self::$indice_archivos[$clase])) {
			require_once( toba_nucleo::toba_dir() .'/php/'. self::$indice_archivos[$clase]);
		}
	}
	
	function cargar_includes_basicos()
	{
		foreach(toba_nucleo::get_includes_funciones_globales() as $archivo ) {
			require_once( toba_nucleo::toba_dir() . $archivo);
		}
	}

	
	//--------------------------------------------------------------
	// Acceso al catalogo del modelo
	//--------------------------------------------------------------

	/**
	 * @return toba_modelo_instalacion
	 */
	function get_instalacion()
	{
		return toba_modelo_catalogo::instanciacion()->get_instalacion($this->progreso);
	}
	
	/**
	 * @return toba_modelo_instancia
	 */
	function get_instancia($id)
	{
		return toba_modelo_catalogo::instanciacion()->get_instancia($id, $this->progreso);
	}

	
	//--------------------------------------------------------------
	// Métodos comprometidos con GTK
	//--------------------------------------------------------------
	
	function get_comp_administracion($id_proyecto=null)
	{
		require_once('instalacion/toba_gtk_admin.php');
		$gtk_admin = new toba_gtk_admin($this, $this->progreso);
		return $gtk_admin->construir_dialogo($id_proyecto);
	}
	
	//--------------------------------------------------------------
	// Métodos de acceso
	//--------------------------------------------------------------
	
	/**
	 * Exporta los proyectos-no-propios de todas las instancias de la instalacion
	 */
	function instalacion_exportar($excluir_internos=true)
	{
		$instalacion = $this->get_instalacion();
		if ($excluir_internos) {
			$excluir = array('toba_editor', 'toba_referencia', 'toba_testing', 'toba_usuarios');
		} else {
			$excluir = array();
		}
		foreach ($instalacion->get_lista_instancias() as $id_inst) {
			$instancia = $instalacion->get_instancia($id_inst);
			$instancia->exportar($excluir);
		}
	}

	/**
	 * Regenera todas las instancias de la instalacion
	 */
	function instalacion_regenerar()
	{
		$instalacion = $this->get_instalacion();
		foreach ($instalacion->get_lista_instancias() as $id_inst) {
			$instancia = $instalacion->get_instancia($id_inst);
			$instancia->cargar(true);
		}		
	}
	
	/**
	 * Elimina todas las instancias de la instalacion
	 */
	function instalacion_eliminar_instancias()
	{
		$instalacion = $this->get_instalacion();
		foreach ($instalacion->get_lista_instancias() as $id_inst) {
			$instancia = $instalacion->get_instancia($id_inst);
			//-- Aprovecha a desinstalar los proyectos propios de toba de las instancias
			$instancia = $this->get_instancia($id_inst);
			foreach ($instancia->get_lista_proyectos_vinculados() as $id_proy) {
				$proy_propios = array('toba_editor', 'toba_referencia', 'toba_testing', 'toba_instancia');
				if (in_array($id_proy, $proy_propios)) {
					$instancia->get_proyecto($id_proy)->desinstalar();
				}
			}
			$instancia->eliminar();
		}		
	}	
	
	function instalacion_migrar($version)
	{
		$version = new toba_version($version);
		$instalacion = $this->get_instalacion();
		$instalacion->migrar_version($version, true);
	}
	
	/**
	 * Retorna el path del archivo de configuración de apache resultante
	 */
	function crear_instalacion_e_instancia($nombre_instancia, $datos_motor, $grupo)
	{
		$instalacion = $this->get_instalacion();
		$nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_string_partes();
		$alias = '/'.$nombre_toba;
		
		$nombre = $instalacion->get_nombre();
		//--- Borra todo rastro anterior
		if (toba_modelo_instalacion::existe_info_basica() ) {		
			toba_modelo_instalacion::borrar_directorio();
		}
		//--- Crea el dir instalacion
		toba_modelo_instalacion::crear($grupo, $alias, $nombre );
		
		//--- Crea la instancia
		$this->crear_instancia($nombre_instancia, $datos_motor);
		
		$toba_conf = toba_modelo_instalacion::dir_base()."/toba.conf";
		return $toba_conf;
	}

	function crear_instancia($nombre_instancia, $datos_motor)
	{
		$nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_string_partes();
				
		//--- Agrega la fuente de datos de la instancia
		$datos_motor['base'] = $nombre_toba.'_'.$nombre_instancia;
		$confirmado = false;
		do {
			$this->get_instalacion()->agregar_db( $nombre_instancia, $datos_motor );
			//--- Si la base existe, pregunta por un nombre alternativo, por si no quiere pisarla
			if ($this->get_instalacion()->existe_base_datos($nombre_instancia)) {
				$mensaje = "La base <b>{$datos_motor['base']}</b> ya está siendo utiliza en este servidor.";
				$confirmado = inst_fact::gtk()->confirmar_pisar_base($mensaje);
				if ($confirmado !== true) {																
					$datos_motor['base'] = $confirmado;
					$confirmado = false;
				}
			} else {
				$confirmado = true;
			}
		} while ($confirmado === false);	
		
			//--- Crea la instancia
		$proyectos = toba_modelo_proyecto::get_lista();
		if (isset($proyectos['toba_testing'])) {
			//--- Elimina el proyecto toba_testing 
			unset($proyectos['toba_testing']);
		}
		toba_modelo_instancia::crear_instancia( $nombre_instancia, $nombre_instancia, $proyectos);
		$instancia = $this->get_instancia($nombre_instancia);
		$instancia->cargar( true );
		
		//--- Vincula un usuario a todos los proyectos
		$instancia->agregar_usuario( 'toba', 'Usuario Toba', 'toba');
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$proyecto = $instancia->get_proyecto($id_proyecto);
			$grupo_acceso = $proyecto->get_grupo_acceso_admin();
			$proyecto->vincular_usuario( 'toba', $grupo_acceso );
		}
		$instancia->exportar_local();
		
		//--- Crea los nuevos alias
		$instancia->crear_alias_proyectos();

		//--- Ejecuta instalaciones particulares de cada proyecto
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$instancia->get_proyecto($id_proyecto)->instalar();
		}			
	}
	
	/**
	 * Carga el proyecto en una instancia y ejecuta el proceso de instalacion y creación de 
	 * la instancia de negocio de ese proyecto
	 */
	function proyecto_cargar_e_instalar($proy_id, $proy_path, $nombre_instancia, $datos_motor, $url=null)
	{
		$instalacion = $this->get_instalacion();
		if (! $this->get_instalacion()->existe_instancia($nombre_instancia)) {
			$this->crear_instancia($nombre_instancia, $datos_motor);
		}
		$instancia = $this->get_instancia($nombre_instancia);

		$existe = false;
		if ($instancia->existe_proyecto_vinculado($proy_id)) {
			if (! $this->progreso->dialogo_simple("Ya existe el proyecto $proy_id en la instancia $nombre_instancia. ¿Desea reemplazarlo?")) {
				return;
			} else {
				$instancia->eliminar_proyecto($proy_id);
			}
		}
		//--- Vincula el proyecto y lo carga a la instancia
		$this->progreso->mensaje("Cargando el proyecto en la instancia $nombre_instancia...");
		$instancia->vincular_proyecto($proy_id, $proy_path, $url);
		$proyecto = $instancia->get_proyecto($proy_id);
		$proyecto->cargar_autonomo();

		//--- Vincula los usuarios existententes a un grupo de acceso administrador
		$this->progreso->mensaje("Vinculando usuarios...");
		$grupo_acceso = $proyecto->get_grupo_acceso_admin();		
		foreach ( $instancia->get_lista_usuarios() as $usuario ) {
			$proyecto->vincular_usuario( $usuario['usuario'], $grupo_acceso );
			$this->progreso->progreso_avanzar();
		}
		$instancia->exportar_local();
		$proyecto->publicar();			
		//--- Ventana de instalacion propia del proyecto
		$proyecto->instalar();
	}

	/**
	 * Recorre todas las instancias donde se encuentra el proyecto y exporta sus metadatos
	 * @param string $proy_id
	 */
	function proyecto_exportar_local($proy_id)
	{
		$instalacion = $this->get_instalacion();
		foreach ($instalacion->get_lista_instancias() as $id_instancia) {
			$instancia = $this->get_instancia($id_instancia);
			if ($instancia->existe_proyecto_vinculado($proy_id)) {
				$instancia->exportar_local();
			}
		}
	}
	
	
	
	/**
	 * Recorre todas las instancias donde se encuentra el proyecto y exporta sus metadatos
	 * @param string $proy_id
	 */
	function proyecto_exportar($proy_id)
	{
		$instalacion = $this->get_instalacion();
		foreach ($instalacion->get_lista_instancias() as $id_instancia) {
			$instancia = $this->get_instancia($id_instancia);
			if ($instancia->existe_proyecto_vinculado($proy_id)) {
				$instancia->get_proyecto($proy_id)->exportar();
				$instancia->exportar_local();
			}
		}
	}	
	
	/**
	 * Recorre todas las instancias donde se encuentra el proyecto y elimina sus metadatos y datos de negocio opcionalmente
	 * @param string $proy_id
	 */
	function proyecto_eliminar($proy_id, $desinstalar)
	{
		$instalacion = $this->get_instalacion();
		foreach ($instalacion->get_lista_instancias() as $id_instancia) {
			$instancia = $this->get_instancia($id_instancia);
			if ($instancia->existe_proyecto_vinculado($proy_id)) {
				$instancia->get_proyecto($proy_id)->despublicar();
				$instancia->eliminar_proyecto($proy_id, $desinstalar);
			}
		}
	}	

	
	/**
	 * Recorre todas las instancias donde se encuentra el proyecto y regenera sus metadatos
	 * @param string $proy_id
	 */	
	function proyecto_regenerar($proy_id)
	{
		$instalacion = $this->get_instalacion();
		foreach ($instalacion->get_lista_instancias() as $id_instancia) {
			$instancia = $this->get_instancia($id_instancia);
			if ($instancia->existe_proyecto_vinculado($proy_id)) {
				$instancia->get_proyecto($proy_id)->regenerar();
			}
		}
	}

	/**
	 * Recorre todas las instancias donde se encuentra el proyecto y migra sus datos de negocio
	 * @param string $proy_id
	 */		
	function proyecto_migrar($proy_id, $version_numero)
	{
		$version_desde = new toba_version($version_numero);
		$instalacion = $this->get_instalacion();
		foreach ($instalacion->get_lista_instancias() as $id_instancia) {
			$instancia = $this->get_instancia($id_instancia);
			if ($instancia->existe_proyecto_vinculado($proy_id)) {
				$proyecto = $instancia->get_proyecto($proy_id);
				$proyecto->migrar_datos_negocio($version_desde, $proyecto->get_version_proyecto());
			}
		}		
	}

}
?>