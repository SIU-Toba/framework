<?php 

class ci_catalogo extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fuentes(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_fuentes_datos();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'fuente.png';
		}
		$cuadro->set_datos($datos);
	}

	function conf__tablas(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_lista_objetos_dt();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'objetos/datos_tabla.gif';
		}
		$cuadro->set_datos($datos);
	}

	function conf__relaciones(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_lista_objetos_dr();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'objetos/datos_relacion.gif';
		}
		$cuadro->set_datos($datos);
	}

	function conf__consultas(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_consultas_php();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'editar.gif';
		}
		$cuadro->set_datos($datos);
	}
/*
	function get_acceso_editores($clase, $id_componente)
	{
	}	
	function acceso_zona($parametros = array())
	{
		$parametros[apex_hilo_qs_zona] = $this->proyecto . apex_qs_separador . $this->id;
		return $parametros;
	}

	function vinculo_editor($parametros = array())
	{
		$editor_item = $this->datos['_info']['clase_editor_item'];
		$editor_proyecto = $this->datos['_info']['clase_editor_proyecto'];
		return toba::vinculador()->generar_solicitud( $editor_proyecto, $editor_item, $this->acceso_zona($parametros),
															false, false, null, true, 'central');
	}
		//Editor
		if (isset($this->datos['_info']['clase_editor_proyecto'])) {
			$ayuda = null;
			if (in_array($this->datos['_info']['clase'], toba_info_editores::get_lista_tipo_componentes())) {
				$metodo = "get_pantallas_".$this->datos['_info']['clase'];
				$pantallas = call_user_func(array("toba_datos_editores", $metodo));
				//-- Se incluye un vinculo a cada pantalla encontrada
				$ayuda = "<div class='editor-lista-vinculos'>";
				foreach ($pantallas as $pantalla) {
					$img = ($pantalla['imagen'] != '') ? $pantalla['imagen'] : "objetos/fantasma.gif";
					$origen = ($pantalla['imagen'] != '') ? $pantalla['imagen_recurso_origen'] : 'apex';
					$vinculo = $this->vinculo_editor(array('etapa' => $pantalla['identificador']));
					$tag_img = ($origen == 'apex') ? toba_recurso::imagen_toba($img, true) : toba_recurso::imagen_proyecto($img, true);
					$ayuda .= '<a href='.$vinculo.' target='.apex_frame_centro.
								" title='".$pantalla['etiqueta']."'>".
								$tag_img.
								'</a> ';
				}
				$ayuda .= "</div>";
				$ayuda = str_replace("'", "\\'", $ayuda);
			}
			$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => $ayuda,
				'vinculo' => $this->vinculo_editor()
			);
		}
		return $iconos;	
*/
}
?>
