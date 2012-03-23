<?php
require_once('zona_editor.php');

class zona_dimension extends zona_editor
{
	function cargar_info($editable=null)	
	{	//Carga el EDITABLE que se va a manejar dentro de la ZONA
		$sql = '	SELECT	descripcion
					FROM	apex_dimension
					WHERE	proyecto='.quote($this->editable_id[0]).'
					AND		dimension ='.quote($this->editable_id[1]).';';
		//echo $sql;
		$rs = toba::db()->consultar($sql);
		if (!$rs) {
			echo ei_mensaje('ZONA-DIMENSION: El editable solicitado no existe', 'info');
			return false;
		} else {
			$this->editable_info = $rs[0];
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_cargado = true;
			return true;
		}	
	}

}
?>