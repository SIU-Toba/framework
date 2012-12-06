<?php 
class toba_rf_ci extends toba_rf_componente
{
	protected $primer_nivel;
	
	function __construct($restriccion, $proyecto, $item, $componente, $padre, $primer_nivel= false, $expande_deps_sin_pantalla = false) 
	{
		parent::__construct($restriccion, $proyecto, $item, $componente, $padre);
		$this->primer_nivel = $primer_nivel;
		$grupo = new toba_rf_grupo_pantallas('<b>PANTALLAS</b>', $this);
		$this->agregar_hijo($grupo);
		$deps = $this->cargar_datos_dependencias();
		$pantallas = $this->cargar_datos_pantallas();
		$obj_pantalla = $this->cargar_datos_objetos_pantalla();
		
		//-----------------------------------------------------------------------------------------------//
		//		Armo los arreglos de pantallas y dependencias
		//-----------------------------------------------------------------------------------------------//
		$deps_en_pantalla = array(); $lista_deps = array();										//clasifico las dependencias por pantallas y hago una lista completa de las usadas
		foreach($obj_pantalla as $obj) {
			$deps_en_pantalla[$obj['pantalla']][] = $obj['identificador'];
			$lista_deps[] = $obj['identificador'];
		}
		
		$deps_sin_pantalla = array();														//Busco las deps que no estan asociadas a una pantalla
		foreach($deps as $dep) {
			if (! in_array($dep['rol'], $lista_deps)) {
				$deps_sin_pantalla[] = $dep['rol']; 
			}
		}

		//-----------------------------------------------------------------------------------------------//
		//		Comienzo procesando las deps fuera de pantallas
		//-----------------------------------------------------------------------------------------------//		
		if (! empty($deps_sin_pantalla) && $expande_deps_sin_pantalla) {
			$this->armar_rama_deps($deps, array('sueltas' => $deps_sin_pantalla), 'sueltas', $this, $expande_deps_sin_pantalla);	
		}
		
		//-----------------------------------------------------------------------------------------------//
		//		Proceso las deps que estan asociadas a pantallas
		//-----------------------------------------------------------------------------------------------//
		foreach($pantallas as $pantalla) {
			//Creo la pantalla
			$p = new toba_rf_pantalla($this->restriccion, $this->item, $pantalla, $grupo, $this->id . '_' . $pantalla['pantalla']);
			if (isset($deps_en_pantalla[$pantalla['pantalla']])) {									//Si hay deps en la pantalla en cuestion
				$this->armar_rama_deps($deps, $deps_en_pantalla, $pantalla['pantalla'], $p, $expande_deps_sin_pantalla);
			}
			$grupo->agregar_hijo($p);		
		}
		
	}
	
	function armar_rama_deps($deps, $deps_pantalla, $pantalla_actual, &$p, $expande_deps_sin_pantalla)
	{
		foreach($deps as $dep) {
			if (in_array($dep['rol'], $deps_pantalla[$pantalla_actual])) {
				switch ($dep['clase']) {
					case 'toba_ci'	:
						$o = new toba_rf_ci($this->restriccion, $this->proyecto, $this->item, $dep['objeto'], $p, $expande_deps_sin_pantalla);
						break;
					case 'toba_ei_cuadro' :
						$o = new toba_rf_componente_cuadro($this->restriccion, $this->proyecto, $this->item, $dep['objeto'], $p);
						break;
					case 'toba_ei_formulario'	:
					case 'toba_ei_formulario_ml'	:
						$o = new toba_rf_componente_formulario($this->restriccion, $this->proyecto, $this->item, $dep['objeto'], $p);
						break;
					case 'toba_ei_filtro'	:
						$o = new toba_rf_componente_filtro($this->restriccion, $this->proyecto, $this->item, $dep['objeto'], $p);
						break;
					default:
						$o = new toba_rf_componente($this->restriccion, $this->proyecto, $this->item, $dep['objeto'], $p);
				}
				$p->agregar_hijo($o);
			}
		}
		return $p;		
	}
	
	function cargar_datos_pantallas()
	{
		$item = quote($this->item);
		$restriccion = quote($this->restriccion);
		$proyecto = quote($this->proyecto);
		$componente = quote($this->componente);
		$sql = "SELECT 	p.objeto_ci_proyecto as proyecto,
						p.pantalla,
						p.identificador,
						p.orden,
						p.etiqueta,
						p.eventos,
						p.objetos,
						p.objeto_ci,
						p.imagen,
						p.imagen_recurso_origen,
						rfp.no_visible as no_visible
				FROM 	apex_objeto_ci_pantalla p
					LEFT OUTER JOIN apex_restriccion_funcional_pantalla rfp
							ON 	p.objeto_ci_proyecto = rfp.proyecto
							AND p.objeto_ci = rfp.objeto_ci
							AND p.pantalla = rfp.pantalla
							AND rfp.item = $item
							AND rfp.restriccion_funcional = $restriccion
				WHERE 	p.objeto_ci_proyecto = $proyecto
					AND p.objeto_ci = $componente
				ORDER BY orden";
		return toba::db()->consultar($sql);
	}

	function cargar_datos_dependencias()
	{
		$componente = quote($this->componente);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT 	o.objeto as 			objeto,
						o.clase as 			clase,
						d.identificador as		rol
				FROM 	apex_objeto_dependencias d,
						apex_objeto o,
						apex_clase c
				WHERE	d.objeto_consumidor = $componente
					AND d.proyecto = $proyecto
					AND o.objeto = d.objeto_proveedor 
					AND o.proyecto = d.proyecto
					AND o.clase = c.clase
					AND c.clase_tipo IN (7,8)";
		return toba::db()->consultar($sql);
	}

	function cargar_datos_objetos_pantalla()
	{
		$proyecto = quote($this->proyecto);
		$componente = quote($this->componente);
		$sql = "SELECT	op.pantalla,
						op.orden,
						op.dep_id,
						od.identificador
			     FROM		apex_objetos_pantalla op,
					         apex_objeto_dependencias od
			     WHERE op.proyecto = $proyecto
				AND	op.objeto_ci = $componente
				AND	op.proyecto = od.proyecto
				AND	op.objeto_ci = od.objeto_consumidor
				AND	op.dep_id	= od.dep_id
			    ORDER BY	op.pantalla, op.orden";
		return toba::db()->consultar($sql);
	}

	function get_input($id)
	{
		if(!$this->primer_nivel) {
			$id_input = $id.'_oculto';
			$valor_inicial = $this->no_visible_actual ? 1 : 0;
			$img_inicial = $this->no_visible_actual ? $this->img_oculto : $this->img_visible;			
			$html = "<img src='$img_inicial' id='".$id_input."_img' title='Visible / Oculto' onclick='{$this->id_js_arbol}.cambiar_oculto(\"{$this->get_id()}\")' />";
			if ($this->comunicacion_elemento_input) {
				$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";
			}
			return $html;
		}
	}
	
	function sincronizar()
	{
		if (!$this->primer_nivel) {
			if($this->no_visible_original != $this->no_visible_actual) {
				if ($this->no_visible_actual) {
					$this->agregar_restriccion();
				}else{
					$this->eliminar_restriccion();
				}
			}
		}
		foreach ($this->get_hijos() as $hijo){
			$hijo->sincronizar();
		}
	}
	
	function cargar_estado_post($id)
	{
		if(!$this->primer_nivel) {
			if (isset($_POST[$id.'_oculto'])) {
				if ($_POST[$id.'_oculto'] == '1') {
					$this->no_visible_actual = true;
				} else {
					$this->no_visible_actual = false;
				}
			}		
		}
	}
	
}
?>