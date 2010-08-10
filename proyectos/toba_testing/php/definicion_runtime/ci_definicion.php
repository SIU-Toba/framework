<?php 
//--------------------------------------------------------------------
class ci_definicion extends toba_testing_pers_ci
{
	protected $datos = array();
	
	function extender_objeto_js()
	{
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = 'propiedad_a_persistir';
		return $propiedades;
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__procesar()
	{
	}

	function evt__cancelar()
	{
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function ini()
	{
		$clave = array('componente' => '1000127', 'proyecto' => 'toba_testing');
		$metadatos = toba_cargador::instancia()->get_metadatos_extendidos($clave, 'toba_ei_formulario');
		$nuevo_ef = array(
			'identificador'  => 'nuevo_ef',
			'columnas' => 'nuevo_ef',
			'obligatorio' => 1,
			'elemento_formulario' => 'ef_editable',
			'etiqueta' => 'NUEVO!!!',
			'descripcion' => 'Este ef se añadio dinámicamente',
			'inicializacion' => '',
			'colapsado' => 0,
			'oculto_relaja_obligatorio' => 0
		);
		$metadatos['_info_formulario_ef'] = array();
		$metadatos['_info_formulario_ef'][] = $nuevo_ef;
		toba_cargador::instancia()->set_metadatos_extendidos($metadatos, $clave);				
	}
	
	//---- form -------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$this->datos = $datos;
	}

	function conf__form()
	{
		return $this->datos;
	}

}

?>