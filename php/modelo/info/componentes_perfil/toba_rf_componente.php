<?php 

class toba_rf_componente extends toba_rf
{
	protected $proyecto;
	protected $componente;
	protected $no_visible_original;
	protected $no_visible;
	
	function __construct($restriccion, $proyecto, $item, $componente, $padre) 
	{
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$this->componente = $componente;
		$datos = $this->cargar_datos();
		parent::__construct($datos['nombre'], $padre, $this->item .'-'. $this->componente);
		$this->no_visible_original = $datos['no_visible'];
		$this->no_visible = $this->no_visible_original;
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( $datos['icono'], false),
				'ayuda' => "Carpeta que contiene operaciones.",
				);
		if($datos['eventos']>0) {
			$this->cargar_eventos();	
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
		return toba::db()->consultar_fila($sql);
	}

	function cargar_eventos()
	{
		$eventos = $this->cargar_datos_eventos();
		if(count($eventos) > 0) {
			$grupo = new toba_rf_grupo_eventos('EVENTOS',$this);
			$evt = array();
			foreach($eventos as $evento) {
				$evento['etiqueta'] = str_replace('&','', $evento['etiqueta']);
				$nombre = isset($evento['etiqueta']) ? $evento['etiqueta'] : '[' . $evento['identificador'] . ']';
				$evt[] = new toba_rf_subcomponente_evento($nombre, $grupo, $evento['evento_id'] , $this->item, $this->restriccion, $evento['estado_original']);		
			}
			$grupo->set_hijos($evt);
			$this->agregar_hijo($grupo);
		}
	}

	function cargar_datos_eventos()
	{
		$sql = "SELECT 	e.etiqueta as		etiqueta, 
						e.identificador as 	identificador,
						e.evento_id as		evento_id,
						re.no_visible as	estado_original,
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
		return toba::db()->consultar($sql);
	}

	function get_input($id)
	{
		$id_input = $id.'_oculto';
		$check_oculto = $this->oculto ? 'checked' : '';
		$html = '';
		$html .= "<LABEL for='$id_input'>Ocultar</LABEL>";
		$html .= "<input type='checkbox' $check_oculto value='1' id='$id_input' name='$id_input' />";
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_oculto'])) {
			$this->oculto = $_POST[$id.'_oculto'];
		} else {
			$this->oculto = false;
		}		
	}
}
?>