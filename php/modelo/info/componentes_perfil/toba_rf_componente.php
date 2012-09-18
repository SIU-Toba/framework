<?php 

class toba_rf_componente extends toba_rf
{
	protected $componente;
	
	function __construct($restriccion, $proyecto, $item, $componente, $padre) 
	{
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$this->componente = $componente;
		$datos = $this->cargar_datos();
		parent::__construct($datos['nombre'], $padre, $this->item .'-'. $this->componente);
		if (!isset($datos['descripcion'])) {
			$this->nombre_largo = $this->nombre_corto;
		}else{
			$this->nombre_largo = $datos['descripcion'];
		}
		$this->no_visible_original = ($datos['no_visible'] !='') ? true : false;
		$this->no_visible_actual = $this->no_visible_original;
		$icono = array(
				'imagen' => toba_recurso::imagen_toba( $datos['icono'], false),
				'ayuda' => "Componente",
				);
		$this->agregar_icono($icono);
		if($datos['eventos']>0) {
			$this->cargar_eventos();	
		}
		if ($this->no_visible_original) {
			$this->marcar_abiertos();	
		}		
	}

	function cargar_datos()
	{
		$item = quote($this->item);
		$restriccion = quote($this->restriccion);
		$componente = quote($this->componente);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT 	o.nombre as 			nombre,
						c.icono as			icono,
						rfei.no_visible as		no_visible,
						(SELECT COUNT(*) FROM apex_objeto_eventos e WHERE e.objeto = o.objeto AND e.proyecto = o.proyecto) as eventos
				FROM 	apex_clase c,
						apex_objeto o
						LEFT OUTER JOIN apex_restriccion_funcional_ei rfei
								ON o.objeto = rfei.objeto AND o.proyecto = rfei.proyecto
								AND rfei.item = $item
								AND rfei.restriccion_funcional = $restriccion
				WHERE 	o.objeto = $componente
					AND o.proyecto = $proyecto
					AND o.clase = c.clase";
		return toba::db()->consultar_fila($sql);
	}

	//-----------------------------------------------------------------------------------------------//
	//						EVENTOS					       //
	//-----------------------------------------------------------------------------------------------//
	function cargar_eventos()
	{
		$eventos = $this->cargar_datos_eventos();
		if(! empty($eventos)) {
			$evt = array();			
			$grupo = new toba_rf_grupo_eventos('<b>EVENTOS</b>', $this);
			foreach($eventos as $evento) {
				$evento['etiqueta'] = str_replace('&','', $evento['etiqueta']);
				$nombre = (trim($evento['etiqueta']) != '') ? $evento['etiqueta'] : '[' . $evento['identificador'] . ']';
				$evt[] = new toba_rf_subcomponente_evento($nombre, $grupo, $evento['evento_id'], $evento['proyecto'], $this->item, $this->restriccion, $evento['no_visible']);
			}
			$grupo->set_hijos($evt);
			$this->agregar_hijo($grupo);
		}
	}

	function cargar_datos_eventos()
	{
		$item = quote($this->item);
		$restriccion = quote($this->restriccion);
		$componente = quote($this->componente);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT 	e.proyecto as 		proyecto,
						e.etiqueta as		etiqueta, 
						e.identificador as 	identificador,
						e.evento_id as		evento_id,
						re.no_visible as	no_visible,
						imagen_recurso_origen,
						imagen		
				FROM 	apex_objeto_eventos e
						LEFT OUTER JOIN apex_restriccion_funcional_evt re
							ON e.evento_id = re.evento_id AND e.proyecto = re.proyecto
							AND re.item = $item
							AND re.restriccion_funcional = $restriccion
				WHERE	(e.implicito <> 1 OR e.implicito IS NULL)
				AND		e.objeto = $componente
				AND		e.proyecto = $proyecto
				ORDER BY e.orden";
		return toba::db()->consultar($sql);
	}

	function get_input($id)
	{
		$id_input = $id.'_oculto';
		$valor_inicial = ($this->no_visible_actual) ? 1 : 0;
		$img_inicial = ($this->no_visible_actual) ? $this->img_oculto : $this->img_visible;		
		$html = "<img src='$img_inicial' id='".$id_input."_img' title='Visible / Oculto' onclick='{$this->id_js_arbol}.cambiar_oculto(\"{$this->get_id()}\")' />";
		if ($this->comunicacion_elemento_input) {
			$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
		}
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_oculto'])) {
			if ($_POST[$id.'_oculto'] == '1') {
				$this->no_visible_actual = true;	
			}else{
				$this->no_visible_actual = false;
			}
		}		
	}
	
	function agregar_restriccion()
	{
		$no_visible = ($this->no_visible_actual) ? '1': '0';
		$proyecto = quote($this->proyecto);
		$restriccion = quote($this->restriccion);
		$item = quote($this->item);
		$comp = quote($this->componente);		
		$sql = "INSERT INTO 
					apex_restriccion_funcional_ei (proyecto, restriccion_funcional, item, objeto, no_visible) 
				VALUES 
					($proyecto, $restriccion, $item, $comp, $no_visible);";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_restriccion()
	{
		$proyecto = quote($this->proyecto);
		$restriccion = quote($this->restriccion);
		$comp = quote($this->componente);		
		$sql = "DELETE FROM 
					apex_restriccion_funcional_ei
				WHERE
					proyecto = $proyecto
				AND restriccion_funcional = $restriccion
				AND objeto = $comp;";
		toba::db()->ejecutar($sql);
	}
	
	function sincronizar()
	{
		if ($this->no_visible_original != $this->no_visible_actual) {
			if ($this->no_visible_actual) {
				$this->agregar_restriccion();
			}else{
				$this->eliminar_restriccion();
			}
		}
	}			
}
?>