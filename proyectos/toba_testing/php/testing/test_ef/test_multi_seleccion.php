<?php

class test_multi_seleccion extends test_toba
/*
	Ejercita los siguientes contratos:
	- El ef se debe cargar desde DAOS, desde SQL o manualmente desde la acción
	- Comunicación con el formulario
	- Validación de maximos y minimos
	- El conjunto seleccionado debe estar incluido en los de entrada
*/
{
	protected $parametros;
	protected $id_form = 'nombre_formnombre_padreid';
	
	function get_descripcion()
	{
		return "EF Multi-selección";
	}	
	
	function get_ef($parametros)
	{
		$padre = array(1=> 'nombre_padre');
		$ef = new ef_multi_seleccion($padre, 'nombre_form', 'id', 'etiqueta', 'descripcion', 'dato', false, $parametros);
		return $ef;	
	}
	
	function test_carga_desde_dao()
	{
		global $retorno_dao;
		$parametros = array(
						'cant_minima' => 2,
						'cant_maxima' => 4,
						'dao' => 'dao_multi_seleccion',
						'include' => 'testing/test_ef/prueba_daos.php',
						'clase' => 'prueba_daos'
					  );	
		
		//Conjunto vacio de datos de entrada
		$retorno_dao = array();
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos();
		$this->assertEqualArray($ef->get_valores(), $retorno_dao);
		
		//Conjunto completo de datos de entrada
		$retorno_dao = array(array('a', 'AAAAaa'), array('0', '010101'), array('otro', 'B'));
		$buscado = array('a' => 'AAAAaa', '0' => '010101', 'otro' => 'B');
		$ef = $this->get_ef($parametros);
		$this->assertEqualArray($ef->get_valores(), $buscado);
	}
/*	
	function test_cargar_desde_sql()
	{
		$parametros = array(
						'sql' => 'SELECT algo FROM tabla',
						'fuente' => 'mifuente'
					  );	
		
		//Conjunto vacio de datos de entrada
		$retorno = array();
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos();
		$this->assertEqualArray($ef->get_valores(), $retorno);
		
		//Conjunto completo de datos de entrada
		$retorno = array('a' => 'AAAAaa', '0' => '010101', 'otro' => 'B');
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos();
		$this->assertEqualArray($ef->get_valores(), $retorno);
			
	}
*/
	function test_lectura_desde_form()
	{
		//Conjunto vacio de datos de entrada y seleccionados
		$parametros = array();
		$entrada = array();
		$buscado = array();
		$_POST[$this->id_form] = $buscado;
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$this->AssertTrue($ef->activado());
		$this->AssertEqual($ef->obtener_estado(), $buscado);
		
		//Conjunto vacio de seleccionados
		$entrada = array('a' => 'A', 'b' => 'B');
		$buscado = array();
		$_POST[$this->id_form] = $buscado;
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$this->AssertTrue($ef->activado());
		$this->AssertEqual($ef->obtener_estado(), $buscado);		
		
		//Conjunto completo de seleccionados
		$entrada = array('a' => 'A', 'b' => 'B');
		$buscado = array('a', 'b');
		$_POST[$this->id_form] = $buscado;
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$this->AssertTrue($ef->activado());
		$this->AssertEqual($ef->obtener_estado(), $buscado);			
		
		//Subconjunto de seleccionados
		$entrada = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D');
		$buscado = array('a', 'd');
		$_POST[$this->id_form] = $buscado;
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$this->AssertTrue($ef->activado());
		$this->AssertEqual($ef->obtener_estado(), $buscado);				
	}
	
	function test_validacion_servidor_maximos_y_minimos()
	{
		$entrada = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D');
		$parametros = array(
			'cant_maxima' => '3', 'cant_minima' => '1'
		);

		// 0 Seleccionados - Falla por cant. minima
		$_POST[$this->id_form] = array();
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$validacion = $ef->validar_estado();
		$this->AssertFalse($validacion[0]);
		
		// 2 Seleccionados - Pasa
		$_POST[$this->id_form] = array('b', 'c');
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$validacion = $ef->validar_estado();
		$this->AssertTrue($validacion[0]);
		
		// 4 Seleccionados - Falla por cant. maxima
		$_POST[$this->id_form] = $entrada;
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$validacion = $ef->validar_estado();
		$this->AssertFalse($validacion[0]);				
	}
	
	function test_validacion_servidor_conj_seleccionado_incluido_conj_entrada()
	/*
		Valida que el conjunto de elementos seleccionados sea un subconjunto de los seleccionables
	*/ 
	{
		$parametros = array();
		$entrada = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D');
		$seleccionados = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'Este está de más!!');

		$_POST[$this->id_form] = $seleccionados;
		$ef = $this->get_ef($parametros);
		$ef->cargar_datos($entrada);
		$ef->cargar_estado();
		$validacion = $ef->validar_estado();
		$this->AssertFalse($validacion[0]);	
	}
}


?>
