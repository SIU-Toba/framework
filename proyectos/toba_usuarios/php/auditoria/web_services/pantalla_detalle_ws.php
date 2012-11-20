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
		echo toba_form::textarea('log_archivo', $this->contenido_archivo_log, 20, 80);		
		parent::generar_layout();
	}	
}

?>