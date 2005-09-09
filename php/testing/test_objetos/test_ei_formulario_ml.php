<?php
require_once('nucleo/browser/clases/objeto_ei_formulario_ml.php');
require_once('nucleo/browser/clases/objeto_ci.php');

Mock::generate('objeto_ci');

class test_ei_formulario_ml extends test_toba
{

	function get_descripcion()
	{
		return "OBJETO ei_formulario_ml";
	}	

	function SetUp()
	{
		$this->mentir_hilo();
	}
	
	function TearDown()
	{
		$this->restaurar_hilo();
	}

	function crear_ml_para_analisis($observador, $generar_form, $datos, $metodo = null, $disparar_eventos = false)
	{
		$ml = new objeto_ei_formulario_ml(array('toba_testing','1322'));		//test ei_formulario_ml
		$ml->inicializar(array('nombre_formulario' => ''));
		$ml->agregar_controlador($observador);
		$ml->set_metodo_analisis($metodo);
		if ($disparar_eventos) {
			$ml->datos = $datos;
			$ml->disparar_eventos();
		} else {
			$ml->cargar_datos($datos);
		}
		if ($generar_form) {
			ob_start();
			$ml->generar_formulario();
			ob_clean();
		}
		$ml->definir_eventos();

		//Interacción con el usuario
		$_POST['ei_form1322'] = 'modificacion';		
		return $ml;
	}
	
	function crear_ml_para_seleccion($observador, $generar_form, $datos, $metodo = null, $parametros, $maneja_datos = true)
	{
		$ml = new objeto_ei_formulario_ml(array('toba_testing','1322'));		//test ei_formulario_ml
		$ml->inicializar(array('nombre_formulario' => ''));
		$ml->agregar_controlador($observador);
		$ml->set_metodo_analisis($metodo);
		$ml->cargar_datos($datos);
		if ($generar_form) {
			ob_start();
			$ml->generar_formulario();
			ob_clean();
		}
		$ml->definir_eventos();
		$ml->set_eventos( $ml->get_lista_eventos() + eventos::seleccion($maneja_datos));
		
		//Interacción con el usuario
		$_POST['ei_form1322'] = 'seleccion';
		$_POST['objeto_form_1322__parametros'] = $parametros;		
		return $ml;
	}	

	//-------------------------------------------------------------------------------
	//--------------------------------	ANALISIS de los datos ----------------------
	//-------------------------------------------------------------------------------	
	function sin_testsin_analisis()
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
		$ml = $this->crear_ml_para_analisis($observador, false, $finales, null, true);
		$ml->disparar_eventos();
		
		//Chequeos
		$observador->tally();
	}
	
	function sin_testanalisis_en_linea()
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
		$ml = $this->crear_ml_para_analisis($observador, false, $finales, 'LINEA', true);
		
		//Chequeos
		$observador->tally();		
	}	
	
	function sin_testanalisis_mediante_eventos()
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
		$ml = $this->crear_ml_para_analisis($observador, false, $finales, 'EVENTOS', true);
		
		//Chequeos
		$observador->tally();		
	}	

	//---------------------------------------------------------------------------------------------
	//------------------------ PRUEBA DE EVENTOS A NIVEL DE FILA ----------------------------------	
	//-------------------------------------------------------------------------------------------
	function sin_testevt_de_fila_modificando_sin_analisis()
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
/*		$esperados= array(
						array('lista' => 'c'),
						array('lista'=> 'b'),
						array('lista'=> 'd')
					);							*/
		$observador = new Mockobjeto_ci($this);
		//Se va a seleccionar el elemento 'd' que esta en el indice 1 del arreglo en la modificacion
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'seleccion', 1));

		$parametros_evt = '146';	//Se seleccione el elemento 'd'
		//Carga de estado inicial
		$ml = $this->crear_ml_para_seleccion($observador, true, $iniciales, null, $parametros_evt);
		$ml->destruir();
		//Retorno de la información
		$ml = $this->crear_ml_para_seleccion($observador, false, $finales, null, $parametros_evt, true);
		
		//Chequeos
		$observador->tally();	
	}
	
	function sin_testevt_de_fila_modificando_analisis_en_linea()
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
/*		$esperados = array(
						146 => array('lista'=> 'b', 	apex_ei_analisis_fila => 'M'),
						145 => array('lista'=> 'c',		apex_ei_analisis_fila => 'M'),
						155 => array('lista'=> 'd',		apex_ei_analisis_fila => 'A'),
						147 => array(					apex_ei_analisis_fila => 'B')
					);		*/
		$observador = new Mockobjeto_ci($this);
		//Se va a seleccionar el elemento 'd' que esta en el indice 155 del arreglo en la modificacion
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'seleccion', '155'));

		$parametros_evt = '155';	//Se seleccione el elemento 'd'
		//Carga de estado inicial
		$ml = $this->crear_ml_para_seleccion($observador, true, $iniciales, 'LINEA', $parametros_evt);
		$ml->destruir();
		//Retorno de la información
		$ml = $this->crear_ml_para_seleccion($observador, false, $finales, 'LINEA', $parametros_evt);
		$ml->disparar_eventos();
	
		//Chequeos
		$observador->tally();	
	}	
	
	function sin_testevt_de_fila_modificando_analisis_eventos()
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
		//Se va a seleccionar el elemento 'd' que esta en el indice 155 del arreglo en la modificacion
		$observador->expectArgumentsAt(4, 'registrar_evento', array(null, 'seleccion', '155'));		

		$parametros_evt = '155';	//Se seleccione el elemento 'd'
		//Carga de estado inicial
		$ml = $this->crear_ml_para_seleccion($observador, true, $iniciales, 'EVENTOS', $parametros_evt);
		$ml->destruir();
		//Retorno de la información
		$ml = $this->crear_ml_para_seleccion($observador, false, $finales, 'EVENTOS', $parametros_evt);
		$ml->disparar_eventos();		
		
		//Chequeos
		$observador->tally();		
	}

	function sin_testevt_de_fila_que_no_maneja_datos()
	{
		//Expectativas
		$iniciales = array(
						145 => array('lista'=> 'a'),
						146 => array('lista'=> 'b'),
						147 => array('lista'=> 'c')						
					 );
		//No le debería prestar atención a los finales ya que no se manejan datos
		$finales= array(
						234 => array('lista' => 'c'),
					);		
		$observador = new Mockobjeto_ci($this);
		//Se va a seleccionar el elemento 'd' que esta en el indice 146 del arreglo original
		$observador->expectOnce('registrar_evento', array(null, 'seleccion', '146'));

		$parametros_evt = '146';	//Se seleccione el elemento 'd'
		//Carga de estado inicial
		$ml = $this->crear_ml_para_seleccion($observador, true, $iniciales, null, $parametros_evt, false);
		$ml->destruir();
		//Retorno de la información
		$ml = $this->crear_ml_para_seleccion($observador, false, $finales, null, $parametros_evt, false);
		$ml->disparar_eventos();
		
		//Chequeos
		$observador->tally();	
	}
		
	
}


?>