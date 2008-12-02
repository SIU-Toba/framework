<?php 

class ci_subclases_generacion extends toba_ci
{
	protected $s__datos_opciones;	
	protected $s__datos_metodos;
	protected $visualizacion;

	
	//-----------------------------------------------------------------
	//---------- OPCIONES 
	//-----------------------------------------------------------------	
	function conf__form_opciones(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_opciones)) {
			$form->set_datos($this->s__datos_opciones);
		}
	}
	
	function evt__form_opciones__modificacion($datos)
	{
		$this->s__datos_opciones = $datos;
	}
	
	function get_opciones()
	{
		return $this->s__datos_opciones;		
	}
	
	//-----------------------------------------------------------------
	//---------- METODOS 
	//-----------------------------------------------------------------
	
	function conf__form_metodos(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_metodos)) {
			$form->set_datos($this->s__datos_metodos);
		}
	}
	
	function evt__form_metodos__modificacion($datos)
	{
		$this->s__datos_metodos = $datos;
	}
	
	//-----------------------------------------------------------------
	//---------- VISTA PREVIA 
	//-----------------------------------------------------------------	
	
	function get_previsualizacion()
	{
		return $this->previsualizacion;	
	}	
	
	function conf__pant_vista_previa()
	{
		$codigo = $this->controlador()->get_codigo_vista_previa();
		require_once(toba_dir()."/php/3ros/PHP_Highlight.php");
		$h = new PHP_Highlight(false);
		$h->loadString($codigo);
		$formato_linea = "<span style='background-color:#D4D0C8; color: black; font-size: 10px;".
						" padding-top: 2px; padding-right: 2px; margin-left: -4px; width: 20px; text-align: right;'>".
						"%2d</span>&nbsp;&nbsp;";
		$this->previsualizacion = $h->toHtml(true, true, $formato_linea, true);
	}
	
	function get_metodos_a_generar()
	{
		$metodos = array();
		foreach ($this->s__datos_metodos as $clave => $valor) {
			if ($valor) {
				$clave = explode('_', $clave);
				$metodos[] = end($clave);
			}
		}		
		return $metodos;
	}



	
	

}

?>