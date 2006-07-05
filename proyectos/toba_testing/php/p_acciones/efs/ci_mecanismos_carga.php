<?php 
//--------------------------------------------------------------------
class ci_mecanismos_carga extends objeto_ci
{
	protected $registro;
	protected $datos_ml;
	
	function mantener_estado_sesion()
	{
		$atr = parent::mantener_estado_sesion();
		$atr[] = 'registro';
		return $atr;
	}
	
	function get_editable_dao()
	{
		return "Cargado";	
	}
	
	function get_popup_dao($estado=null)
	{
		if (!isset($estado)) {
			return "Sin estado";	
		} else {
			return "El estado es $estado";
		}
	}

	function evt__form__modificacion($registro)
	{
		$this->registro = $registro;
	}
	
	function evt__form__carga()
	{
		return $this->registro;	
	}
	
	function evt__form_ml__carga()
	{
		return $this->datos_ml;
	}
	
	function evt__form_ml__modificacion($datos)
	{
		$this->datos_ml = $datos;
	}	
}

?>