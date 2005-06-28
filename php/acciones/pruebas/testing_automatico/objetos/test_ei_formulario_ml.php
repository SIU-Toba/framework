<?php
require_once('nucleo/browser/clases/objeto_ei_formulario_ml.php');
require_once('nucleo/browser/clases/objeto_ci.php');

Mock::generate('objeto_ci');

class test_ei_formulario_ml extends test_toba
{

	function SetUp()
	{
		$this->mentir_hilo();
	}
	
	function TearDown()
	{
		$this->restaurar_hilo();
	}

	//-------------------------------------------------------------------------------
	//--------------------------------	RELACION CLIENTE-SERVIDOR -------------------
	//-------------------------------------------------------------------------------
	function crear_ml_para_analisis($observador, $generar_form, $datos, $metodo = null)
	{
		$ml = new objeto_ei_formulario_ml(array('toba_testing','1320'));		//test ei_formulario_ml
		$ml->inicializar(array('nombre_formulario' => ''));
		$ml->agregar_observador($observador);
		$ml->set_metodo_analisis($metodo);
		$ml->cargar_datos($datos);
		if ($generar_form) {
			ob_start();
			$ml->generar_formulario();
			ob_clean();
		}
		$ml->definir_eventos();

		//Interacción con el usuario
		$_POST['ei_form1320'] = 'modificacion';		
		return $ml;
	}
	
	function test_sin_analisis()
	{
		//Expectativas
		$iniciales = array(
						145 => array('lista'=> 'a'),
						146 => array('lista'=> 'b'),
						147 => array('lista'=> 'c')						
					 );
		$finales= array(
						145 => array('lista' => 'c'),
						146 => array('lista'=> 'b'),
						155 => array('lista'=> 'd')
					);		
		$esperados= array(
						array('lista' => 'c'),
						array('lista'=> 'b'),
						array('lista'=> 'd')
					);							
		$observador = new Mockobjeto_ci($this);
		$observador->expectOnce('registrar_evento', array(null, 'modificacion', $esperados));

		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis($observador, true, $iniciales, null);
		$ml->destruir();
		//Retorno de la información
		$ml = $this->crear_ml_para_analisis($observador, false, $finales, null);
		$ml->disparar_eventos();
		
		//Chequeos
		$observador->tally();
	}
	
	function test_analisis_en_linea()
	{
		//Expectativas
		$iniciales = array(
						145 => array('lista'=> 'a'),
						146 => array('lista'=> 'b'),
						147 => array('lista'=> 'c')						
					 );
		$finales= array(
						146 => array('lista'=> 'b'),
						145 => array('lista'=> 'c'),
						155 => array('lista'=> 'd')
					);							 
		$esperados = array(
						146 => array('lista'=> 'b', 	apex_ei_analisis_fila => 'M'),
						145 => array('lista'=> 'c',		apex_ei_analisis_fila => 'M'),
						155 => array('lista'=> 'd',		apex_ei_analisis_fila => 'A'),
						147 => array(					apex_ei_analisis_fila => 'B')
					);		
		$observador = new Mockobjeto_ci($this);
		$observador->expectOnce('registrar_evento', array(null, 'modificacion', $esperados));

		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis($observador, true, $iniciales, 'LINEA');
		$ml->destruir();

		//Retorno de la información
		$ml = $this->crear_ml_para_analisis($observador, false, $finales, 'LINEA');
		$ml->disparar_eventos();
		
		//Chequeos
		$observador->tally();		
	}	
	
	function test_analisis_mediante_eventos()
	{
		//Expectativas
		$iniciales = array(
						145 => array('lista'=> 'a'),
						146 => array('lista'=> 'b'),
						147 => array('lista'=> 'c')						
					 );
		$finales= array(
						146 => array('lista'=> 'b'),
						155 => array('lista'=> 'd'),
						145 => array('lista'=> 'c')
					);							 

		$observador = new Mockobjeto_ci($this);
		$observador->expectArgumentsAt(0, 'registrar_evento', array(null, 'registro_modificacion', array('lista'=>'b'), 146));
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'registro_alta', array('lista'=>'d'), 155));
		$observador->expectArgumentsAt(2, 'registrar_evento', array(null, 'registro_modificacion', array('lista'=>'c'), 145));
		$observador->expectArgumentsAt(3, 'registrar_evento', array(null, 'registro_baja', 147));
		$observador->expectCallCount('registrar_evento', 4);

		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis($observador, true, $iniciales, 'EVENTOS');
		$ml->destruir();

		//Retorno de la información
		$ml = $this->crear_ml_para_analisis($observador, false, $finales, 'EVENTOS');
		$ml->disparar_eventos();
		
		//Chequeos
		$observador->tally();		
	}	
	
}


?>