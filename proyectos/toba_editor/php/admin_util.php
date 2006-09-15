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
	
	static function get_url_desarrollos()
	{
		$host = (toba::instalacion()->get_id_grupo_desarrollo() != 0) ? "desarrollos2" : "desarrollos";
		return "https://$host.siu.edu.ar";
	}
	
	static function get_icono_abrir_php($archivo)
	{
		$parametros = array('archivo' => $archivo);
		$opciones = array('servicio' => 'ejecutar', 'celda_memoria' => 'ajax', 'validar' => false, 'menu' => true );
		$vinculo = toba::vinculador()->crear_vinculo(toba_editor::get_id(),"/admin/objetos/php", $parametros, $opciones);
		$js = "toba.comunicar_vinculo('$vinculo')";
		$ayuda = toba_recurso::ayuda(null, 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
						   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]');
		return "<img style='cursor:pointer' onclick=\"$js\" src='".toba_recurso::imagen_apl('reflexion/abrir.gif', false)."' $ayuda>";		
	}
}
?>