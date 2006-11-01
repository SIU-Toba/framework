<?php 
class ci_observaciones_solicitud extends toba_ci
{
	protected $id_solicitud;
	
	function ini()
	{
		$this->id_solicitud = toba::memoria()->get_parametro('id');
	}

	function conf__cuadro($componente)
	{
		$componente->set_datos(consultas_instancia::get_solicitud_observaciones($this->id_solicitud));
	}
}
?>