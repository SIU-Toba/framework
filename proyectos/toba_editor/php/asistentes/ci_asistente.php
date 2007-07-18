<?php 
class ci_asistente extends toba_ci
{
	protected $s__tipo;

	function conf__form_tipo_operacion()
	{
		if (isset($this->s__tipo)) {
			return $this->s__tipo;
		}
	}
	
	function evt__form_tipo_operacion__modificacion($datos)
	{
		$this->s__tipo = $datos;
	}	
	
	function evt__siguiente()
	{
		$this->set_pantalla('pant_edicion');	
	}
	
	function evt__volver()
	{
		$this->set_pantalla('pant_tipo_operacion');	
	}	
}

?>