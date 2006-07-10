<?
/*
	A travez de esta clase el nucleo registra al admin
*/
class editor
{
	static function get_id()
	{
		return 'admin';	
	}

	/**
	*	_falta: Hacer un control de que el administrador esta en esa instancia
	*			(hoy en dia seria obligatorio)
	*/
	static function iniciar($instancia, $proyecto)
	{
		$_SESSION['toba']['_editor_']['instancia'] = $instancia;
		$_SESSION['toba']['_editor_']['proyecto'] = $proyecto;
		//Averiguo el punto de acceso del editor
		$punto_acceso = explode('?', $_SERVER['PHP_SELF']);	
		$_SESSION['toba']['_editor_']['punto_acceso'] = $punto_acceso[0];
	}
	
	/**
	*	Indica si el EDITOR de metadatos se encuentra encendido
	*/
	static function activado()
	{
		if (isset($_SESSION['toba']['_editor_'])) {
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
		if (self::activado() && toba::get_sesion()->activa()) {
			return $_SESSION['toba']['_editor_']['proyecto'] == info_proyecto::get_id();
		}
		return false;
	}

	static function get_instancia_activa()
	{
		if (self::activado()) {
			return $_SESSION['toba']['_editor_']['instancia'];
		}
	}

	static function get_proyecto_cargado()
	{
		if (self::activado()) {
			return $_SESSION['toba']['_editor_']['proyecto'];
		}
	}
	
	static function set_proyecto_cargado($proyecto)
	{
		$_SESSION['toba']['_editor_']['proyecto'] = $proyecto;
		self::get_parametros_previsualizacion(true);
	}
		
	static function get_punto_acceso_editor()
	{
		if (self::activado()) {
			return $_SESSION['toba']['_editor_']['punto_acceso'];
		}
	}

	/**
	*	Indica si el ADMIN se esta editando a si mismo
	*/
	static function acceso_recursivo()
	{
		if (self::activado()) {
			return $_SESSION['toba']['_editor_']['proyecto'] == self::get_id();
		}
		return false;		
	}

	static function borrar_memoria()
	{
		unset($_SESSION['toba']['_editor_']);
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

	static function get_grupo_acceso_previsualizacion()
	{
		$param_prev = self::get_parametros_previsualizacion();
		if(isset($param_prev['grupo_acceso'])) {
			return $param_prev['grupo_acceso'];
		} else {
			throw new excepcion_toba("No esta definido el parametro 'grupo de acceso' del editor.");	
		}
	}

	/**
	*	Recuperar las propiedades y setearlas en la sesion
	*/
	static function get_parametros_previsualizacion($refrescar = false)
	{
		if ($refrescar || !isset($_SESSION['toba']['_editor_']['previsualizacion'])) {
			$rs = self::get_parametros_previsualizacion_db();
			if ($rs) {
				$_SESSION['toba']['_editor_']['previsualizacion'] = $rs;
			} else {
				$_SESSION['toba']['_editor_']['previsualizacion']['punto_acceso'] = null;
				$_SESSION['toba']['_editor_']['previsualizacion']['grupo_acceso'] = null;
			}		
		}
		return 	$_SESSION['toba']['_editor_']['previsualizacion'];
	}
	
	/**
	*	Establecer las propiedades desde el editor
	*/
	static function set_parametros_previsualizacion($datos)
	{
		if (!( array_key_exists('punto_acceso', $datos) && array_key_exists('grupo_acceso', $datos))) {
			throw new excepcion_toba('Los parametros de previsualizacion son incorrectos.');	
		}
		$_SESSION['toba']['_editor_']['previsualizacion']['punto_acceso'] = $datos['punto_acceso'];
		$_SESSION['toba']['_editor_']['previsualizacion']['grupo_acceso'] = $datos['grupo_acceso'];
		self::set_parametros_previsualizacion_db($datos);
	}

	static function get_parametros_previsualizacion_db()
	{
		$sql = "SELECT grupo_acceso, punto_acceso 
				FROM apex_admin_param_previsualizazion
				WHERE proyecto = '" . self::get_proyecto_cargado() . "'
				AND usuario = '".toba::get_usuario()->get_id()."';";
		$datos = toba::get_db('instancia')->consultar($sql);
		if ($datos) {
			return $datos[0];	
		}
		return null;
	}
	
	static function set_parametros_previsualizacion_db($datos)
	{
		$rs = self::get_parametros_previsualizacion_db();
		if (!$rs) {
			$sql = "INSERT INTO apex_admin_param_previsualizazion (grupo_acceso, punto_acceso, proyecto, usuario) 
					VALUES ('{$datos['grupo_acceso']}', '{$datos['punto_acceso']}', 
							'" . self::get_proyecto_cargado() . "', '".toba::get_usuario()->get_id()."');";
		} else {
			$sql = "UPDATE apex_admin_param_previsualizazion
					SET grupo_acceso = '{$datos['grupo_acceso']}', 
						punto_acceso = '{$datos['punto_acceso']}'
					WHERE proyecto = '" . self::get_proyecto_cargado() . "'
					AND usuario = '".toba::get_usuario()->get_id()."';";
		}
		toba::get_db('instancia')->ejecutar($sql);
	}
	
	//---------------------------------------------------------------------------
	//-- Generacion de VINCULOS al editor (desde un proyecto PREVISUALIZADO)
	//---------------------------------------------------------------------------

	/**
	*	Generacion del invocador al editor.
	*/
	static function javascript_invocacion_editor()
	{
		echo js::abrir();
		echo "	function toba_invocar_editor(frame, url) {\n";
		if ( editor::acceso_recursivo() ) {
			// La previsualizacion es dentro del mismo entorno
			echo "	top.frame_control.editor.abrir_editor(frame,url);\n";
		} else {
			// La previsualizacion es en un popup
			echo "	if (window.opener) {
					window.opener.top.frame_control.editor.abrir_editor(frame,url);
					window.opener.focus();
				} else {
					// Si cerraron el editor, esta ventana a no tiene sentido abierta.
					window.close();	
				}\n";
		}
		echo "}";
		echo js::cerrar();		
	}

	/*
	*	Zona de vinculos de los items
	*/
	static function generar_zona_vinculos_item( $item )
	{
		self::javascript_invocacion_editor();
		echo "<div class='div-editor'>";
		foreach(self::get_vinculos_item($item) as $vinculo) {
			echo "<a href='#' onclick=\"toba_invocar_editor('{$vinculo['frame']}','{$vinculo['url']}')\">";
			echo recurso::imagen_apl($vinculo['imagen'],true);//,null,null,$vinculo['tip']);
			echo "</a>\n";
		}
		echo "</div>";
	}

	/*
	*	Zona de vinculos de los componentes
	*/
	static function generar_zona_vinculos_componente( $componente, $editor )
	{
		echo "<span class='div-editor'>";		
		foreach(self::get_vinculos_componente($componente, $editor) as $vinculo) {
			echo "<a href='#' onclick=\"toba_invocar_editor('{$vinculo['frame']}','{$vinculo['url']}')\">";
			echo recurso::imagen_apl($vinculo['imagen'],true);//,null,null,$vinculo['tip']);
			echo "</a>\n";
		}
		echo "</span>";
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
		$url = toba::get_vinculador()->crear_vinculo(self::get_id(),$item_editor,$parametros,$opciones);
		$html = "<a href='#' class='div-editor' onclick=\"toba_invocar_editor('$frame','$url')\">";
		$html .= recurso::imagen_apl($imagen,true);//,null,null,$vinculo['tip']);
		$html .= '</a>';
		return $html;
	}

	/*
		ATENCION __falta: 
			- Logger
			- Editor de CSS
			- Cronometro
	*/
	static function get_vinculos_item( $item )
	{
		//Celda de memoria central
		//punto de acceso del admin

		$proyecto = self::get_proyecto_cargado();
		$vinculos = array();
		//Etitor Item
		$opciones['celda_memoria'] = 'central';
		$opciones['prefijo'] = self::get_punto_acceso_editor();
		$opciones['validar'] = false;
		$parametros = array(apex_hilo_qs_zona=> $proyecto . apex_qs_separador . $item);
		$vinculos[0]['url'] = toba::get_vinculador()->crear_vinculo(self::get_id(),'/admin/items/editor_items',$parametros,$opciones);
		$vinculos[0]['frame'] = 'frame_centro';
		$vinculos[0]['imagen'] = 'objetos/editar.gif';
		$vinculos[0]['tip'] = 'Ir al editor del item.';

		//Catalogo Unificado
		$parametros = array("proyecto"=>$proyecto,"item"=>$item);
		$opciones['celda_memoria'] = 'lateral';
		$vinculos[1]['url'] = toba::get_vinculador()->crear_vinculo(self::get_id(),'/admin/items/catalogo_unificado',$parametros,$opciones);
		$vinculos[1]['frame'] = 'frame_lista';
		$vinculos[1]['imagen'] = 'objetos/arbol.gif';
		$vinculos[1]['tip'] = 'Ver composicion del ITEM.';

/*		//Consola JS
		//-- Link a la consola JS
		$vinculos[2]['url'] = toba::get_vinculador()->crear_vinculo(self::get_id(),'/admin/objetos/consola_js');
		$vinculos[2]['frame'] = 'frame_lista';
		$vinculos[2]['imagen'] = 'solic_consola.gif';
		$vinculos[2]['tip'] = 'Ir al editor del item.';
*/
		return $vinculos;
	}

	static function get_vinculos_componente($componente,$editor) 
	{
		$vinculos = array();
		$opciones['celda_memoria'] = 'central';
		$opciones['prefijo'] = self::get_punto_acceso_editor();
		$opciones['validar'] = false;
		
		//Vinculo al EDITOR del OBJETO
		$parametros = array(apex_hilo_qs_zona=>implode(apex_qs_separador,$componente));
		$vinculos[0]['url'] = toba::get_vinculador()->crear_vinculo(self::get_id(),$editor,$parametros,$opciones);
		$vinculos[0]['frame'] = 'frame_centro';
		$vinculos[0]['imagen'] = 'objetos/editar.gif';
		$vinculos[0]['tip'] = 'Ir al editor del componente.';
		return $vinculos;
	}		
}