<?php
require_once('zona_editor.php');

class zona_usuario extends zona_editor
{
	protected function get_editable_id()
	{
		return $this->editable_id;
	}	
		
	function cargar_info($editable=null)
	{	//Carga el EDITABLE que se va a manejar dentro de la ZONA
		$sql = '	SELECT	*
					FROM	apex_usuario
					WHERE	usuario='.quote($this->editable_id);
		$rs = toba::db()->consultar($sql);
		if (!$rs) {
			echo ei_mensaje('ZONA-USUARIO: El editable solicitado no existe', 'info');
			return false;
		} else {
			$this->editable_info = $rs[0];
			//ei_arbol($this->editable_info,"EDITABLE");
			return true;
		}	
	}

	function generar_html_barra_inferior()	
	{
		//echo "BARRA inferior<br>"	;	
	}
}
?>