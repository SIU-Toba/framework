<?php 
class toba_rf_ci extends toba_rf_componente
{
	protected $primer_nivel;
	
	function __construct($restriccion, $proyecto, $item, $componente, $padre, $primer_nivel= false) 
	{
		parent::__construct($restriccion, $proyecto, $item, $componente, $padre);
		$this->primer_nivel = $primer_nivel;
		$grupo = new toba_rf_grupo_pantallas('<b>PANTALLAS</b>', $this);
		$this->agregar_hijo($grupo);
		$deps = $this->cargar_datos_dependencias();
		$pantallas = $this->cargar_datos_pantallas();
		$obj_pantalla = $this->cargar_datos_objetos_pantalla();
		foreach($pantallas as $pantalla) {
			//Creo la pantalla
			if ($pantalla['orden']=='' || $pantalla['orden']=='1') {
				$primera = true;
			}else{
				$primera = false;
			}
			$p = new toba_rf_pantalla($this->restriccion, $this->item, $pantalla, $grupo, $this->id . '_' . $pantalla['pantalla'], $primera);
			$grupo->agregar_hijo($p);
			//Cargo las dependencias
			$deps_pantalla = array();
			foreach($obj_pantalla as $obj){
				if ($pantalla['pantalla'] == $obj['pantalla']){
					$deps_pantalla[] = $obj['identificador'];
				}
			}			
			foreach( $deps as $dep ) {
				if( in_array($dep['rol'], $deps_pantalla) ){
					switch ($dep['clase']) {
						case 'toba_ci'	:
							$o = new toba_rf_ci($this->restriccion, $this->proyecto, $this->item, $dep['objeto'], $p);
							break;
						case 'toba_ei_cuadro'	:
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
		}
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
						o.clase as 				clase,
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
					 FROM	apex_objetos_pantalla op,
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
			$html = '';		
			$html .= "<img src='$img_inicial' id='".$id_input."_img' title='Visible / Oculto' onclick='cambiar_oculto(\"$id_input\")' />";
			$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
			return $html;
		}
	}
	
	function sincronizar()
	{
		if (!$this->primer_nivel) {
			if($this->no_visible_original != $this->no_visible_actual) {
				if ($this->no_visible_actual == 1) {
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
				if ($_POST[$id.'_oculto']) {
					$this->no_visible_actual = $_POST[$id.'_oculto'];	
				} else {
					$this->no_visible_actual = 0;
				}
			}		
		}
	}
	
}
?>