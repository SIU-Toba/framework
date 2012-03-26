<?php
class ci_visor_observaciones extends toba_ci
{
	protected $id_solicitud;
	
	function ini()
	{
		$id = toba::memoria()->get_parametro('id');	//Obtengo el id de solicitud
		if (isset($id) && !is_null($id)) {
			$this->id_solicitud = $id;
		}
	}

	function conf__cuadro($cuadro)
	{
		if (isset($this->id_solicitud)) {
			$datos = consultas_instancia::get_solicitud_observaciones($this->id_solicitud);
			if (! empty($datos)) {
				$cuadro->set_datos($datos);
			}
		}
	}
}

?>