<?php
require_once("zona_editor.php");

class zona_fuente extends zona_editor
{
	function cargar_info($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		$sql = 	"	SELECT	*
					FROM	apex_fuente_datos
					WHERE	proyecto='{$this->editable_id[0]}'
					AND		fuente_datos='{$this->editable_id[1]}';";
		//echo $sql;
		$rs = toba::get_db()->consultar($sql);
		if(!$rs) {
			echo ei_mensaje("ZONA-FUENTE: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = $rs[0];
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $this->editable_id[0],$this->editable_id[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}

}
?>