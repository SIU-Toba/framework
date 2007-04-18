<?php

class test_editable_numero extends test_toba
/*
	Ejercita los siguientes contratos:
	- El parametro rango limita el rango de números permitidos con esta sintaxis:
		(minimo..maximo] donde 
			-'(' o ')' indica no incluido y '[' o ']' indica incluido
			-minimo y maximo pueden ser '*' indicando un 'no importa'
	- Este parametro puede cambiarse en ejecución
*/
{
	protected $parametros;
	protected $id_form = 'nombre_formnombre_padreid';
	protected $clase;
	
	function get_descripcion()
	{
		return "EF Número";
	}	

	function SetUp()
	{
		$this->clase = 'ef_editable_numero';
	}
	
	function get_ef($parametros)
	{
		$padre = array(1=> 'nombre_padre');
		$ef = new $this->clase($padre, 'nombre_form', 'id', 'etiqueta', 'descripcion', 'dato', false, $parametros);
		return $ef;	
	}
	
	function validar_correctos($ef, $numeros)
	{
		foreach ($numeros as $numero)
		{
			$ef->cargar_estado($numero);
			$validacion = $ef->validar_estado();
			$this->AssertTrue($validacion[0], "Fallo con $numero");
		}
	}

	function validar_incorrectos($ef, $numeros)
	{
		foreach ($numeros as $numero)
		{
			$ef->cargar_estado($numero);
			$validacion = $ef->validar_estado();
			$this->AssertFalse($validacion[0], "Fallo con $numero");
		}	
	}
	
	function test_validacion_intervalo_defecto()
	{
		$parametros = array(
		);
		$ef = $this->get_ef($parametros);
		
		$this->validar_correctos($ef, array(-1000, -0.2, 0, 0.132, 123, ''));
		$this->validar_incorrectos($ef, array('123hola123', '&77'));
	}
	
	function test_validacion_negativo_no_estricto()
	{
		$parametros = array(
			'rango' => '*..0, Menor que cero!'
		);
		$ef = $this->get_ef($parametros);
		$this->validar_correctos($ef, array(-1000, -0.2, 0, ''));
		$this->validar_incorrectos($ef, array(0.1, 123));
	}
	
	function test_validacion_negativo_estricto()
	{
		$parametros = array(
			'rango' => '*..0)'
		);
		$ef = $this->get_ef($parametros);
		$this->validar_correctos($ef, array(-1000, -0.2, ''));
		$this->validar_incorrectos($ef, array(0, 123));
	}
	
	function test_validacion_positivo_no_estricto()
	{
		$parametros = array(
			'rango' => '0..*'
		);
		$ef = $this->get_ef($parametros);
		$this->validar_correctos($ef, array(0, 0.1, 123, ''));
		$this->validar_incorrectos($ef, array(-1000, -0.2));
	}	

	function test_validacion_positivo_estricto()
	{
		$parametros = array(
			'rango' => '(0..*'
		);
		$ef = $this->get_ef($parametros);
		$this->validar_correctos($ef, array(0.1, 123, ''));
		$this->validar_incorrectos($ef, array(-1000, -0.2, 0));
	}
	
	function test_validacion_rango_no_estricto()
	{
		$parametros = array(
			'rango' => '[-10..10]'
		);
		$ef = $this->get_ef($parametros);
		$this->validar_correctos($ef, array(-10, -4, 0, 1, 5, 10, ''));
		$this->validar_incorrectos($ef, array(-11, 11));	
	}

	function test_validacion_rango_estricto()
	{
		$parametros = array(
			'rango' => '(-10.1..10.12)'
		);
		$ef = $this->get_ef($parametros);
		$this->validar_correctos($ef, array(-9.99999999,-4, 0, 1, 5,9.9999999,-10.0009, 10.11999, ''));
		$this->validar_incorrectos($ef, array(-10.1,-11, 11, 10.12));	
	}	

	
	function test_validacion_porcentaje()
	{
		$this->clase= 'ef_editable_numero_porcentaje';
		$ef = $this->get_ef(array());
		$this->validar_correctos( $ef, array(0, 1, 99.9, 100));
		$this->validar_incorrectos($ef, array(-10, 100.00001, 200));	
	}
	
	function test_validacion_rango_cambia_en_ejecucion()
	{
		//Definición estática
		$parametros = array(
			'rango' => '0..*'
		);
		$ef = $this->get_ef($parametros);
		$this->validar_correctos($ef, array(0, 0.1, 123));
		$this->validar_incorrectos($ef, array(-1000, -0.2));

		//Cambio en ejecución
		$ef->cambiar_rango('(*..0)');
		$this->validar_correctos($ef, array(-1000, -0.2));
		$this->validar_incorrectos($ef, array(0, 0.1, 123));
	}
}


?>