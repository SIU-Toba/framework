<?php

/**
*	Utilidades varias para el administrador Toba
*/
define('toba_abrir_archivo_ajax','toba_abrir_archivo_ajax');

class admin_util
{
	
	/**
	*	Refresca el frame izquierdo del editor
	*/
	static function refrescar_editor_item($ir_a_item=null)
	{
		echo toba_js::abrir();
		$frame = "parent.".apex_frame_lista;
		echo "if ($frame.js_arbol_1368_items) {\n";
		if (isset($ir_a_item)) {
			echo "$frame.js_arbol_1368_items.ver_propiedades('$ir_a_item');\n";
		} else {
			echo "$frame.js_arbol_1368_items.set_evento(new $frame.evento_ei('refrescar', true, '' ));\n";
		}
		echo "	}\n";
		echo toba_js::cerrar();		
	}
	
	static function redirecionar_a_editor_item($proyecto, $item)
	{
		$clave = array( 'proyecto' => $proyecto, 'componente' => $item );		
		$elem_item = toba_constructor::get_info($clave, 'item');
		$vinculo = $elem_item->vinculo_editor();
		echo toba_js::abrir();
		echo "window.location.href='$vinculo'\n";
		echo toba_js::cerrar();
	}
	
	static function redireccionar_a_editor_objeto($proyecto, $objeto)
	{
		$clave = array( 'componente'=>$objeto, 'proyecto'=>$proyecto );
		$vinculo = toba_constructor::get_info($clave)->vinculo_editor();
		admin_util::refrescar_editor_item();
		echo toba_js::abrir();
		echo "window.location.href='$vinculo'\n";
		echo toba_js::cerrar();		
	}
	
	static function get_icono_abrir_php($archivo)
	{
		$parametros = array('archivo' => $archivo);
		$opciones = array('servicio' => 'ejecutar', 'celda_memoria' => 'ajax', 'validar' => false, 'menu' => true );
		$vinculo = toba::vinculador()->crear_vinculo(toba_editor::get_id(),"/admin/objetos/php", $parametros, $opciones);
		$js = "toba.comunicar_vinculo('$vinculo')";
		$ayuda = toba_recurso::ayuda(null, 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
						   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]');
		return "<img style='cursor:pointer' onclick=\"$js\" src='".toba_recurso::imagen_proyecto('reflexion/abrir.gif', false)."' $ayuda>";		
	}

	static function existe_archivo_subclase($path_relativo)
	{
		$path_real = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/" . $path_relativo;
		return file_exists($path_real) && is_file($path_real);
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
				return toba_recurso::url_proyecto(toba_editor::get_proyecto_cargado())."/img/".$img;
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
				return toba::instancia()->get_path_proyecto($cargado)."/www/img";				
				break;
				
			default: throw new toba_error("No esta contemplado el origen $origen");
		}
	}	

}
?>