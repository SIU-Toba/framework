<?php

require_once('ef_popup_utileria_php.php');

/**
*	Utilidades varias para el administrador Toba
*/
define('toba_abrir_archivo_ajax', 'toba_abrir_archivo_ajax');

class admin_util
{
	
	static function get_js_editor()
	{
		return "
			toba_editor = new function() {
			};

			toba_editor.medida_css_correcta = function(texto) {
				if (texto != '' ) {
					return /\d+\s*(in|cm|mm|pt|pc|px|em|ex|%)\s*$/i.test(texto);
				}
				return true;
			};

			toba_editor.mensaje_error_medida_css = function() {
				return \"Debe contener una medida CSS por ejemplo '300px' '50%' '20em' ,etc.\";
			}
		";
	}
	
	/**
	*	Refresca el frame izquierdo del editor
	*/
	static function refrescar_editor_item($ir_a_item=null)
	{
		echo toba_js::abrir();
		$frame = 'parent.'.apex_frame_lista;
		echo "if ($frame.js_arbol_1368_items) {\n";
		if (isset($ir_a_item)) {
			echo "$frame.js_arbol_1368_items.ver_propiedades('". toba::escaper()->escapeJs($ir_a_item)."');\n";
		} else {
			echo "$frame.js_arbol_1368_items.set_evento(new $frame.evento_ei('refrescar', true, '' ));\n";
		}
		echo "	}\n";
		echo toba_js::cerrar();		
	}
	
	/**
	*	Refresca el frame izquierdo del editor
	*/
	static function refrescar_barra_lateral()
	{
		echo toba_js::abrir();
		echo 'parent.'.apex_frame_lista.'.location.reload()';
		echo toba_js::cerrar();		
	}
	
	static function redirecionar_a_editor_item($proyecto, $item)
	{
		$clave = array( 'proyecto' => $proyecto, 'componente' => $item );		
		$elem_item = toba_constructor::get_info($clave, 'toba_item');
		$vinculo = $elem_item->vinculo_editor();
		echo toba_js::abrir();
		echo "window.location.href='". toba::escaper()->escapeJs($vinculo)."'\n";
		echo toba_js::cerrar();
	}
	
	static function redireccionar_a_editor_objeto($proyecto, $objeto)
	{
		$clave = array( 'componente'=>$objeto, 'proyecto'=>$proyecto );
		$vinculo = toba_constructor::get_info($clave)->vinculo_editor();
		admin_util::refrescar_editor_item();
		echo toba_js::abrir();
		echo "window.location.href='". toba::escaper()->escapeJs($vinculo)."'\n";
		echo toba_js::cerrar();		
	}
	
	static function get_icono_abrir_php($archivo)
	{
		$parametros = array('archivo' => $archivo);
		$opciones = array('servicio' => 'ejecutar', 'celda_memoria' => 'ajax', 'validar' => false, 'menu' => true );
		$vinculo = toba::vinculador()->get_url(toba_editor::get_id(), 3463, $parametros, $opciones);
		$js = "toba.comunicar_vinculo('".  toba::escaper()->escapeJs($vinculo)."')";
		$ayuda = toba_recurso::ayuda(null, 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
						   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]');
		return "<img style='cursor:pointer' onclick=\"$js\" src='".toba_recurso::imagen_proyecto('reflexion/abrir.gif', false)."' $ayuda>";		
	}

	static function existe_archivo_subclase($path_relativo, $pm_id=null)
	{
		//ei_arbol(debug_backtrace());
		$path_real = self::get_path_archivo($path_relativo, $pm_id);
		return (file_exists($path_real) && is_file($path_real));
	}
	
	static function get_path_archivo($path_relativo, $pm_id=null)
	{		
		if (! is_null($pm_id)) {
			$pm = toba_modelo_pms::get_pm($pm_id, toba_editor::get_proyecto_cargado());
			$path = $pm->get_path_absoluto().'/';
		} else {
			$path = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . '/php/';
		}
		return $path . $path_relativo;
	}
	
	/**
	 * Rutea el pedido de uan imagen como si se estubiera ejecutando el proyecto
	 */
	static function url_imagen_de_origen($img, $origen)
	{
		switch ($origen) {
			case 'apex':
				return toba_recurso::imagen_toba($img);
				break;
				
			case 'skin':	
				$estilo = toba::proyecto(toba_editor::get_proyecto_cargado())->get_parametro('estilo');
				$proyecto = toba::proyecto(toba_editor::get_proyecto_cargado())->get_parametro('estilo_proyecto');
				return toba_recurso::url_skin($estilo, $proyecto).'/'.$img;
				break;
				
			case 'proyecto':
				return toba_recurso::url_proyecto(toba_editor::get_proyecto_cargado()).'/img/'.$img;
				break;
				
			default: throw new toba_error("No esta contemplado el origen $origen");				
		}
	}
	
	/**
	 * Rutea el pedido del path de una  imagen como si se estubiera ejecutando el proyecto
	 */
	static function dir_imagen_de_origen($img, $origen)
	{
		switch ($origen) {
			case 'apex':
				return toba::instalacion()->get_path().'/www/img';	
				break;
				
			case 'skin':	
				$estilo = toba::proyecto(toba_editor::get_proyecto_cargado())->get_parametro('estilo');
				$proyecto = toba::proyecto(toba_editor::get_proyecto_cargado())->get_parametro('estilo_proyecto');
				return toba::instancia()->get_path_proyecto($proyecto)."/www/skins/$estilo";
				break;
				
			case 'proyecto':
				$cargado = toba_editor::get_proyecto_cargado();
				return toba::instancia()->get_path_proyecto($cargado).'/www/img';				
				break;
				
			default: throw new toba_error("No esta contemplado el origen $origen");
		}
	}	

	//--------------------------------------------------------------------------------------
	//--- Funcionalidad transversal para ZONAs que requieran apertura de archivos
	//--------------------------------------------------------------------------------------

	static function get_acceso_abrir_php($componente, $item_visualizador=30000014, $parametros=array())
	{
		$id = array('proyecto'=>$componente[0], 'componente' =>$componente[1]);
		$utileria = self::get_utileria_editor_abrir_php($item_visualizador, $id, 'reflexion/abrir.gif', $parametros);
		return '<a href="' . $utileria['vinculo'] .'"'. " title='".$utileria['ayuda']. "'>" .
				toba_recurso::imagen($utileria['imagen'], null, null, $utileria['ayuda']).
				"</a>\n";
	}
	
	static function get_acceso_ver_php($componente, $item_visualizador=30000014, $frame=apex_frame_centro, $parametros=array())
	{
		$id = array('proyecto'=>$componente[0],'componente' =>$componente[1]) ;
		$utileria = admin_util::get_utileria_editor_ver_php($item_visualizador, $id, 'nucleo/php.gif', $parametros);
		return "<a href='" . $utileria['vinculo'] ."' target='".$frame."' title='".$utileria['ayuda']."'>" .
				toba_recurso::imagen($utileria['imagen'], null, null, $utileria['ayuda']).
				"</a>\n";
	}

	static function get_utileria_editor_abrir_php($item_visualizador, $id_componente, $icono='reflexion/abrir.gif', $parametros=array())
	{
		$param_local = array(apex_hilo_qs_zona => $id_componente['proyecto'] . apex_qs_separador . $id_componente['componente']);
		$parametros = array_merge($param_local, $parametros);
		$opciones = array('servicio' => 'ejecutar', 'zona' => false, 'celda_memoria' => 'ajax', 'menu' => true);
		$vinculo = toba::vinculador()->get_url(toba_editor::get_id(), $item_visualizador, $parametros, $opciones);
		$js = "toba.comunicar_vinculo('$vinculo')";
		return array(
			'imagen' => toba_recurso::imagen_proyecto($icono, false),
			'ayuda' => 'Abrir el archivo PHP en el editor del escritorio.' .
					   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]',
			'vinculo' => "javascript: $js;",
			'js' => $js,
			'target' => '',
			'plegado' => false
		);
	}

	static function get_utileria_editor_ver_php($item_visualizador, $id_componente, $icono='nucleo/php.gif', $parametros=array())
	{
		$param_local = array(apex_hilo_qs_zona => $id_componente['proyecto'] . apex_qs_separador . $id_componente['componente']);
		$parametros = array_merge($param_local, $parametros);
		$opciones = array('zona' => true, 'celda_memoria' => 'central', 'menu' => true);
		$vinculo = toba::vinculador()->get_url(toba_editor::get_id(), $item_visualizador, $parametros, $opciones);
		return array( 'imagen' => toba_recurso::imagen_toba($icono, false),
				'ayuda' => 'Ver el contenido del archivo PHP',
				'vinculo' => $vinculo,
				'plegado' => true
		);		
	}

	static function get_ef_popup_utileria_php()
	{
		$iconos = array();
		$iconos[] = new ef_popup_utileria_php(false);
		$iconos[] = new ef_popup_utileria_php(true);
		return $iconos;
	}

	static function get_ef_popup_utileria_extension_php($parametros=array())
	{
		//Armo el icono para la extension del componente
		$icono_edicion = new ef_popup_utileria_php(false, false);
		$icono_edicion->cambiar_item(3463);
		$icono_edicion->agregar_parametros($parametros);
		$icono_edicion->invocar_sin_archivo(true);
		$icono_edicion->registrar();
		return array($icono_edicion);
	}

	static function get_ef_popup_utileria_abrir_php()
	{
		return array(new ef_popup_utileria_php(true));
	}
}
?>