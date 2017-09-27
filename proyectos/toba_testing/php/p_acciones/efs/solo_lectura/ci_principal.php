<?php

class ci_principal extends toba_testing_pers_ci
{
	protected $datos_form;
	protected $datos_form_ml;
	
	function ini()
	{
		$this->set_propiedades_sesion(array('datos_form', 'datos_form_ml'));		
	}	
	
	function datos_combo_dao()
	{
		$res = array(
			array('clave' => 1, 'valor' => 'Uno'),
			array('clave' => 2, 'valor' => 'Dos'),
			array('clave' => 3, 'valor' => 'Tres')
		);
		return $res;	
	}
	
	function datos_comunes_form() 
	{
			return array (
				    'combo_dao' => '1',
				    'combo_db' => '2',
				    'checkbox' => '1',
				    'fecha' => '2006-01-04',
				    'editable' => 'HOLA',
				    'moneda' => '1211.32',
				    'multilinea' => '1 - Este es un multilínea.
2 - Este es un multilínea.
3 - Este es un multilínea.
4 - Este es un multilínea.
5 - Este es un multilínea.
6 - Este es un multilínea.
7 - Este es un multilínea.
8 - Este es un multilínea.
9 - Este es un multilínea.',
				    'numero' => '1212',
				    'porcentaje' => '99',
				    'multi_seleccion' => 
					    array (
					      0 => '2',
					      1 => '3',
					    ),
				    'multi_sel_check' => 
					    array (
					      0 => '1',
					      1 => '2',
					    ),
					'multi_sel_doble' =>
						array('2','3'),
				    'popup' => '2',
				    'upload' => 'Nombre del archivo.jpg',
				    'radio' => '2'
				  );		
	}

	function get_descripcion()
	{
		return 'Que miras?';
	}

	//------------------------------------------------------------------------
	//-------------------------- FORMULARIO COMÚN --------------------------
	//------------------------------------------------------------------------
		
	function evt__form__readonly_server()
	{
		$this->dependencia('form')->set_solo_lectura();
	}
	
	function evt__form__modificacion($datos)
	{
		if (isset($datos['upload'])) {
			$datos['upload'] = $datos['upload']['name'];
		}
		$this->datos_form = $datos;	
	}
	
	function conf__form()
	{
		return $this->datos_form;	
	}

	function evt__form__carga_defecto()
	{
		$this->datos_form = $this->datos_comunes_form();
	}

	//------------------------------------------------------------------------
	//-------------------------- FORMULARIO ML --------------------------
	//------------------------------------------------------------------------

	function evt__form_ml__carga_defecto()
	{
		$this->datos_form_ml = array($this->datos_comunes_form(),
									$this->datos_comunes_form(),
									$this->datos_comunes_form());
	}
	
	function conf__form_ml()
	{
		return $this->datos_form_ml;
	}	
	
	function evt__form_ml__modificacion($datos)
	{
		foreach ($datos as $id => $dato) {
			if (isset($datos[$id]['upload'])) {
				$datos[$id]['upload'] = $dato['upload']['name'];
			}
		}
		$this->datos_form_ml = $datos;
	}
	
	function evt__form_ml__readonly_server()
	{
		$this->dependencia('form_ml')->set_solo_lectura();
	}	
}

?>