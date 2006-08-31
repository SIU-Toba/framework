<?php

/**
*	Utilidades varias para el administrador Toba
*/
class admin_util
{
	
	/**
	*	Refresca el frame izquierdo del editor
	*/
	static function refrescar_editor_item()
	{
		echo toba_js::abrir();
		$frame = "parent.".apex_frame_lista;
		echo "
			if ($frame.objeto_ci_1381) {
				$frame.objeto_ci_1381.set_evento(new $frame.evento_ei('refrescar', true, '' ));
			}
		";
		echo toba_js::cerrar();		
	}
	
	function redireccionar_a_editor_objeto($proyecto, $objeto)
	{
		$clave = array( 'componente'=>$objeto, 'proyecto'=>$proyecto );
		$vinculo = constructor_toba::get_info($clave)->vinculo_editor();
		admin_util::refrescar_editor_item();
		echo toba_js::abrir();
		echo "window.location.href='$vinculo'\n";
		echo toba_js::cerrar();		
	}
	
	function get_url_desarrollos()
	{
		$host = (toba_instalacion::instancia()->get_id_grupo_desarrollo() != 0) ? "desarrollos2" : "desarrollos";
		return "https://$host.siu.edu.ar";
	}
	
	static function get_icono_abrir_php($archivo)
	{
		$parametros = array('archivo' => $archivo);
		$opciones = array('servicio' => 'ejecutar', 'celda_memoria' => 'ajax', 'validar' => false);
		$vinculo = toba::get_vinculador()->crear_vinculo(editor::get_id(),"/admin/objetos/php", $parametros, $opciones);
		$js = "toba.comunicar_vinculo('$vinculo')";
		$ayuda = toba_recurso::ayuda(null, 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
						   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]');
		return "<img style='cursor:pointer' onclick=\"$js\" src='".toba_recurso::imagen_apl('reflexion/abrir.gif', false)."' $ayuda>";		
	}
}
?>