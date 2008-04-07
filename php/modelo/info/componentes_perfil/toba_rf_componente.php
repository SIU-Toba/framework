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
		$this->no_visible_original = ($datos['no_visible'] !='') ? 1 : 0;
		$this->no_visible_actual = $this->no_visible_original;
		$icono = array(
				'imagen' => toba_recurso::imagen_toba( $datos['icono'], false),
				'ayuda' => "Carpeta que contiene operaciones.",
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
		$sql = "SELECT 	o.nombre as 			nombre,
						c.icono as				icono,
						rfei.no_visible as		no_visible,
						(SELECT COUNT(*) FROM apex_objeto_eventos e WHERE e.objeto = o.objeto AND e.proyecto = o.proyecto) as eventos
				FROM 	apex_clase c,
						apex_objeto o
							LEFT OUTER JOIN apex_restriccion_funcional_ei rfei
								ON o.objeto = rfei.objeto AND o.proyecto = rfei.proyecto
								AND rfei.item = '$this->item'
								AND rfei.restriccion_funcional = '$this->restriccion'
				WHERE 	o.objeto = '$this->componente' 
					AND o.proyecto = '$this->proyecto'
					AND o.clase = c.clase";
		toba::logger()->debug($sql);
		return toba::db()->consultar_fila($sql);
	}

	function cargar_eventos()
	{
		$eventos = $this->cargar_datos_eventos();
		if(count($eventos) > 0) {
			$grupo = new toba_rf_grupo_eventos('<b>EVENTOS</b>',$this);
			$evt = array();
			foreach($eventos as $evento) {
				$evento['etiqueta'] = str_replace('&','', $evento['etiqueta']);
				$nombre = (isset($evento['etiqueta']) && $evento['etiqueta'] != '') ? $evento['etiqueta'] : '[' . $evento['identificador'] . ']';
				$evt[] = new toba_rf_subcomponente_evento($nombre, $grupo, $evento['evento_id'], $evento['proyecto'], $this->item, $this->restriccion, $evento['no_visible']);
			}
			$grupo->set_hijos($evt);
			$this->agregar_hijo($grupo);
		}
	}

	function cargar_datos_eventos()
	{
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
							AND re.item = '$this->item'
							AND re.restriccion_funcional = '$this->restriccion'
				WHERE	(e.implicito <> 1 OR e.implicito IS NULL)
				AND		e.objeto = '$this->componente' 
				AND		e.proyecto = '$this->proyecto'
				ORDER BY e.orden";
		toba::logger()->debug($sql);
		return toba::db()->consultar($sql);
	}

	function get_input($id)
	{
		$id_input = $id.'_oculto';
		$valor_inicial = $this->no_visible_actual ? 1 : 0;
		$img_inicial = $this->no_visible_actual ? $this->img_oculto : $this->img_visible;		
		$html = '';		
		$html .= "<img src='$img_inicial' id='".$id_input."_img' onclick='cambiar_oculto(\"$id_input\")' />";
		$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_oculto'])) {
			if ($_POST[$id.'_oculto']) {
				$this->no_visible_actual = $_POST[$id.'_oculto'];	
			}else{
				$this->no_visible_actual = 0;
			}
		}		
	}
	
	function agregar_restriccion()
	{
		$sql = "INSERT INTO 
					apex_restriccion_funcional_ei (proyecto, restriccion_funcional, item, objeto, no_visible) 
				VALUES 
					('$this->proyecto','$this->restriccion','$this->item','$this->componente', '$this->no_visible_actual');";
		toba::db()->ejecutar($sql);
	}
	
	function eliminar_restriccion()
	{
		$sql = "DELETE FROM 
					apex_restriccion_funcional_ei
				WHERE
					proyecto = '$this->proyecto'
				AND restriccion_funcional = '$this->restriccion'
				AND objeto = '$this->componente';";
		toba::db()->ejecutar($sql);
	}
	
	function sincronizar()
	{
		if($this->no_visible_original != $this->no_visible_actual) {
			if ($this->no_visible_actual == 1) {
				$this->agregar_restriccion();
			}else{
				$this->eliminar_restriccion();
			}
		}
	}
	
}
?>