<?php
require_once('zona_editor.php');

class zona_consulta_php extends zona_editor
{
	function cargar_info($editable=null)	
	{	//Carga el EDITABLE que se va a manejar dentro de la ZONA
		$sql = '	SELECT	clase,
							descripcion,
							archivo
					FROM	apex_consulta_php
					WHERE	proyecto='.quote($this->editable_id[0]).'
					AND		consulta_php='.quote($this->editable_id[1]).';';
		//echo $sql;
		$rs = toba::db()->consultar($sql);
		if (!$rs) {
			echo ei_mensaje('ZONA-CONSULTA_PHP: El editable solicitado no existe', 'info');
			return false;
		} else {
			$this->editable_info = $rs[0];
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_cargado = true;
			return true;
		}	
	}

	protected function get_editable_id()
	{
		return $this->editable_info['clase'];
	}
	
	function get_archivo()
	{
		return $this->editable_info['archivo'];
	}

	function generar_html_barra_vinculos()
	{	
		if ($this->get_archivo() != '' && admin_util::existe_archivo_subclase($this->get_archivo())) {
			$parametros = array('archivo' => $this->get_archivo());
			echo admin_util::get_acceso_abrir_php($this->get_editable(), 30000014, $parametros);
			echo admin_util::get_acceso_ver_php($this->get_editable(), 30000014, apex_frame_centro, $parametros);
		}
		parent::generar_html_barra_vinculos();
	}

	

}
?>