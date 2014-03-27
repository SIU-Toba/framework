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
		echo htmlentities($this->contenido_archivo_log, ENT_QUOTES);
		echo '</pre>';
		
		parent::generar_layout();
	}	
}

?>