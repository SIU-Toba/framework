<?php 
class toba_rf_componente extends toba_rf
{
	protected $restriccion;
	protected $proyecto;
	protected $item;
	protected $componente;
	protected $no_visible_original;
	protected $no_visible;
	
	function __construct($restriccion, $proyecto, $item, $componente, $padre= null) 
	{
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$this->componente = $componente;
		$datos = $this->cargar_datos();
		$this->no_visible_original = $datos['no_visible'];
		$this->no_visible = $this->no_visible_original;
		$this->set_iconos( array($datos['icono']) );
		parent::__construct($datos['nombre'], $padre=null);
	}

	function cargar_datos()
	{
		$sql = "SELECT 	o.nombre as 			nombre,
						c.icono as				icono,
						rfei.no_visible as		no_visible
				FROM 	apex_clase c,
						apex_objeto o
							LEFT OUTER JOIN apex_restriccion_funcional_ei rfei
								ON o.objeto = rfei.objeto AND o.proyecto = rfei.proyecto
				WHERE 	o.objeto = '$this->componente' 
					AND o.proyecto = '$this->proyecto'
					AND o.clase = c.clase
					AND rfei.item = '$this->item'
					AND rfei.restriccion_funcional = '$this->restriccion'";
		return toba::db()->consultar_fila($sql);
	}

	function cargar_eventos()
	{
	}

	function cargar_datos_eventos()
	{
		$sql = "SELECT 	e.etiqueta as		etiqueta, 
						e.evento_id as		evento_id,
						re.no_visible as	no_visible
				FROM 	apex_evento e
						LEFT OUTER JOIN apex_restriccion_funcional_evt re
							ON e.evento_id = re.evento_id AND e.proyecto = re.proyecto
							WHERE re.item = '$this->item'
							AND re.proyecto = '$this->proyecto'
							AND re.restriccion_funcional = '$this->restriccion'
				WHERE	e.implicito <> 1 OR e.implicito IS NULL
				AND		e.objeto = '$this->componente' 
				AND		e.proyecto = '$this->proyecto'
				ORDER BY e.orden";
		return toba::db()->consultar($sql);
	}
}
?>