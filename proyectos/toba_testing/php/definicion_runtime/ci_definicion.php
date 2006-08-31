<?php 
//--------------------------------------------------------------------
class ci_definicion extends toba_ci
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

	function conf()
	{
		$clave = array('componente' => '1000127', 'proyecto' => 'toba_testing');
		$metadatos = cargador_toba::instancia()->get_metadatos_extendidos($clave, 'ei_formulario');
		$nuevo_ef = array(
			'identificador'  => 'nuevo_ef',
			'columnas' => 'nuevo_ef',
			'obligatorio' => 1,
			'elemento_formulario' => 'ef_editable',
			'etiqueta' => 'NUEVO!!!',
			'descripcion' => 'Este ef se añadio dinámicamente',
			'inicializacion' => '',
			'colapsado' => 0
		);
		$metadatos['info_formulario_ef'] = array();
		$metadatos['info_formulario_ef'][] = $nuevo_ef;
		cargador_toba::instancia()->set_metadatos_extendidos($metadatos, $clave);				
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