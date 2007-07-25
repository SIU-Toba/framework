<?php 

class ci_listado_moldes extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Elegir molde ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_planes($componente)
	{
		return toba_info_editores::get_lista_moldes_existentes();
	}

	//-----------------------------------------------------------------------------------
	//---- Opciones ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function evt__opciones()
	{
		$this->set_pantalla('opciones');
	}
	
	function evt__volver()
	{
		$this->set_pantalla('elegir');	
	}
	
	function conf__form_opciones(toba_ei_formulario $form)
	{
		$this->dep('opciones')->cargar(array('proyecto' => toba_editor::get_proyecto_cargado()));
		return $this->dep('opciones')->get();
	}
	
	function evt__form_opciones__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dep('opciones')->set($datos);
		$this->dep('opciones')->sincronizar();
	}
	

}
?>