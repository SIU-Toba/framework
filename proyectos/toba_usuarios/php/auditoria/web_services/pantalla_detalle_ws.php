<?php
class pantalla_detalle_ws extends toba_ei_pantalla
{
	protected $contenido_archivo_log;
	
	function set_contenido_archivo_log($texto)
	{
		$this->contenido_archivo_log = $texto;
	}
	
	function generar_layout()
	{
		echo "<pre style='width: 700px; overflow: scroll; font-size: 11px'>";
		echo toba::escaper()->escapeHtml($this->contenido_archivo_log);
		echo '</pre>';
		
		parent::generar_layout();
	}	
}

?>