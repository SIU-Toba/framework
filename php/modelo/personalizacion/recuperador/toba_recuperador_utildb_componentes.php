<?php

class toba_recuperador_utildb_componentes
{
	protected $proyecto;
	protected $db;

	function __construct($proyecto)
	{
		$this->proyecto = $proyecto;
		$this->db = $this->proyecto->get_db();
	}

	static function get_lista_tipos_componentes()
	{
		$datos = toba_info_editores::get_lista_tipo_componentes(false);
		$datos[] = 'toba_item';
		return $datos;
	}

	function get_componentes($tipo, $condicion, $schema)
	{
		$comp_sano = $this->db->quote($tipo);
		if ($tipo == 'toba_item') {
			$datos = $this->get_toba_items($condicion, $schema);
		} elseif(strpos($tipo,'toba_asistente')!== false) {
			$datos = $this->get_asistentes($comp_sano, $condicion, $schema);
		} else {
			$datos = $this->get_resto($comp_sano, $condicion, $schema);
		}
		return $datos;
	}

	private function get_toba_items($condicion = null, $schema = null)
	{
		$proyecto = $this->db->quote($this->proyecto->get_id());
		$schema = (is_null($schema)) ? '' : "$schema.";
		$condicion = (is_null($condicion)) ? '' : "AND $condicion";

		$sql = "SELECT 	proyecto as 		proyecto,
						item as 			componente
				FROM $schema apex_item
				WHERE proyecto = $proyecto
				$condicion
				ORDER BY 1,2;";

		return $this->db->consultar($sql);
	}

	private function get_asistentes($tipo, $condicion = null, $schema = null)
	{
		$proyecto = $this->db->quote($this->proyecto->get_id());
		$schema = (is_null($schema)) ? '' : "$schema.";
		$condicion = (is_null($condicion)) ? '' : "AND $condicion";
		$sql = "SELECT 	o.proyecto as 		proyecto,
						o.molde as 			componente,
						t.clase
				FROM 	$schema apex_molde_operacion o,
						$schema apex_molde_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		t.clase = $tipo
				AND		proyecto = $proyecto
				$condicion
				ORDER BY 1,2;";

		return $this->db->consultar($sql);
	}

	private function get_resto($tipo, $condicion = null, $schema = null)
	{
		$proyecto = $this->db->quote($this->proyecto->get_id());
		$schema = (is_null($schema)) ? '' : "$schema.";
		$condicion = (is_null($condicion)) ? '' : "AND $condicion";

		$sql = "SELECT 	proyecto as 		proyecto,
						objeto as 			componente
				FROM $schema apex_objeto
				WHERE proyecto = $proyecto
				AND clase = $tipo
				$condicion
				ORDER BY 1,2;";

		return $this->db->consultar( $sql );
	}
}
?>
