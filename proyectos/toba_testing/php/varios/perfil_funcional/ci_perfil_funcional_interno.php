<?php
class ci_perfil_funcional_interno extends toba_testing_pers_ci
{
	protected $s__datos;
	
	function ini()
	{
	}
	
	function conf()
	{
		try {
			$this->set_pantalla('pant_dos');
		} catch (Exception $e) {
			$this->pantalla()->set_descripcion("Lanzado OK de excepcion");		
		}
		
		$this->pantalla()->tab('pant_dos')->desactivar();
	}

	function conf__form_prueba(toba_ei_formulario $form)
	{
		if (! isset($this->s__datos)) {
			$this->s__datos['editable'] = 'Texto solo-lectura';
		}
		$form->set_datos($this->s__datos);
	}
	
	function evt__form_prueba__modificacion($datos)
	{
		ei_arbol($datos);
		$this->s__datos = $datos;
	}
}

?>