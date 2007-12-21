<?php 
class ci_importar_operacion extends toba_ci
{
	protected $s__datos;
	
	function get_catalogo_operaciones()
	{
		$salida = array();
		$salida[] = array(
			'proyecto' => 'toba_referencia',
			'item' => '1000182',
			'nombre' => 'Consulta de Auditoría'
		);
		return $salida;
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__importar()
	{
		
	}

	//---- form_origen ------------------------------------------------------------------

	function evt__form_origen__modificacion($datos)
	{
		$this->s__datos = $datos;	
	}

	function conf__form_origen(toba_ei_formulario $form)
	{
		$form->set_datos($this->s__datos);
		
	}
}

?>