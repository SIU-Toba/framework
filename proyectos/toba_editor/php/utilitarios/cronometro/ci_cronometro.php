<?php 

class ci_cronometro extends toba_ci
{
	protected $s__solicitud;
	protected $s__proyecto;
	
	function ini()
	{
		$solicitud = toba::memoria()->get_parametro('solicitud');
		$proyecto = toba::memoria()->get_parametro('solicitud_proy');
		
		if ($proyecto) {
			$this->s__proyecto = $proyecto;
		} else {
			$this->s__proyecto = toba_editor::get_proyecto_cargado();
		}		
		if ($solicitud) {
			$this->s__solicitud = $solicitud;	
		} else {
			//Consulta la ultima solicitud
			 $schema_logs = toba::instancia()->get_db()->get_schema(). '_logs';
	        $sql = "SELECT max(solicitud) as ultima 
				FROM $schema_logs.apex_solicitud_cronometro
				WHERE proyecto=".quote($this->s__proyecto);
			$rs = toba::instancia()->get_db()->consultar_fila($sql);	
			if (! empty($rs)) {
				$this->s__solicitud = $rs['ultima'];
			}
		}	

	}
	
	function get_solicitud()
	{
		return $this->s__solicitud;
	}
	
	function get_proyecto()
	{
		return $this->s__proyecto;	
	}
	
	function evt__borrar()
	{
		$schema_logs = toba::instancia()->get_db()->get_schema(). '_logs';
		$sql = "DELETE FROM $schema_logs.apex_solicitud_cronometro";
		toba::instancia()->get_db()->ejecutar($sql);
		$this->s__solicitud = null;
	}
}


?>