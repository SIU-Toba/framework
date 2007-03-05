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
		return "<img style='cursor:pointer' onclick=\"$js\" src='".toba_recurso::imagen_toba('reflexion/abrir.gif', false)."' $ayuda>";		
	}

	static function existe_archivo_subclase($path_relativo)
	{
		$path_real = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/" . $path_relativo;
		return file_exists($path_real) && is_file($path_real);
	}
	
	static function generar_html_imagenes()
	{
		$src = toba::memoria()->get_parametro('imagen');
		$origen = toba::memoria()->get_parametro('imagen_recurso_origen');
		
		if ($origen == 'apex') {
			$dir = toba::instalacion()->get_path().'/www/img';	
			$url = toba_recurso::url_toba();
		} else {
			$cargado = toba_editor::get_proyecto_cargado();
			$dir = toba::instancia()->get_path_proyecto($cargado)."/www/img";
			$url = toba_recurso::url_proyecto($cargado);
		}
		echo "<div id='editor_imagen_listado'>";
		echo "<table>";
		$archivos = toba_manejador_archivos::get_archivos_directorio($dir, '/(.)png|(.)gif|(.)jpg|(.)jpeg/', false);
		$columnas = 3;
		$cant = 1;
		$total = count($archivos);
		foreach ($archivos as $archivo) {
			if ($cant % $columnas == 1) {
				echo "<tr>";
			}
			$relativo = substr($archivo, strlen($dir)+1);
			echo "<td title='Seleccionar imagen' onclick='seleccionar_imagen(\"$relativo\")'>
					<img  src='".$url."/img/".$relativo."' />
					<div>$relativo</div>
				</td>\n";
			
			if ($cant % $columnas == 0) {
				echo "</tr>\n";
			}			
			$cant++;
		}
		if ($cant % $columnas != 0) {
			echo "</tr>\n";
		}
		echo "</table></div>";
	}
}
?>