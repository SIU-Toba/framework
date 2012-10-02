<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_principal extends toba_ci
{
	protected $datos_form;
	protected $datos_form_ml;
		
	function ini()
	{
		$default = array( 'descripcion' => '', 'obligatorio' => '0', 'oculto_relaja_obligatorio' => '0', 'colapsado' => '0', 'permitir_html' => '0', 
			'carga_maestros' => 'combo_dao', 'carga_metodo' => 'get_desc_nuevo_ef', 'carga_permite_no_seteado' => '0');
		$this->dep('form')->agregar_ef ('nuevo_ef', 'ef_editable', 'Agregado Dinamicamente', 'nuevo_din', $default);	
	}
		
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos_form';
		$propiedades[] = 'datos_form_ml';		
		return $propiedades;
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
					'combo_editable' => '5',
				    'combo_db' => '2',
				    'checkbox' => '1',
				    'fecha' => '2006-01-04',
				    'editable' => 'HOLA',
				    'moneda' => '1211.32',
				    'multilinea' => '1 - Este es un multil�nea.
2 - Este es un multil�nea.
3 - Este es un multil�nea.
4 - Este es un multil�nea.
5 - Este es un multil�nea.
6 - Este es un multil�nea.
7 - Este es un multil�nea.
8 - Este es un multil�nea.
9 - Este es un multil�nea.',
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
				    'radio' => '2',
				    'cuit' => '20055121711'
				  );		
	}
	
	function get_desc_nuevo_ef($param)
	{
		if (isset($param)) {
			$datos = $this->datos_combo_dao();
			return $datos[$param]['valor'];
		} else {
			return 'Seleccione el combo!';
		}
	}
	
	//------------------------------------------------------------------------
	//-------------------------- FORMULARIO COM�N --------------------------
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
		if (! isset($this->datos_form)) {
			$this->datos_form = $this->datos_comunes_form();
		}
		return $this->datos_form;	
	}

	//------------------------------------------------------------------------
	//-------------------------- FORMULARIO ML --------------------------
	//------------------------------------------------------------------------

	function evt__form_ml__carga_defecto()
	{
		$datos_comunes = $this->datos_comunes_form();
		$datos_comunes['editable'] = 'Cargado no me podes modificar';
		$this->datos_form_ml = array($datos_comunes, $datos_comunes, $datos_comunes);
	}
	
	function conf__form_ml()
	{
		return $this->datos_form_ml;
	}	
	
	function evt__form_ml__modificacion($datos)
	{
		foreach ($datos as $id => $dato) {
			if (isset($dato['upload'])) {
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