<?php 

class ci_catalogo extends toba_ci
{
	protected $datos_editores;
	
	function ini()
	{
		//Inicializa la lista de editores
		$info_componentes = toba_info_editores::get_info_tipos_componente();
		foreach ($info_componentes as $componente) {
			$this->datos_editores[$componente['clase']] = array('proyecto' => $componente['editor_proyecto'],
																	'item' => $componente['editor_item']);
		}
	}
	
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fuentes(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_fuentes_datos();
		foreach (array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'fuente.png';
			$parametros = array(apex_hilo_qs_zona => $datos[$id]['proyecto'] .apex_qs_separador. $datos[$id]['fuente_datos']);
			$datos[$id]['editar'] = "<span style='white-space: nowrap;'>";
			// Probar dimensiones
			if (toba_info_editores::get_cantidad_dimensiones_fuente($datos[$id]['fuente_datos']) > 0) {
				$img = toba_recurso::imagen_toba('probar_dimensiones.png', true, null, null, 'Probar dimensiones');
				$datos[$id]['editar'] .= $this->tag_vinculo_editor(toba_editor::get_id(),
																	3461,
																	$parametros,
																	$img);
			}
			// Relaciones
			$img = toba_recurso::imagen_toba('solic_wddx.gif', true, null, null, 'Relaciones entre tablas');
			$datos[$id]['editar'] .= $this->tag_vinculo_editor(toba_editor::get_id(),
																3442,
																$parametros,
																$img);
			// Ver el modelo
			$img = toba_recurso::imagen_toba('buscar.png', true, null, null, 'Navegar tablas');
			$datos[$id]['editar'] .= $this->tag_vinculo_editor(toba_editor::get_id(),
																3412,
																$parametros,
																$img);
																
			// Creacion / Actualizacion Automatica de datos tabla
			$img = toba_recurso::imagen_toba('objetos/dt_refresh.gif', true, null, null, 'Creacion y actualización automatica de los datos_tabla');
			$datos[$id]['editar'] .= $this->tag_vinculo_editor(toba_editor::get_id(),
																33000010,
																$parametros,
																$img);
																																	
			// Editar la fuente
			$img = toba_recurso::imagen_toba('objetos/editar.gif', true, null, null);
			$datos[$id]['editar'] .= $this->tag_vinculo_editor(toba_editor::get_id(),
																1000237,
																$parametros,
																$img);
			$datos[$id]['editar'] .= '</span>';
		}
		$cuadro->set_datos($datos);
		$cuadro->colapsar();
	}

	function conf__consultas(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_consultas_php();
		foreach (array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'consulta_php.gif';
			$img = toba_recurso::imagen_toba('objetos/editar.gif', true, null, null);
			$parametros = array( apex_hilo_qs_zona => $datos[$id]['proyecto'] .apex_qs_separador. $datos[$id]['consulta_php']);
			$datos[$id]['editar'] = $this->tag_vinculo_editor(toba_editor::get_id(), '3398', $parametros, $img);
			if (admin_util::existe_archivo_subclase($datos[$id]['archivo'])) {
				$id_consulta = array($datos[$id]['proyecto'], $datos[$id]['consulta_php']);
				$parametros['archivo'] = $datos[$id]['archivo'];
				$datos[$id]['editar'] = admin_util::get_acceso_ver_php($id_consulta, 30000014, apex_frame_centro, $parametros). $datos[$id]['editar'];
				$datos[$id]['editar'] = admin_util::get_acceso_abrir_php($id_consulta, 30000014, $parametros) . $datos[$id]['editar'];				
			}
			$datos[$id]['editar'] = "<div class='editor-lista-vinculos'>" . $datos[$id]['editar'] . '</div>';
		}
		$cuadro->set_datos($datos);
		$cuadro->colapsar();
	}
	
	function conf__tablas(toba_ei_cuadro $cuadro)
	{
		$proyecto = toba_contexto_info::get_proyecto();
		$catalogo = new toba_catalogo_objetos($proyecto);
		$objetos = $catalogo->get_objetos(array('clase' => 'toba_datos_tabla'));
		$tablas = toba_info_editores::get_tabla_fuente_de_dt();
		$tablas = rs_convertir_asociativo($tablas, array('id'), 'tabla');
		$datos = array();
		foreach ($objetos as $comp) {
			$tabla = $tablas[$comp->get_id()];
			$datos[$tabla]['editar'] = $this->get_string_iconos($comp->get_utilerias());
			$datos[$tabla]['proyecto'] = $proyecto;
			$datos[$tabla]['fuente'] = $comp->get_fuente_datos();
			$datos[$tabla]['objeto'] = $comp->get_id();
			$datos[$tabla]['tabla'] = $tabla;		
			$datos[$tabla]['icono'] = $this->get_string_iconos($comp->get_iconos());
		}
		//Lo recorre de nuevo para que esten en orden		
		$salida = array();
		foreach ($tablas as $tabla) {
			$salida[] = $datos[$tabla];
		}
		$cuadro->set_datos($salida);
		$cuadro->colapsar();
	}

	function conf__dimensiones($cuadro)
	{
		$datos = toba_info_editores::get_dimensiones();
		foreach (array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'filtrar.png';
			$img = toba_recurso::imagen_toba('objetos/editar.gif', true, null, null);
			$parametros = array( apex_hilo_qs_zona => $datos[$id]['proyecto'] .apex_qs_separador. $datos[$id]['dimension']);
			$datos[$id]['editar'] = $this->tag_vinculo_editor(toba_editor::get_id(),
																'3441',
																$parametros,
																$img);
		}
		$cuadro->set_datos($datos);
		$cuadro->colapsar();
	}

	function conf__arbol_relaciones($componente)
	{
		$componente->set_frame_destino(apex_frame_centro);
		$proyecto = toba_contexto_info::get_proyecto();
		$catalogo = new toba_catalogo_objetos($proyecto);
		$objetos = $catalogo->get_objetos(array('clase' => 'toba_datos_relacion'));
		$componente->set_datos($objetos);
		$componente->colapsar();
	}

	function evt__arbol_relaciones__cargar_nodo($id)
	{
		$this->dependencia('arbol_relaciones')->set_frame_destino(apex_frame_centro);		
		$proyecto = toba_contexto_info::get_proyecto();
		$catalogo = new toba_catalogo_objetos($proyecto);
		$opciones['id'] = $id;
		$obj = $catalogo->get_objetos($opciones, true);
		return $obj;
	}	
	
	
	function conf__servicios_web(toba_ei_cuadro $cuadro)
	{
		$cuadro->colapsar();
		$datos = toba_info_editores::get_servicios_web_acc();
		foreach (array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'fuente.png';
			$parametros = array('menu' => 1,  apex_hilo_qs_zona => $datos[$id]['proyecto'] .apex_qs_separador. $datos[$id]['servicio_web']);
			$datos[$id]['editar'] = "<span style='white-space: nowrap;'>";
			// Editar la fuente
			$img = toba_recurso::imagen_toba('objetos/editar.gif', true, null, null);
			$datos[$id]['editar'] .= $this->tag_vinculo_editor(toba_editor::get_id(),
																	30000048,
																	$parametros,
																	$img);
			$datos[$id]['editar'] .= '</span>';
		}		
		$cuadro->set_datos($datos);		
	}

	//---------------------------------------------------------------
	//---------------------------------------------------------------

	function tag_vinculo_editor($item_editor_proyecto, $item_editor, $parametros, $contenido, $ayuda=null)
	{
		$url = toba::vinculador()->get_url($item_editor_proyecto, 
												$item_editor,
												$parametros,
												array('menu' => true, 'celda_memoria' => 'central'));
		$ayuda = isset($ayuda) ? ' title="'.$ayuda.'" ' : '';
		return "<a href='".$url."' target='".apex_frame_centro."' ".$ayuda." >".$contenido."</a>\n";
	}

	function get_string_iconos($iconos)
	{
		$salida = '';
		foreach ($iconos as $icono) {
			$ayuda = toba_parser_ayuda::parsear($icono['ayuda']);
			$img = toba_recurso::imagen($icono['imagen'], null, null, $ayuda);
			if (isset($icono['vinculo'])) {
				$salida .= "<a target='".apex_frame_centro."' href=\"".$icono['vinculo']."\">$img</a>\n";
			} else {
				$salida .= $img."\n";
			}
		}
		return $salida;	
	}
}

class pantalla_catalogo extends toba_ei_pantalla 
{
	function generar_layout()
	{
		foreach ($this->_dependencias as $dep) {
			$dep->generar_html();	
		}
	}
}

?>
