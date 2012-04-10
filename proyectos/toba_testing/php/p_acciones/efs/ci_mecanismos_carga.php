<?php 
//--------------------------------------------------------------------
class ci_mecanismos_carga extends toba_testing_pers_ci
{
	protected $registro;
	protected $datos_ml;
	
	function set_propiedades_sesion()
	{
		$atr = parent::set_propiedades_sesion(array('registro'));
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
	
	function conf__form()
	{
		if (! isset($this->registro)) {
			$this->registro['popup_dao'] = 4;
		}	
		return $this->registro;	
	}
	
	function conf__form_ml()
	{
		return $this->datos_ml;
	}
	
	function evt__form_ml__modificacion($datos)
	{
		$this->datos_ml = $datos;
	}	
}

?>