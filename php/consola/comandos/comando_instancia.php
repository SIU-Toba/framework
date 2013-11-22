<?php
require_once('comando_toba.php');
/**
*	Publica los servicios de la clase INSTANCIA a la consola toba
*/
class comando_instancia extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de INSTANCIAS';
	}

	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba instancia OPCION [-i id_instancia]");
		$this->consola->enter();
		$this->get_info_parametro_instancia();
		$this->consola->enter();
	}

	function get_info_extra()
	{
		$i = $this->get_instancia();
		try {
			$salida = "Versión: ".$i->get_version_actual()->__toString();
		} catch (toba_error_db $e) {
			$salida = $e->getMessage();
		}
		$db = $i->get_parametros_db();
		$salida .= "\nBase: {$db['profile']} / {$db['base']}";
		return $salida;
	}
		
	
	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	* Crea una instancia NUEVA. 
	* @consola_parametros [-t mini] se crea una instancia reducida, útil para ejecutar proyectos compilados
	* @gtk_icono nucleo/agregar.gif 
	* @gtk_no_mostrar 1
	*/
	function opcion__crear($datos=null)
	{
		if (isset($datos)) {
			list($id_instancia, $tipo, $base, $proyectos, $usuario) = $datos;
		} else {
			$id_instancia = $this->get_id_instancia_actual();			
			$tipo = $this->get_tipo_instancia();
			$usuario = null;
		}
		$instalacion = $this->get_instalacion();
		if ( toba_modelo_instancia::existe_carpeta_instancia($id_instancia) ) {
			throw new toba_error("Ya existe una INSTANCIA con el nombre '$id_instancia'");
		}
		if ( ! $instalacion->hay_bases() ) {
			throw new toba_error("Para crear una INSTANCIA, es necesario definir al menos una BASE. Utilice el comando 'toba base registrar'");
		}
		$this->consola->titulo("Creando la INSTANCIA: $id_instancia TIPO: $tipo");

		//---- A: Creo la definicion de la instancia
		$this->consola->enter();
		if (!isset($base)) {
			$base = $this->seleccionar_base();
		}
		if (!isset($proyectos)) {
			$proyectos = $this->seleccionar_proyectos();
		}
		toba_modelo_instancia::crear_instancia( $id_instancia, $base, $proyectos, $tipo );

		//---- B: Cargo la INSTANCIA en la BASE
		$instancia = $this->get_instancia($id_instancia);
		if($tipo == 'mini') {
			$metodo_carga = 'cargar_tablas_minimas';
		} else {
			$metodo_carga = 'cargar';
		}
		try {
			$instancia->$metodo_carga();
		} catch ( toba_error_modelo_preexiste $e ) {
			$this->consola->error( 'ATENCION: Ya existe una instancia en la base de datos seleccionada' );
			$this->consola->lista( $instancia->get_parametros_db(), 'BASE' );
			if ( $this->consola->dialogo_simple('Desea ELIMINAR la instancia y luego CARGARLA (La informacion local previa se perdera!)?') ) {
				$instancia->$metodo_carga( true );
			} else {
				return;	
			}
		} catch ( toba_error $e ) {
			$this->consola->error( 'Ha ocurrido un error durante la importacion de la instancia.' );
			$this->consola->error( $e->getMessage() );
		}

		//---- C: Actualizo la versión, Creo un USUARIO y lo asigno a los proyectos
		$instancia->set_version( toba_modelo_instalacion::get_version_actual());
		$this->opcion__crear_usuario($usuario, false, $id_instancia);

		if($tipo != 'mini') {
			//---- D: Exporto la informacion LOCAL
			$instancia->exportar_local();
			//-- Agregar los alias
			$this->consola->enter();		
			$crear_alias = $this->consola->dialogo_simple("Desea crear automáticamente los alias de apache en el archivo toba.conf?", true);
			if ($crear_alias) {
				$instancia->crear_alias_proyectos();
			}
		}
		//Creo el esquema basico de logs de Toba.
		//$instancia->crear_modelo_logs_toba();		
	}

	/**
	* Brinda informacion sobre la instancia.
	* @gtk_icono info_chico.gif 
	* @gtk_no_mostrar 1
	*/
	function opcion__info()
	{
		$i = $this->get_instancia();
		$param = $this->get_parametros();
		$this->consola->titulo( 'INSTANCIA: ' . $i->get_id() );
		if ( isset( $param['-u'] ) ) {
			// Lista de USUARIOS
			$this->consola->subtitulo('Listado de USUARIOS');
			$this->consola->tabla( $i->get_lista_usuarios(), array( 'Usuario', 'Nombre') );
		} else {										
			// Informacion BASICA
			$this->consola->subtitulo('Informacion BASICA');
			//VERSION
			$this->consola->lista(array($i->get_version_actual()->__toString()), "VERSION");
			$this->consola->lista_asociativa( $i->get_parametros_db() , array('Parametros Conexion', 'Valores') );
			$this->consola->lista( $i->get_lista_proyectos_vinculados(), 'Proyectos Vinculados' );
			$this->consola->enter();
			$this->consola->subtitulo('Reportes');
			$subopciones = array( '-u' => 'Listado de usuarios' ) ;
			$this->consola->coleccion( $subopciones );			
		}
	}
	
	/**
	* Crea un nuevo proyecto asociado a la instancia
	* @consola_no_mostrar 1 
	* @gtk_icono nucleo/proyecto.gif
	*/	
	function opcion__crear_proyecto()
	{
		//------ESTO ES UN ALIAS DE PROYECTO::CREAR
		require_once('comando_proyecto.php');
		$comando = new comando_proyecto($this->consola);
		$comando->set_id_instancia_actual($this->get_id_instancia_actual());
		$comando->opcion__crear();
	}
	
	/**
	* Carga un PROYECTO en la INSTANCIA (Carga metadatos y crea un vinculo entre ambos elementos).
	* @consola_no_mostrar 1 
	* @gtk_icono nucleo/proyecto.gif
	* @gtk_param_extra cargar_proyecto
	*/	
	function opcion__cargar_proyecto($datos = null)
	{
		//------ESTO ES UN ALIAS DE PROYECTO::CARGAR
		require_once('comando_proyecto.php');
		$comando = new comando_proyecto($this->consola);
		$comando->set_id_instancia_actual($this->get_id_instancia_actual());
		$comando->opcion__cargar($datos);		
	}	
	
	/**
	* Importa y migra un proyecto desde otra instalacion de toba. Se asume que el código del proyecto se encuentra en la carpeta PROYECTOS de toba
	* @consola_no_mostrar 1 
	* @gtk_icono nucleo/proyecto.gif
	* @gtk_separador 1 
	* @gtk_param_extra importar_proyecto
	*/	
	function opcion__importar_proyecto($datos = null)
	{
		//------ESTO ES UN ALIAS DE PROYECTO::IMPORTAR
		require_once('comando_proyecto.php');
		$comando = new comando_proyecto($this->consola);
		$comando->set_id_instancia_actual($this->get_id_instancia_actual());
		$comando->opcion__importar($datos);
	}
	
	
	/**
	* Exporta la instancia completa incluyendo METADATOS propios y de proyectos contenidos.
	* @gtk_icono exportar.png 
	*/
	function opcion__exportar()
	{
		$this->get_instancia()->exportar();
	}

	/**
	 * Exporta los METADATOS propios de la instancia de la DB (exclusivamente la información local).
	 * @gtk_icono exportar.png	 
	 */
	function opcion__exportar_local()
	{
		$this->get_instancia()->exportar_local();
	}

	/**
	 * Elimina la instancia y la vuelve a cargar.
	 * @gtk_icono importar.png
	 */
	function opcion__regenerar()
	{
		if ($this->get_instancia()->existe_modelo()) {
			$timestamp = $this->get_instancia()->get_fecha_exportacion_local();
			if (isset($timestamp)) {
				$extra = "Si responde NO, se utilizaran los exportados el ".date("D j-M-y \a \l\a\s h:m ", $timestamp);
			} else {
				$extra = "Si responde NO, la instancia quedara sin usuarios y será inaccesible";
			}
			$extra .= "\n";
			if ( $this->consola->dialogo_simple('Desea conservar datos locales como usuarios y logs?', true, $extra) ) {
				$this->opcion__exportar_local();
			}
		}							
		$this->consola->enter();
		$i = $this->get_instancia();
		$this->consola->lista($i->get_parametros_db(), 'BASE');
		$forzar = false;
		
		//Para ejecutar migraciones a la instancia mediante el instalador		
		$this->get_instancia()->ejecutar_ventana_migracion_version();
		if ($i->existe_modelo()) {
			$this->consola->mensaje("Se guardaran los datos existentes en un schema backup");
			$forzar = true;
		}
		$this->get_instancia()->cargar($forzar);
	}

	
	/**
	* Carga una instancia en la DB referenciada, partiendo de los METADATOS en el sistema de archivos.
	* @gtk_icono importar.png 
	*/
	function opcion__cargar()
	{
		try {
			$this->get_instancia()->cargar();
		} catch ( toba_error_modelo_preexiste $e ) {
			$this->consola->error( 'Ya existe una instancia en la base de datos' );
			$this->consola->lista( $this->get_instancia()->get_parametros_db(), 'BASE' );
			if ( $this->consola->dialogo_simple('Desea ELIMINAR la instancia y luego CARGARLA?') ) {
				$this->get_instancia()->cargar( true );
			}
		} catch ( toba_error $e ) {
			$this->consola->error( 'Ha ocurrido un error durante la importacion de la instancia.' );
			$this->consola->error( $e->getMessage() );
		}
	}
	
	/**
	 * Importa los METADATOS locales desde otra instalacion/instancia
	 * @consola_parametros Opcionales: [-o instancia origen] [-d 'directorio toba'] [-r 0|1 Reemplazar los metadatos actuales, por defecto 0]
	 * @gtk_icono importar.png  
	 * @gtk_param_extra importar_instancia
	 */
	function opcion__importar($datos=null)
	{
		$path = null;
		$reemplazar = false;
		if (! isset($datos)) {
			$param = $this->get_parametros();
			if ( isset($param['-o']) &&  (trim($param['-o']) != '') ) {
				$origen = $param['-o'];
			} else {
				$origen = $this->get_id_instancia_actual(true);
			}
			if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
				$path = $param['-d'];
			}		
			if ( isset($param['-r']) &&  (trim($param['-r']) != '') ) {
				$reemplazar = $param['-r'];
			}				
		} else {
			list($origen, $path, $reemplazar) = $datos;
		}
		$this->get_instancia()->importar_informacion_instancia($origen, $path, $reemplazar);
	}	
	
	/**
	* Elimina la instancia.
	* @gtk_icono borrar.png
	*/
	function opcion__eliminar()
	{
		$i = $this->get_instancia();
		$this->consola->lista( $i->get_parametros_db(), 'BASE' );
		if ( $this->consola->dialogo_simple('Desea eliminar los datos de la INSTANCIA?') ) {
			$i->eliminar_base();
		}
		if ( $this->consola->dialogo_simple('Desea eliminar la carpeta de datos y configuración de la INSTANCIA?') ) {
			$i->eliminar_archivos();
		}		
	}

	/**
	 * Crea un usuario administrador y lo asigna a los proyectos
	 * @gtk_icono usuarios/usuario_nuevo.gif
	 * @gtk_param_extra crear_usuario
	 */
	function opcion__crear_usuario($datos=null, $asociar_previsualizacion_admin=true, $id_instancia=null)
	{
		$instancia = $this->get_instancia($id_instancia);
		if (!isset($datos)) {
			$datos = $this->get_datos_usuario();
		}
		
		$instancia->get_db()->abrir_transaccion();
		$instancia->agregar_usuario( $datos['usuario'], $datos['nombre'], $datos['clave'] );
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$proyecto = $instancia->get_proyecto($id_proyecto);
			$grupo_acceso = $this->seleccionar_grupo_acceso( $proyecto );
			$proyecto->vincular_usuario($datos['usuario'],array($grupo_acceso), null, $asociar_previsualizacion_admin);
		}
		$instancia->get_db()->cerrar_transaccion();		
	}
	
	/**
	 * Permite cambiar los grupos de acceso de un usuario 
	 * @consola_parametros [-u usuario]
	 * @gtk_icono usuarios/grupo.gif
	 */
	function opcion__editar_acceso()
	{
		$instancia = $this->get_instancia();
		$param = $this->get_parametros();
		if ( isset($param['-u']) &&  (trim($param['-u']) != '') ) {
			$usuario = $param['-u'];
		} else {
			$usuarios = $instancia->get_lista_usuarios();
			$usuarios = rs_convertir_asociativo($usuarios, array('usuario'),'nombre');
			$usuario = $this->consola->dialogo_lista_opciones( $usuarios, 'Seleccionar Usuario', false, 'Nombre de usuario', 
														true);			
		}
		if (! isset($usuario)) {
			throw new toba_error("Es necesario indicar el usuario con '-u'");			
		}
		$acceso = array();
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$this->consola->enter();			
			$proyecto = $instancia->get_proyecto($id_proyecto);
			$grupos = $proyecto->get_lista_grupos_acceso();
			$grupos = rs_convertir_asociativo($grupos, array('id'), 'nombre');
			$grupos = $this->consola->dialogo_lista_opciones($grupos, "Proyecto $id_proyecto", true, 'Descripción', false);
			if (! isset($grupos)) {
				return;
			}
			$acceso[$id_proyecto] = $grupos;
		}
		$instancia->cambiar_acceso_usuario($usuario, $acceso);
	}
	
	/**
	 * Limpia la tabla de ips bloqueadas
	 * @gtk_icono desbloquear.png
	 */
	function opcion__desbloquear_ips()
	{
		$instancia = $this->get_instancia();
		$instancia->desbloquear_ips();
	}
	
	/**
	 * Migra un instancia entre dos versiones toba.
	 * @consola_parametros Opcionales: [-d 'desde']  [-h 'hasta'] [-R 0|1] [-m metodo puntual de migracion]
	 * @gtk_icono convertir.png
	 */
	function opcion__migrar_toba()
	{
		$instancia = $this->get_instancia();
		//--- Parametros
		$param = $this->get_parametros();
		$desde = isset($param['-d']) ? new toba_version($param['-d']) : $instancia->get_version_actual();
		$hasta = isset($param['-h']) ? new toba_version($param['-h']) : toba_modelo_instalacion::get_version_actual();
		$recursivo = (!isset($param['-R']) || $param['-R'] == 1);
		
		if ($recursivo) {
			$texto_recursivo = " y proyectos contenidos";
		}
		$desde_texto = $desde->__toString();
		$hasta_texto = $hasta->__toString();
		$this->consola->titulo("Migración de la instancia '{$instancia->get_id()}'".$texto_recursivo." desde la versión $desde_texto hacia la $hasta_texto.");

		if (! isset($param['-m'])) {
			$versiones = $desde->get_secuencia_migraciones($hasta);
			if (empty($versiones)) {
				$this->consola->mensaje("No es necesario ejecutar una migración entre estas versiones para la instancia '{$instancia->get_id()}'");
				return ;
			}

			$instancia->migrar_rango_versiones($desde, $hasta, $recursivo);
		} else {
			//Se pidio un método puntual
			$this->consola->mensaje("Ejecutando método particular:". trim($param['-m']));
			$instancia->ejecutar_migracion_particular($hasta, trim($param['-m']));
		}		
	}
	
	function get_tipo_instancia()
	{
		$tipo = 'normal';
		$param = $this->get_parametros();
		if ( isset($param['-t'] ) && ( trim( $param['-t'] ) == 'mini') ) {
			$tipo = 'mini';
		}		
		return $tipo;
	}
	
	function get_datos_usuario()
	{
		//Verifico que la clave cumpla ciertos requisitos basicos
		do {
			$hubo_error = false;
			if (!isset($datos)) {
				$datos = $this->definir_usuario( "Crear USUARIO" );
			}
			if ($this->get_instalacion()->es_produccion()) {
				try {
					toba_usuario::verificar_composicion_clave($datos['clave'], apex_pa_pwd_largo_minimo);			//Hay que brindar la posibilidad de marcar produccion antes
				} catch(toba_error_pwd_conformacion_invalida $e) {
					$this->consola->mensaje($e->getMessage(), true);
					$hubo_error = true;
					unset($datos);
				}
			}
		} while ($hubo_error);
		return $datos;		
	}
}
?>