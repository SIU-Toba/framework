<?php
/**
 * A travez de esta clase el nucleo se relaciona con el proyecto toba_editor
 * Esta es una clase muy particular, su contenido deberia repartirse entre modelo,
 * proyecto editor y nucleo. Por simplicidad se deja todo junto.
 * @package Varios
 * @ignore 
 */
class toba_editor
{
	private static $memoria;	// Bindeo a $_sesion
	private static $ultimo_item;
	private static $fuentes;

	static function get_id()
	{
		return 'toba_editor';	
	}

	/**
	*	_falta: Hacer un control de que el administrador esta en esa instancia
	*			(hoy en dia seria obligatorio)
	*/
	static function iniciar($instancia, $proyecto)
	{
		if(!isset($instancia) || !isset($proyecto)) {
			throw new toba_error('Editor: es necesario definir la instancia y proyecto a utilizar.');	
		}
		self::referenciar_memoria();
		
		self::$memoria['instancia'] = $instancia;
		self::$memoria['proyecto'] = $proyecto;
		//Busco el ID de la base donde reside la instancia
		$parametros_instancia = toba::instancia()->get_datos_instancia($instancia);
		self::$memoria['base'] = $parametros_instancia['base'];
		//Averiguo el punto de acceso del editor
		$punto_acceso = explode('?', $_SERVER['PHP_SELF']);	
		self::$memoria['punto_acceso'] = $punto_acceso[0];
		self::$memoria['conexion_limitada'] = 1;
	}
	
	static function referenciar_memoria()
	{
		self::$memoria =& toba::manejador_sesiones()->segmento_editor();
		//Acceso a la informacion del modelo
		toba_contexto_info::set_proyecto(toba_editor::get_proyecto_cargado());
		toba_contexto_info::set_db(toba_editor::get_base_activa());
				
		if (! self::modo_prueba()) {
			return;
		}
		
		//Cambia el perfil activo
		$perfil_activo = toba::memoria()->get_parametro('perfil_activo');
		if (isset($perfil_activo)) {
			if ($perfil_activo == apex_ef_no_seteado) {
				toba::manejador_sesiones()->set_perfiles_funcionales_activos(toba::manejador_sesiones()->get_perfiles_funcionales());
			} else {
				toba::manejador_sesiones()->set_perfiles_funcionales_activos(array($perfil_activo));
			}
		}
		
		//Cambia el usuario de conexion
		$tipo_conexion = toba::memoria()->get_parametro('usuario_conexion');
		if (isset($tipo_conexion)) {
			self::$memoria['conexion_limitada'] = ($tipo_conexion == 'limitado');
		}
		self::$fuentes = toba_info_editores::get_fuentes_datos(toba_editor::get_proyecto_cargado());
		$modelo = self::get_modelo_proyecto();			
		foreach (self::$fuentes as $fuente) {
			try {			
				if (self::$memoria['conexion_limitada'] && $fuente['permisos_por_tabla']) {
					//El proyecto usa permisos por tablas para las operaciones, en modo previsualizacion se setea el rol especifico
					$id_base = self::$memoria['instancia'].' '.self::get_proyecto_cargado().' '.$fuente['fuente_datos'];
					$parametros = toba_dba::get_parametros_base($id_base);
					$usuario = $modelo->get_usuario_prueba_db($fuente['fuente_datos']);
					$parametros['usuario'] = $usuario;
					$parametros['clave'] = $usuario;
					toba_dba::set_parametros_base($id_base, $parametros);
				}

				//Pone la base por defecto en modo debug, para leer la cantidad y tiempo de las querys
				$base = toba_admin_fuentes::instancia()->get_fuente($fuente['fuente_datos'], toba_editor::get_proyecto_cargado())->get_db();
				if ($base) {
					$base->set_modo_debug(true, false);
				}				
			} catch (toba_error $e) {
				//Si no se tiene acceso a la base no se hace nada
			}
		}
		
		
		//Cambia el skin
		if (toba::memoria()->get_parametro('skin') != '') {
			$skin = explode(apex_qs_separador, toba::memoria()->get_parametro('skin'));
			$sql = "SELECT es_css3 FROM apex_estilo WHERE estilo = ".quote($skin[0])." AND proyecto =".quote($skin[1]);
			$datos = toba::instancia()->get_db()->consultar_fila($sql);
			toba::proyecto()->set_parametro('estilo', $skin[0]);
			toba::proyecto()->set_parametro('estilo_proyecto', $skin[1]);
			toba::proyecto()->set_parametro('es_css3', $datos['es_css3']);
			
		}
		//Cambia tipo de navegación
		if (toba::memoria()->get_parametro('navegacion_ajax') != '') {
			$ajax = toba::memoria()->get_parametro('navegacion_ajax') ? true : false;
			toba::proyecto()->set_parametro('navegacion_ajax', $ajax);
		}
		
	}

	static function finalizar()
	{
		toba::manejador_sesiones()->borrar_segmento_editor();
	}

	
	/**
	*	Indica si el EDITOR de metadatos se encuentra encendido
	*/
	static function activado()
	{
		if (count(self::$memoria)>0) {
			return true;	
		}
		return false;
	}

	/**
	*	Indica si la ejecucion actual corresponde a la previsualizacion de un proyecto 
	*		lanzada desde el admin
	*/
	static function modo_prueba()
	{
		if (self::activado() && toba::manejador_sesiones()->existe_sesion_activa() ) {
			return self::$memoria['proyecto'] == toba_proyecto::get_id();
		}
		return false;
	}

	static function get_id_instancia_activa()
	{
		if (self::activado()) {
			return self::$memoria['instancia'];
		}
	}
	
	static function get_base_activa()
	{
		if (self::activado()) {
			return toba_dba::get_db(self::$memoria['base']);
		}
	}
	
	static function get_db_defecto()
	{
		$fuente = toba_info_editores::get_fuente_datos_defecto(toba_editor::get_proyecto_cargado());
		return toba::db($fuente, toba_editor::get_proyecto_cargado());
	}

	static function get_proyecto_cargado()
	{
		if (self::activado()) {
			return self::$memoria['proyecto'];
		}
	}
	
	static function set_proyecto_cargado($proyecto)
	{
		if (self::$memoria['proyecto'] != $proyecto ) {
			//Cambio el proyecto que se esta editando, elimino la sesion del anterior.
			self::limpiar_memoria_proyecto_cargado();
		}
		self::$memoria['proyecto'] = $proyecto;
		self::get_parametros_previsualizacion(true);
	}
	
	/**
	 * Se cambio el item actual
	 */
	static function set_item_solicitado($item) 
	{
		if (isset($item) && $item[0] == self::$memoria['proyecto'] && 
				!isset(self::$ultimo_item) && self::$ultimo_item[1] != $item[1]) {
			self::$ultimo_item = $item;			
					
			$modelo = self::get_modelo_proyecto();

			if (isset(self::$fuentes)) {
				foreach (self::$fuentes as $fuente) {				
					if (self::$memoria['conexion_limitada'] && $fuente['permisos_por_tabla']) {
						try {
							$rol = $modelo->get_rol_prueba_db($fuente['fuente_datos'], $item[1]);
							if (toba::db()->existe_rol($rol)) {
								toba::db()->set_rol($rol);
							} else {
								$rol = $modelo->get_rol_prueba_db_basico($fuente['fuente_datos']);
								toba::db()->set_rol($rol);
							}
							toba::logger()->info("Se cambio el rol postgres a '$rol'");													
						} catch (toba_error_db $e) {
							toba::notificacion()->error("No fue posible cambiar el rol del usuario de conexion", $e->get_mensaje_log());
						}
					}
				}
			}
		}
	}
		
	static function get_punto_acceso_editor()
	{
		if (self::activado()) {
			return self::$memoria['punto_acceso'];
		}
	}

	/**
	*	Indica si el ADMIN se esta editando a si mismo
	*/
	static function acceso_recursivo()
	{
		if (self::activado()) {
			return self::get_proyecto_cargado() == self::get_id();
		}
		return false;		
	}

	static function limpiar_memoria_proyecto_cargado()
	{
		if ( ! toba_editor::acceso_recursivo() ) {	//Si se esta editando el editor, no es necesario
			$proyecto = toba_editor::get_proyecto_cargado();
			if ( toba::manejador_sesiones()->existe_sesion_activa($proyecto) ) {
				$msg = 'El proyecto estaba en modo edicion y el usuario finalizo la sesion del editor.';
				toba::manejador_sesiones()->abortar_sesion_proyecto($proyecto, $msg);
			} elseif (toba::manejador_sesiones()->existe_proyecto_cargado($proyecto)) {
				//El proyecto puede estar cargado para mostrar un item publico, como la pantalla de login.
				toba::manejador_sesiones()->borrar_segmento_proyecto($proyecto);
			}
		}
	}

	//---------------------------------------------------------------------------
	//-- 
	//---------------------------------------------------------------------------

	/**
	*	Inicializa el contexto del proyecto en edicion.
	*		(Utilizado en el analisis de codigo y la simulacion)
	*/
	function iniciar_contexto_proyecto_cargado()
	{
		if(!self::acceso_recursivo()){
			self::incluir_path_proyecto_cargado();
			$info = toba::proyecto()->cargar_info_basica(self::get_proyecto_cargado());
			if($info['contexto_ejecucion_subclase_archivo']&&$info['contexto_ejecucion_subclase_archivo']) {
				require_once($info['contexto_ejecucion_subclase_archivo']);
				$contexto = new $info['contexto_ejecucion_subclase']();
				$contexto->conf__inicial();
			}
		}
	}
	
	function incluir_path_proyecto_cargado()
	{
		if(!self::acceso_recursivo()){
			//La subclase puede incluir archivos del proyecto
			$path_proyecto = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . '/php';
			agregar_dir_include_path($path_proyecto);
		}		
	}

	//---------------------------------------------------------------------------
	//-- Manejo de la configuracion de PREVISUALIZACION
	//-- ( La previsualizacion es la ejecucion de un proyecto desde el ADMIN)
	//---------------------------------------------------------------------------

	/**
	*	Alimenta a la clase que representa al editor en JS
	*/
	static function get_parametros_previsualizacion_js()
	{
		$param_prev = self::get_parametros_previsualizacion();
		$param_prev['proyecto'] = self::get_proyecto_cargado();
		return $param_prev;
	}

	/**
	 * @deprecated Desde 1.5 usar get_perfiles_funcionales_previsualizacion
	 */
	static function get_grupos_acceso_previsualizacion()
	{
		return self::get_perfiles_funcionales_previsualizacion();
	}
	

	static function get_perfiles_funcionales_previsualizacion()
	{
		$param_prev = self::get_parametros_previsualizacion();
		if(isset($param_prev['grupo_acceso'])) {
			$grupos = explode(',', $param_prev['grupo_acceso'] );
			$grupos = array_map('trim', $grupos);
			return $grupos;
		} else {
			throw new toba_error("No estan definidos los perfiles de acceso a la previsualización. Desde toba_editor se pueden definir en la opción de Configuración > Previsualización");	
		}
	}	

	/**
	* @deprecated 3.0.0
	* @see toba_editor::get_perfiles_datos_previsualizacion()
	*/
	static function get_perfil_datos_previsualizacion()
	{
		$perfiles = self::get_perfiles_datos_previsualizacion();
		if (! empty($perfiles) && $perfiles !== FALSE) {
			return current($perfiles);
		}		
	}
	
	static function get_perfiles_datos_previsualizacion()
	{
		$param_prev = self::get_parametros_previsualizacion();
		if(isset($param_prev['perfil_datos'])) {
			$perfiles = explode(',', $param_prev['perfil_datos']);
			if (current($perfiles) != '')  {
				return $perfiles;
			}
		}
	}
	
	/**
	 * Retorna la URL base del proyecto editado, basandose en la URL del PA (puede no ser la real..)
	 * @return unknown
	 */
	static function get_url_previsualizacion()
	{
		$pa = '';
		if (isset(self::$memoria['previsualizacion']['punto_acceso'])) {
			$pa = self::$memoria['previsualizacion']['punto_acceso'];
		}
		if (strpos($pa, '.php') !== false) {
			return dirname($pa);
		} else {
			return $pa;	
		}
	}
	

	/**
	*	Recuperar las propiedades y setearlas en la sesion
	*/
	static function get_parametros_previsualizacion($refrescar = false)
	{
		if ($refrescar || !isset(self::$memoria['previsualizacion'])) {
			$rs = self::get_parametros_previsualizacion_db();
			if ($rs) {
				self::$memoria['previsualizacion'] = $rs;
			} else {
				 self::$memoria['previsualizacion'] = null;
			}
		}
		return 	self::$memoria['previsualizacion'];
	}
	
	/**
	*	Establecer las propiedades desde el editor
	*/
	static function set_parametros_previsualizacion($datos)
	{
		if (!( array_key_exists('punto_acceso', $datos) && array_key_exists('grupo_acceso', $datos))) {
			throw new toba_error('Los parametros de previsualizacion son incorrectos.');	
		}
		self::$memoria['previsualizacion']['punto_acceso'] = $datos['punto_acceso'];
		self::$memoria['previsualizacion']['grupo_acceso'] = $datos['grupo_acceso'];
		self::$memoria['previsualizacion']['perfil_datos'] = $datos['perfil_datos'];
		if (self::get_id_instancia_activa() == toba::instancia()->get_id() ) {
			//Si estoy editando un proyecto en otra instancia, no tengo certeza de como guardar estos datos.
			self::set_parametros_previsualizacion_db($datos);
		}
	}

	static function get_parametros_previsualizacion_db()
	{
		$proyecto =  quote(self::get_proyecto_cargado()) ;
		$usuario = quote(toba::usuario()->get_id());
		$sql = "SELECT perfil_datos, grupo_acceso, punto_acceso
				FROM apex_admin_param_previsualizazion
				WHERE proyecto = $proyecto
				AND usuario = $usuario;";
		//Esto se accede solo desde el ADMIN
		$datos = toba::db()->consultar($sql);
		if ($datos) {
			return $datos[0];	
		}
		return null;
	}
	
	static function set_parametros_previsualizacion_db($datos)
	{
		$rs = self::get_parametros_previsualizacion_db();
		$datos = quote($datos);
		$proyecto = quote(self::get_proyecto_cargado());
		$usuario = quote(toba::usuario()->get_id());
		if (!$rs) {
			$sql = "INSERT INTO apex_admin_param_previsualizazion (perfil_datos, grupo_acceso, punto_acceso, proyecto, usuario) 
					VALUES ({$datos['perfil_datos']}, {$datos['grupo_acceso']}, {$datos['punto_acceso']}, $proyecto, $usuario);";
		} else {
			$sql = "UPDATE apex_admin_param_previsualizazion
					SET grupo_acceso = {$datos['grupo_acceso']}, 
						perfil_datos = {$datos['perfil_datos']}, 
						punto_acceso = {$datos['punto_acceso']}
					WHERE proyecto = $proyecto
					AND usuario = $usuario;";
		}
		//Esto se accede solo desde el ADMIN
		toba::db()->ejecutar($sql);
	}

	/**
	 *
	 * @return toba_modelo_proyecto
	 */
	static function get_modelo_proyecto()
	{
		return toba_modelo_catalogo::instanciacion()->get_proyecto(self::get_id_instancia_activa(), self::get_proyecto_cargado());
	}
	
	//---------------------------------------------------------------------------
	//-- Generacion de VINCULOS al editor (desde un proyecto PREVISUALIZADO)
	//---------------------------------------------------------------------------

	/*
	*	Zona de vinculos de los items
	*/
	static function generar_zona_vinculos_item( $item, $accion, $enviar_div_wrapper = true )
	{
		if (! self::acceso_recursivo()) {
			toba::solicitud()->set_cronometrar(true);
		}
		toba_js::cargar_consumos_globales(array('utilidades/toba_editor'));
		$html_ayuda_editor = toba_recurso::ayuda(null, 'Presionando la tecla CTRL se pueden ver los enlaces hacia los editores de los distintos componentes de esta página');
		$html_ayuda_cronometro = toba_recurso::ayuda(null, 'Ver los tiempos de ejecución en la generación de esta página');
		$html_ayuda_ajax = toba_recurso::ayuda(null, 'Activar/Desactivar navegación interna de la operación via AJAX');
		$html_ayuda_editor = toba_recurso::ayuda(null, 'Volver al editor de toba');
		$solicitud = toba::solicitud()->get_id();
		$link_cronometro = toba::vinculador()->get_url('toba_editor', '1000263', null, array('prefijo'=>toba_editor::get_punto_acceso_editor()));
		$link_analizador_sql = toba::vinculador()->get_url('toba_editor', '30000030', null, array('prefijo'=>toba_editor::get_punto_acceso_editor()));
		$link_logger = toba::vinculador()->get_url('toba_editor', '1000003', null, array('prefijo'=>toba_editor::get_punto_acceso_editor()));
		$link_archivos = toba::vinculador()->get_url('toba_editor', '30000029', null, array('prefijo'=>toba_editor::get_punto_acceso_editor()));
		$estilo = toba::proyecto()->get_parametro('estilo');		
		if ($enviar_div_wrapper) {
			echo "<div id='editor_previsualizacion'>";
		/*echo "<div id='editor_previsualizacion_colap'><img style='cursor:pointer;_cursor:hand;' title='Ocultar la barra'
				src='".toba_recurso::imagen_toba('nucleo/expandir_izq.gif', false)."'
				onclick='toggle_nodo(\$\$(\"editor_previsualizacion_cont\"))'/></div>";*/
		}
		echo "<span id='editor_previsualizacion_cont'>";
		echo "<span id='editor_previsualizacion_vis'>";

		//Logger
		list($log_nivel, $log_cant) = toba::logger()->get_mensajes_minimo_nivel();
		$niveles = toba::logger()->get_niveles();
		$niveles[0] = 'INFO';
		$img = self::imagen_editor('logger/'.strtolower($niveles[$log_nivel]).'.gif', true);
		$html_ayuda_logger = toba_recurso::ayuda(null, 'Visor de logs');		
		echo "<a href='$link_logger' target='logger' $html_ayuda_logger >".$img." $log_cant</a>\n";

		//Cronometro
		toba::cronometro()->marcar('Resumen toba_editor');
		echo "<a href='$link_cronometro' target='logger' $html_ayuda_cronometro >\n".
				toba_recurso::imagen_toba('clock.png', true).
				' '.round(toba::cronometro()->tiempo_acumulado(), 2). ' seg'."</a> ";
				
		//Memoria
		if (function_exists('memory_get_peak_usage')) {
			$memoria_pico = memory_get_peak_usage();
			echo toba_recurso::imagen_toba('memory.png', true, 16, 16, 'Pico máximo de memoria que ha consumido el script actual');
			echo ' '.file_size($memoria_pico, 0).' ';
		}
		
		//Base de datos
		$fuente = toba_admin_fuentes::instancia()->get_fuente_predeterminada(false, toba_editor::get_proyecto_cargado());
		if ($fuente) {
			try {
				$base = toba_admin_fuentes::instancia()->get_fuente($fuente, toba_editor::get_proyecto_cargado())->get_db();
				$info_db = $base->get_info_debug();
				$total = 0;
				foreach($info_db as $info) {
					if (isset($info['fin'])) {
						$total += ($info['fin'] - $info['inicio']);
					}
				}
				$rol = toba::db()->get_rol_actual();
				toba::memoria()->set_dato_instancia('previsualizacion_consultas', array('fuente' => $fuente, 'datos' => $info_db));
				echo "<a href='$link_analizador_sql' target='logger'>".toba_recurso::imagen_toba('objetos/datos_relacion.gif', true, 16, 16, 'Ver detalles de las consultas y comandos ejecutados en este pedido de página').
					count($info_db). " ($rol)</a>";
					
			} catch (toba_error $e) {
				//Si no se tiene acceso a la base no se hace nada
			}
		}
		
		//Archivos
		$archivos = self::get_archivos_incluidos();		
		$total = 0;
		foreach ($archivos as $arch) {
			$total += filesize($arch);
		}
		toba::memoria()->set_dato_instancia('previsualizacion_archivos', $archivos);
		echo "<a href='$link_archivos' target='logger'>".toba_recurso::imagen_toba('nucleo/php.gif', true, 16, 16, 'Ver detalle de archivos .php del proyecto incluidos en este pedido de página').
			' '.count($archivos)." arch. (".file_size($total,0).')</a>';
				
		//Session		
		$tamano = file_size(strlen(serialize($_SESSION)), 0);
		echo toba_recurso::imagen_toba('sesion.png', true, 16, 16, 'Tamaño de la sesión')." $tamano  ";
		echo "</span>";		
		
		//-- ACCIONES
		echo "<span id='editor_previsualizacion_acc'>";
		$perfiles = array(apex_ef_no_seteado => '-- Todos --');
		foreach (toba::manejador_sesiones()->get_perfiles_funcionales() as $perfil) {
			$perfiles[$perfil] = $perfil;
		}
		$actuales = toba::manejador_sesiones()->get_perfiles_funcionales_activos();
		$actual = null;
		if (count($actuales) == 1) {
			$actual = current($actuales);
		}
		$js = "title='Cambia el perfil actual del usuario' onchange=\"location.href = toba_prefijo_vinculo + '&perfil_activo=' + this.value\"";
		echo "Perfiles: ".toba_form::select('cambiar_perfiles', $actual, $perfiles, 'ef-combo', $js);		

		//Usuario de la base
		$hay_limitado = false;
		if (! isset(self::$fuentes)) {
			self::$fuentes = toba_info_editores::get_fuentes_datos(toba_editor::get_proyecto_cargado());	
		}
		foreach (self::$fuentes as $fuente) {
			if ($fuente['permisos_por_tabla']) {
				$hay_limitado = true;
			}
		}		
		
		if ($hay_limitado) {
			$actual = self::$memoria['conexion_limitada'] ? 'limitado' : 'normal';
			$datos = array("normal" => "Normal", "limitado" => "Limitados");
			$js = "title='Cambia temporalmente el usuario de conexión a la base' onchange=\"location.href = toba_prefijo_vinculo + '&usuario_conexion=' + this.value\"";
			echo "Permisos DB: ".toba_form::select('cambiar_rol', $actual, $datos, 'ef-combo', $js);
		}
		
		
		//Skin
		$skins = rs_convertir_asociativo(toba_info_editores::get_lista_skins(), array('estilo','proyecto'), 'descripcion');
		$js = "title='Cambia temporalmente el skin de la aplicación' onchange=\"location.href = toba_prefijo_vinculo + '&skin=' + this.value\"";
		$defecto = toba::proyecto()->get_parametro('estilo').apex_qs_separador.toba::proyecto()->get_parametro('estilo_proyecto');
		echo "Skin: ".toba_form::select('cambiar_skin', $defecto, $skins, 'ef-combo', $js);
		
		//AJAX		
		echo "<a id='editor_ajax' href='javascript: editor_cambiar_ajax()' $html_ayuda_ajax>".toba_recurso::imagen_toba('objetos/ajax_off.png', true)."</a>\n";
				
		//Edicion
		echo	"<a href='javascript: editor_cambiar_vinculos()' $html_ayuda_editor >".
		toba_recurso::imagen_toba('edicion_chico.png', true)."</a>\n";

		//Arbol
		$vinculos = self::get_vinculos_item($item, $accion);
		if (isset($vinculos[1])) {
			self::mostrar_vinculo($vinculos[1]);
		}
		
		//Editor
		echo "<a href='#' onclick='return toba_invocar_editor()' $html_ayuda_editor>".toba_recurso::imagen_toba('icono_16.png', true)."</a>\n";
		echo "</span>";
		echo "</span>";
		
			
		echo "<div class='div-editor' style='position:fixed; top: 85px; left: 1px;'>";
		foreach(self::get_vinculos_item($item, $accion) as $vinculo) {
			self::mostrar_vinculo($vinculo);
		}
		echo "</div>";
		if ($enviar_div_wrapper) {
			echo "</div>";

		}
	}
	
	static protected function get_archivos_incluidos()
	{
		$todos = get_included_files();
		$path_relativo = toba_manejador_archivos::path_a_unix(toba::proyecto()->get_path());
		$archivos = array();
		foreach ($todos as $archivo) {
			$archivo = toba_manejador_archivos::path_a_unix($archivo);
			if (strpos($archivo, $path_relativo) !== false) {
				$archivos[] = $archivo;
			}
		}
		return $archivos;
	}

	static protected function mostrar_vinculo($vinculo)
	{
		if (! isset($vinculo['js'])) {
			echo "<a href='#' title='{$vinculo['tip']}' onclick=\"return toba_invocar_editor('{$vinculo['frame']}','{$vinculo['url']}')\">";
		} else {
			echo "<a href='#' title='{$vinculo['tip']}' onclick=\"return {$vinculo['js']}\">";
		}
		if (isset($vinculo['imagen_origen']) && $vinculo['imagen_origen'] == 'proyecto') {
			echo self::imagen_editor($vinculo['imagen'],true);
		} else {
			echo toba_recurso::imagen_toba($vinculo['imagen'],true);
		}
		echo "</a>\n";
	}

	/*
	*	Acceso a la edicion del componente
	*/
	static function generar_zona_vinculos_componente( $componente, $editor, $clase, $con_subclase )
	{
		$salida = "<span class='ei-base' style='height: 55px' >";
		if ($con_subclase) {
			$salida .= self::get_utileria_editor_abrir_php(array('componente'=>$componente[1], 'proyecto' => $componente[0]));
		}
		foreach(self::get_vinculos_componente($componente, $editor, $clase) as $vinculo) {
			$salida .= "<a href='#' onclick=\"return toba_invocar_editor('{$vinculo['frame']}','{$vinculo['url']}')\">";
			if ($vinculo['imagen_recurso_origen'] == 'apex') {
				$salida .= toba_recurso::imagen_toba($vinculo['imagen'],true,null,null,$vinculo['etiqueta']);
			} else {
				$salida .= self::imagen_editor($vinculo['imagen'],true,null,null,$vinculo['etiqueta']);
			}
			$salida .= "</a>\n";
		}
		$salida .= "</span>";
		return $salida;
	}

	/*
	*	Vinculos a EFs y a COLUMNAS
	*/
	static function get_vinculo_subcomponente($item_editor, $parametros, $opciones=array(),$frame='frame_centro')
	{
		$imagen='objetos/editar.gif';
		if(!isset($opciones['celda_memoria'])) $opciones['celda_memoria'] = 'central';
		if(!isset($opciones['prefijo'])) $opciones['prefijo'] = self::get_punto_acceso_editor();
		if(!isset($opciones['validar'])) $opciones['validar'] = false;
		if(!isset($opciones['menu'])) $opciones['menu'] = true;
		$url = toba::vinculador()->get_url(self::get_id(),$item_editor,$parametros,$opciones);
		$html = "<a href='#' title='Editar' class='div-editor' onclick=\"return toba_invocar_editor('$frame','$url')\">";
		$html .= toba_recurso::imagen_toba($imagen,true);
		$html .= '</a>';
		return $html;
	}

	static function get_vinculos_item( $item, $accion )
	{
		//Celda de memoria central
		//punto de acceso del admin

		$proyecto = self::get_proyecto_cargado();
		$vinculos = array();
		
		//Accion
		if ($accion != '') {
			$parametros[apex_hilo_qs_zona] = $proyecto . apex_qs_separador . $item;
			$opciones = array('servicio' => 'ejecutar', 'zona' => false, 'celda_memoria' => 'ajax', 'menu' => true);
			$vinculo = toba::vinculador()->get_url(toba_editor::get_id(), "30000014", $parametros, $opciones);
			$js = "toba.comunicar_vinculo('$vinculo')";
			$vinculo = array();			
			$vinculo['js'] = $js;
			$vinculo['frame'] = '';
			$vinculo['imagen'] = 'reflexion/abrir.gif';
			$vinculo['imagen_origen'] = 'proyecto';
			$vinculo['tip'] = 'Abrir el PHP del ítem en el escritorio';
			$vinculos[] = $vinculo;
		}

		//Etitor Item
		$opciones = array();
		$opciones['celda_memoria'] = 'central';
		$opciones['prefijo'] = self::get_punto_acceso_editor();
		$opciones['validar'] = false;
		$parametros = array(apex_hilo_qs_zona=> $proyecto . apex_qs_separador . $item);
		$vinculo = array();
		$vinculo['url'] = toba::vinculador()->get_url(self::get_id(),'1000240',$parametros,$opciones);
		$vinculo['frame'] = 'frame_centro';
		$vinculo['imagen'] = 'objetos/editar.gif';
		$vinculo['tip'] = 'Ir al editor de la operación.';
		$vinculos[] = $vinculo;

		//Catalogo Unificado
		$parametros = array("proyecto"=>$proyecto,"item"=>$item);
		$opciones = array();
		$opciones['celda_memoria'] = 'lateral';
		$opciones['prefijo'] = self::get_punto_acceso_editor();
		$vinculo = array();		
		$vinculo['url'] = toba::vinculador()->get_url(self::get_id(),'1000239',$parametros,$opciones);
		$vinculo['frame'] = 'frame_lista';
		$vinculo['imagen'] = 'objetos/arbol.gif';
		$vinculo['tip'] = 'Ver composicion de la operación en el editor.';
		$vinculos[] = $vinculo;

/*		//Consola JS
		//-- Link a la consola JS
		$vinculos[2]['url'] = toba::vinculador()->get_url(self::get_id(),'/admin/objetos/consola_js');
		$vinculos[2]['frame'] = 'frame_lista';
		$vinculos[2]['imagen'] = 'solic_consola.gif';
		$vinculos[2]['tip'] = 'Ir al editor de la operación.';
*/
		return $vinculos;
	}

	static function get_vinculos_componente($componente,$editor,$clase) 
	{
		$vinculos = array();
		$opciones['celda_memoria'] = 'central';
		$opciones['menu'] = true;
		$opciones['prefijo'] = self::get_punto_acceso_editor();
		$opciones['validar'] = false;

		$vinculos = call_user_func(array('toba_datos_editores', 'get_pantallas_'.$clase));		
		foreach(array_keys($vinculos) as $id) {
			$parametros = array(apex_hilo_qs_zona => implode(apex_qs_separador,$componente),
								'etapa' => $vinculos[$id]['identificador']);
			$vinculos[$id]['url'] = toba::vinculador()->get_url(self::get_id(),$editor,$parametros,$opciones);
			$vinculos[$id]['frame'] = 'frame_centro';
		}
		return $vinculos;
	}

	static function get_vinculo_evento($componente, $editor, $evento)
	{
		$opciones['celda_memoria'] = 'central';
		$opciones['prefijo'] = self::get_punto_acceso_editor();
		$opciones['validar'] = false;
		$parametros = array(apex_hilo_qs_zona=>implode(apex_qs_separador,$componente), 'evento' => $evento);
		$url = toba::vinculador()->get_url(self::get_id(),$editor,$parametros,$opciones);
		$salida = "<span class='div-editor'>";		
		$salida .= "<a href='#' title='Editar propiedades del evento' onclick=\"return toba_invocar_editor('frame_centro', '$url')\">";
		$salida .= toba_recurso::imagen_toba('objetos/editar.gif',true);
		$salida .= "</a>\n";
		$salida .= "</span>";		
		return $salida;
	}
	
	static function get_vinculo_pantalla($componente, $editor, $pantalla)
	{
		$opciones['celda_memoria'] = 'central';
		$opciones['prefijo'] = self::get_punto_acceso_editor();
		$opciones['validar'] = false;
		$parametros = array(apex_hilo_qs_zona=>implode(apex_qs_separador,$componente), 'pantalla' => $pantalla);
		$url = toba::vinculador()->get_url(self::get_id(),$editor,$parametros,$opciones);
		$salida = "<span class='div-editor' style='position:absolute'>";		
		$salida .= "<a href='#' title='Editar propiedades de la pantalla' onclick=\"return toba_invocar_editor('frame_centro', '$url')\">";
		$salida .= toba_recurso::imagen_toba('objetos/editar.gif',true);
		$salida .= "</a>\n";
		$salida .= "</span>";		
		return $salida;		
	}
	
	static function get_utileria_editor_abrir_php($id_componente, $icono='reflexion/abrir.gif')
	{
		$parametros[apex_hilo_qs_zona] = $id_componente['proyecto'] . apex_qs_separador . $id_componente['componente'];
		$opciones = array('servicio' => 'ejecutar', 'zona' => false, 'celda_memoria' => 'ajax', 'menu' => true);
		$vinculo = toba::vinculador()->get_url(toba_editor::get_id(), "3463", $parametros, $opciones);
		$js = "toba.comunicar_vinculo('$vinculo')";
		$ayuda = 'Abre la extensión PHP del componente en el editor del escritorio';
		return "<a href='#' title='$ayuda' onclick=\"$js\">".self::imagen_editor($icono, true)."</a>";
	}	
	
	static function imagen_editor($imagen,$html=false,$ancho=null, $alto=null,$tooltip=null,$mapa=null)
	{
		$src = toba_recurso::url_proyecto(self::get_id()) . "/img/" . $imagen;
		if ($html){
			return toba_recurso::imagen($src, $ancho, $alto, $tooltip, $mapa);
		}else{
			return $src;
		}
	}
	
	//--------------------------------------------------------------------------
	// Abrir una fuente de datos del proyecto editado
	//--------------------------------------------------------------------------
	
	static function db_proyecto_cargado($id_fuente)
	{
		$fuente_datos = toba_admin_fuentes::instancia()->get_fuente( $id_fuente,
																	 toba_editor::get_proyecto_cargado() );
		return $fuente_datos->get_db();		
	}
	
}
?>