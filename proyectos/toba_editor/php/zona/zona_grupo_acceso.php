<?php
require_once('zona_editor.php');

class zona_grupo_acceso extends zona_editor
{
	function cargar_info($editable=null)
	{	//Carga el EDITABLE que se va a manejar dentro de la ZONA
		$sql = '	SELECT	*
					FROM	apex_usuario_grupo_acc
					WHERE	proyecto = '.quote($this->editable_id[0]).'
					AND		usuario_grupo_acc = '.quote($this->editable_id[1]).';';
		//echo $sql;
		$rs = toba::db()->consultar($sql);
		if (!$rs) {
			toba::notificacion()->agregar('ZONA - GRUPO ACCESO: El editable solicitado no existe', 'info');
			return false;
		} else {
			$this->editable_info = $rs[0];
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array($this->editable_id[0], $this->editable_id[1]);
			$this->editable_cargado = true;
			return true;
		}	
	}


}
?>