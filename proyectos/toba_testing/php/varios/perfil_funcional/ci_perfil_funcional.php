<?php
class ci_perfil_funcional extends toba_testing_pers_ci
{
	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}
	
	function conf()
	{
		$this->dep('filtro')->set_descripcion('bla');		
	}

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_lista_items();
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->set_pantalla('pant_edicion');
	}
	
	function evt__cancelar()
	{
		$this->set_pantalla('pant_seleccion');
	}
	
	function evt__procesar()
	{
		throw new toba_error('No se debio ejecutar el procesar');
	}
}

?>