<?php 

class ci_catalogo extends toba_ci
{
	protected $datos_editores;
	
	function ini()
	{
		//Inicializa la lista de editores
		$info_componentes = toba_info_editores::get_info_tipos_componente();
		foreach($info_componentes as $componente) {
			$this->datos_editores[$componente['clase']] = array(	'proyecto' => $componente['editor_proyecto'],
																	'item' => $componente['editor_item']);
		}
	}
	
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
			$datos[$id]['editar'] = $this->get_acceso_editores('toba_datos_tabla',$datos[$id]['proyecto'],$datos[$id]['objeto']);
		}
		$cuadro->set_datos($datos);
	}

	function conf__relaciones(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_lista_objetos_dr();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'objetos/datos_relacion.gif';
			$datos[$id]['editar'] = $this->get_acceso_editores('toba_datos_relacion',$datos[$id]['proyecto'],$datos[$id]['objeto']);
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
	
	//---------------------------------------------------------------
	//---------------------------------------------------------------

	function get_acceso_editores($clase, $proyecto, $componente)
	{
		$parametros_editor[apex_hilo_qs_zona] = $proyecto . apex_qs_separador . $componente;
		// AYUDA
		$ayuda = null;
		$metodo = "get_pantallas_$clase";
		$pantallas = call_user_func(array("toba_datos_editores", $metodo));
		//-- Se incluye un vinculo a cada pantalla encontrada
		$ayuda = "<div class='editor-lista-vinculos'>";
		foreach ($pantallas as $pantalla) {
			$img = ($pantalla['imagen'] != '') ? $pantalla['imagen'] : "objetos/fantasma.gif";
			$origen = ($pantalla['imagen'] != '') ? $pantalla['imagen_recurso_origen'] : 'apex';
			$vinculo = $this->vinculo_editor($clase, array_merge( $parametros_editor, array('etapa' => $pantalla['identificador'])) );
			$tag_img = ($origen == 'apex') ? toba_recurso::imagen_toba($img, true) : toba_recurso::imagen_proyecto($img, true);
			$ayuda .= '<a href='.$vinculo.' target='.apex_frame_centro.
						" title='".$pantalla['etiqueta']."'>".
						$tag_img.
						'</a> ';
		}
		$ayuda .= "</div>";
		$ayuda = str_replace("'", "\\'", $ayuda);
		
		//$img = toba_recurso::imagen_toba("objetos/editar.gif", true, null, null);
		$img = toba_recurso::imagen_toba("objetos/editar.gif", true, null, null, $ayuda);

		$html = "<a href=\"".$this->vinculo_editor($clase, $parametros_editor)."\" target=\"".apex_frame_centro."\">$img</a>\n";
		return $html;	
	}

	function vinculo_editor($clase, $parametros)
	{
		return toba::vinculador()->generar_solicitud( 		$this->datos_editores[$clase]['proyecto'], 
															$this->datos_editores[$clase]['item'],
															$parametros,
															false, false, null, true, 'central');
	}
}
?>