<?php

Mock::generate('toba_ci');

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

	//--------------------------------------------------------------------
	//--------------------------------	UTILITARIOS ----------------------
	//--------------------------------------------------------------------		
	
	/**
	*	Crea un formulario_ml y genera la interface para que se cree la sensacion que se fue hasta el cliente
	*/
	function crear_ml_para_analisis_gen_interface($datos)
	{
		$ml = toba_constructor::get_runtime(array('proyecto' => 'toba_testing', 'componente' => '1322'), 
											'toba_ei_formulario_ml');
		$ml->inicializar(array('nombre_formulario' => ''));
		$ml->definir_eventos();
		$ml->cargar_datos($datos);
		ob_start();
		$ml->generar_formulario();
		ob_clean();
		$ml->destruir();
	}
	
	/**
	*	Crea un formulario_ml, se asignan cosas al post haciendo cuenta que se vuelve del cliente
	*	Luego se disparan los eventos
	*/	
	function crear_ml_para_analisis_disparo_eventos($observador, $metodo, $datos, $evento='modificacion')
	{
		$ml = toba_constructor::get_runtime(array('proyecto' => 'toba_testing', 'componente' => '1322'), 
											'toba_ei_formulario_ml');
		$ml->inicializar(array('nombre_formulario' => ''));
		$ml->agregar_controlador($observador);
		$ml->set_metodo_analisis($metodo);
		$_POST['ei_form1322'] = $evento;
		$_POST['objeto_form_1322_listafilas'] = implode('_',array_keys($datos));
		//Seteo los efs
		foreach ($datos as $id => $valor) {
			$_POST["1322lista$id"] = $valor['lista'];
		}
		$ml->disparar_eventos();
	}	
	
	/**
	* 	Similar al anterior pero se dispara un evento seleccion e implicitamente
	*	tambien el evento modificacion
	*/
	function crear_ml_para_seleccion($observador, $metodo, $datos, $parametros, $evento='seleccion')
	{
		$ml = toba_constructor::get_runtime(array('proyecto' => 'toba_testing', 'componente' => '1322'), 
											'toba_ei_formulario_ml');
		$ml->inicializar(array('nombre_formulario' => ''));
		$ml->agregar_controlador($observador);
		$ml->set_metodo_analisis($metodo);
		$_POST['ei_form1322'] = $evento;
		$_POST['objeto_form_1322__parametros'] = "$parametros";		
		$_POST['objeto_form_1322_listafilas'] = implode('_',array_keys($datos));
		//Seteo los efs
		foreach ($datos as $id => $valor) {
			$_POST["1322lista$id"] = $valor['lista'];
		}
		$ml->disparar_eventos();
	}
	
	
	//-------------------------------------------------------------------------------
	//--------------------------------	ANALISIS de los datos ----------------------
	//-------------------------------------------------------------------------------		
	
	/**
	*	No se pide ningún análisis, por lo que debe retornar los datos puros
	*/
	function test_sin_analisis()
	{
		//Expectativas
		$iniciales = array(
						145 => array('lista'=> 'a'),
						146 => array('lista'=> 'b'),
						147 => array('lista'=> 'c')						
					 );
		$finales= array(
						145 => array('lista'=> 'c'),
						146 => array('lista'=> 'b'),
						155 => array('lista'=> 'd'),
						190 => array('lista'=> 'f')
					);		
		$esperados= array(
						array('lista'=> 'c'),
						array('lista'=> 'b'),
						array('lista'=> 'd'),
						array('lista'=> 'f')
					);							

		$observador = new Mocktoba_ci($this);
		$observador->expectOnce('registrar_evento', array(null, 'modificacion', $esperados));		
					
		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis_gen_interface($iniciales);

		//Carga del estado final		
		$ml = $this->crear_ml_para_analisis_disparo_eventos($observador, 'NO', $finales);
		
		//Chequeos
		$observador->tally();
	}	
	
	/**
	*	Se pide un analisis en linea con los registros, cada uno debe decir que accion sufrio
	*/
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

		$observador = new Mocktoba_ci($this);
		$observador->expectOnce('registrar_evento', array(null, 'modificacion', $esperados));		
					
		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis_gen_interface($iniciales);

		//Carga del estado final		
		$ml = $this->crear_ml_para_analisis_disparo_eventos($observador, 'LINEA', $finales);
		
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

		$observador = new Mocktoba_ci($this);
		$observador->expectArgumentsAt(0, 'registrar_evento', array(null, 'registro_modificacion', array('lista'=>'b'), 146));
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'registro_alta', array('lista'=>'d'), 155));
		$observador->expectArgumentsAt(2, 'registrar_evento', array(null, 'registro_modificacion', array('lista'=>'c'), 145));
		$observador->expectArgumentsAt(3, 'registrar_evento', array(null, 'registro_baja', 147));
		$observador->expectCallCount('registrar_evento', 4);

		
		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis_gen_interface($iniciales);
		//Carga del estado final
		$ml = $this->crear_ml_para_analisis_disparo_eventos($observador, 'EVENTOS', $finales);
		
		//Chequeos
		$observador->tally();		
	}		
	
	//---------------------------------------------------------------------------------------------
	//------------------------ PRUEBA DE EVENTOS A NIVEL DE FILA ----------------------------------	
	//-------------------------------------------------------------------------------------------
	
	/**
	*	Se busca que se dispare un evento a nivel de filas y tambien la modificacion sin analisis
	*/
	function test_evt_de_fila_modificando_sin_analisis()
	{
		//Expectativas
		$iniciales = array(
						145 => array('lista'=> 'a'),
						146 => array('lista'=> 'b'),
						147 => array('lista'=> 'c')						
					 );
		$finales= array(
						145 => array('lista'=> 'c'),
						146 => array('lista'=> 'b'),
						155 => array('lista'=> 'd')
					);		
		$esperados= array(
						array('lista'=> 'c'),
						array('lista'=> 'b'),
						array('lista'=> 'd')
					);
		$observador = new Mocktoba_ci($this);

		//Se va a seleccionar el elemento 'd' que esta en el indice 1 del arreglo en la modificacion
		$observador->expectArgumentsAt(0, 'registrar_evento', array(null, 'modificacion', $esperados));
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'seleccion', 1));
		$observador->expectCallCount('registrar_evento', 2);
		
		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis_gen_interface($iniciales);
		
		//Retorno de la información
		//Se seleccione el elemento 'd'
		$ml = $this->crear_ml_para_seleccion($observador, 'NO', $finales, '146');
		
		//Chequeos
		$observador->tally();
	}

	/**
	*	Se busca que se dispare un evento a nivel de filas y tambien la modificacion con analisis en linea
	*/
	function test__evt_de_fila_modificando_analisis_en_linea()
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
					
		$observador = new Mocktoba_ci($this);
		$observador->expectArgumentsAt(0, 'registrar_evento', array(null, 'modificacion', $esperados));
		//Se va a seleccionar el elemento 'd' que esta en el indice 155 del arreglo en la modificacion
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'seleccion', '155'));
		$observador->expectCallCount('registrar_evento', 2);
		
		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis_gen_interface($iniciales);
		
		//Retorno de la información
		//Se seleccione el elemento 'd'
		$ml = $this->crear_ml_para_seleccion($observador, 'LINEA', $finales, '155');
	
		//Chequeos
		$observador->tally();		
	}	
	
	/**
	*	Se busca que se dispare un evento a nivel de filas y tambien la modificacion con analisis por eventos
	*/	
	function test_evt_de_fila_modificando_analisis_eventos()
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

		$observador = new Mocktoba_ci($this);
		$observador->expectArgumentsAt(0, 'registrar_evento', array(null, 'registro_modificacion', array('lista'=>'b'), 146));
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'registro_alta', array('lista'=>'d'), 155));
		$observador->expectArgumentsAt(2, 'registrar_evento', array(null, 'registro_modificacion', array('lista'=>'c'), 145));
		$observador->expectArgumentsAt(3, 'registrar_evento', array(null, 'registro_baja', 147));		
		//Se va a seleccionar el elemento 'd' que esta en el indice 155 del arreglo en la modificacion
		$observador->expectArgumentsAt(4, 'registrar_evento', array(null, 'seleccion', '155'));		
		$observador->expectCallCount('registrar_evento', 5);
		
		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis_gen_interface($iniciales);
		
		//Retorno de la información
		//Se seleccione el elemento 'd'
		$ml = $this->crear_ml_para_seleccion($observador, 'EVENTOS', $finales, '155');
	
		//Chequeos
		$observador->tally();		
	}
	

	/**
	*	Se dispara un evento a nivel de filas pero que no maneja datos
	*	Por lo que el modificar no se tiene que disparar
	*/
	function test_evt_de_fila_que_no_maneja_datos()
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

		$observador = new Mocktoba_ci($this);

		//Se va a seleccionar el elemento 'd' que esta en el indice 1 del arreglo en la modificacion
		$observador->expectArgumentsAt(0, 'registrar_evento', array(null, 'seleccion_sin_datos', '146'));
		$observador->expectCallCount('registrar_evento', 1);
		
		//Carga de estado inicial
		$ml = $this->crear_ml_para_analisis_gen_interface($iniciales, false);
		
		//Retorno de la información
		//Se seleccione el elemento 'd'
		$ml = $this->crear_ml_para_seleccion($observador, 'NO', $finales, '146', 'seleccion_sin_datos');
		
		//Chequeos
		$observador->tally();		
	}

	//-------------------------------------------------------------------------------------------
	//------------------------ PRUEBA DE AGREGADO EN EL SERVER ----------------------------------	
	//-------------------------------------------------------------------------------------------
	
	/**
	*	Desde el cliente viene un conjunto de datos y un pedido de crear una nueva fila
	*	El ML debe procesar las modificaciones de los datos y pedirle al CI el esqueleto de la nueva fila
	*	(o simplemente la confirmacion)
	*/
	function test_agregado_en_server_con_modificacion_implicita()
	{
		//Expectativas
		$iniciales = array(
						145 => array('lista'=> 'a'),
						146 => array('lista'=> 'b'),
						147 => array('lista'=> 'c')						
					 );
		$finales= array(
						145 => array('lista'=> 'c'),
						146 => array('lista'=> 'b'),
						155 => array('lista'=> 'd')
					);		
		$esperados= array(
						array('lista'=> 'c'),
						array('lista'=> 'b'),
						array('lista'=> 'd')
					);
						
		$observador = new Mocktoba_ci($this);

		//Se genera la pantalla
		$this->crear_ml_para_analisis_gen_interface($iniciales);
		
		//Se va a seleccionar el elemento 'd' que esta en el indice 1 del arreglo en la modificacion
		$observador->expectArgumentsAt(0, 'registrar_evento', array(null, 'modificacion', $esperados));
		$observador->expectArgumentsAt(1, 'registrar_evento', array(null, 'pedido_registro_nuevo', null));
		$observador->expectCallCount('registrar_evento', 2);		
		
		$this->crear_ml_para_analisis_disparo_eventos($observador, "NO", $finales, "pedido_registro_nuevo");
	
		//Chequeos
		$observador->tally();		
	}
	
	
}


?>