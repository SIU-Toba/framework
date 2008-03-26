<?php 
class toba_rf_ci extends toba_rf_componente
{
	protected $primer_nivel;
	
	function __construct($restriccion, $proyecto, $item, $componente, $padre, $primer_nivel= false) 
	{
		parent::__construct($restriccion, $proyecto, $item, $componente, $padre);
		$this->primer_nivel = $primer_nivel;
		$grupo = new toba_rf_grupo_pantallas('PANTALLAS', $this);
		$this->agregar_hijo($grupo);
		$deps = $this->cargar_datos_dependencias();
		$pantallas = $this->cargar_datos_pantallas();
		foreach($pantallas as $pantalla) {
			//Creo la pantalla
			$p = new toba_rf_pantalla($pantalla['etiqueta'], $grupo, $this->id . '_' . $pantalla['pantalla']);
			$grupo->agregar_hijo($p);
			//Cargo las dependencias
			$deps_pantalla = array_map('trim',explode(',',$pantalla['objetos']));
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
						case 'toba_ei_filtro'	:
							$o = new toba_rf_componente_formulario($this->restriccion, $this->proyecto, $this->item, $dep['objeto'], $p);
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
		$sql = "SELECT 	p.pantalla,
						p.identificador,
						p.orden,
						p.etiqueta,
						p.eventos,
						p.objetos,
						rfp.no_visible as no_visible
				FROM 	apex_objeto_ci_pantalla p
					LEFT OUTER JOIN apex_restriccion_funcional_pantalla rfp
							ON 	p.objeto_ci_proyecto = rfp.proyecto
							AND p.objeto_ci = rfp.objeto_ci
							AND rfp.item = '$this->item'
							AND rfp.restriccion_funcional = '$this->restriccion'
				WHERE 	p.objeto_ci_proyecto = '$this->proyecto' 
					AND p.objeto_ci = '$this->componente'
				ORDER BY orden";
		toba::logger()->debug($sql);
		return toba::db()->consultar($sql);
	}

	function cargar_datos_dependencias()
	{
		$sql = "SELECT 	o.objeto as 			objeto,
						o.clase as 				clase,
						d.identificador as		rol
				FROM 	apex_objeto_dependencias d,
						apex_objeto o,
						apex_clase c
				WHERE	d.objeto_consumidor = '$this->componente' 
					AND d.proyecto = '$this->proyecto'
					AND o.objeto = d.objeto_proveedor 
					AND o.proyecto = d.proyecto
					AND o.clase = c.clase
					AND c.clase_tipo IN (7,8)";
		return toba::db()->consultar($sql);
	}
	
	function get_input($id)
	{
		if(!$this->primer_nivel) {
			$check_oculto = $this->no_visible_actual ? 'checked' : '';
			$html = '';
			$html .= "<input type='checkbox' $check_oculto value='1' name='".$id."_oculto' />";
			return $html;
		}
	}
	
	function sincronizar()
	{
		if (!$this->primer_nivel) {
			
		}
	}
	
	function cargar_estado_post($id)
	{
		if(!$this->primer_nivel) {
			if (isset($_POST[$id.'_oculto'])) {
				$this->no_visible_actual = $_POST[$id.'_oculto'];
			} else {
				$this->no_visible_actual = '';
			}		
		}
	}
	
}
?>