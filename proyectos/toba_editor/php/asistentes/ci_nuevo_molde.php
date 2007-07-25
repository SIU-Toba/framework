<?php 
class ci_nuevo_molde extends toba_ci
{
	protected $s__tipo;
	protected $datos_tipo_operacion;
	
	function ini()
	{
		if (isset($this->s__tipo)) {
			$this->cargar_editor_molde();
		}	
	}
	
	function cargar_editor_molde()
	{
		$info = toba_info_editores::get_lista_tipo_molde($this->s__tipo['tipo']);
		$ci = $info['ci'];
		$this->agregar_dependencia('asistente', 'toba_editor', $ci);	
		$this->dep('asistente')->set_molde_nuevo($this->s__tipo['tipo']);
	}
	
	//-----------------------------------------------------------------------------------
	//---- Navegacion ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	
	
	function evt__siguiente_editar()
	{
		$this->set_pantalla('pant_edicion');	
	}
	
	function evt__siguiente_generar()
	{
		$this->set_pantalla('pant_generacion');	
	}
	
	
	function evt__generar()
	{
		$this->dep('asistente')->sincronizar();
	}
	
	function evt__volver_editar()
	{
		$this->set_pantalla('pant_tipo_operacion');	
	}	
	
	function evt__volver_generar()
	{
		$this->set_pantalla('pant_edicion');	
	}	
		
	
	//-----------------------------------------------------------------------------------
	//---- Elegir tipo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	

	function conf__form_tipo_operacion()
	{
		if (isset($this->s__tipo)) {
			return $this->s__tipo;
		}
	}
	
	function evt__form_tipo_operacion__modificacion($datos)
	{
		$this->s__tipo = $datos;
		$this->cargar_editor_molde();
	}	

	//-----------------------------------------------------------------------------------
	//---- Editar ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	
	function conf__pant_edicion()
	{
		$info = toba_info_editores::get_lista_tipo_molde($this->s__tipo['tipo']);
		$this->pantalla()->set_descripcion('Edición de un '.$info['descripcion_corta']);
		$this->pantalla()->agregar_dep('asistente');		
	}

	//-----------------------------------------------------------------------------------
	//---- Generación ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
		
	function conf__form_molde(toba_ei_formulario $form)
	{
		$relacion = $this->dep('asistente')->dep('datos');
		$form->set_datos($relacion->tabla('molde')->get());
	}
	
	function evt__form_molde__modificacion($datos)
	{
		$relacion = $this->dep('asistente')->dep('datos');
		$relacion->tabla('molde')->set($datos);		
	}
	
}

?>