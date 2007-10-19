<?php
//require_once('objetos_toba/asignador_objetos.php');

class test_asignador_objetos extends test_toba
{
	protected $objeto_creado = array('objeto' => '1606', 'proyecto' => 'toba_testing');
	
	function get_descripcion()
	{
		return "Asignador de objetos a otros objetos/items";	
	}
	
	function SetUp()
	{
		abrir_transaccion();
	}
	
	function TearDown()
	{
		abortar_transaccion();
	}
	
	/**
	*	Se le asigna un objeto a un item
	*/
	function test_asignar_a_item()
	{
		//Setup
		$item_destino = array('tipo' => 'toba_item', 'objeto' =>'1240', 'proyecto' => 'toba_testing');
		
		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $item_destino);
		$asignador->asignar();
		
		//Validacion
		$item = toba_constructor::get_info( array('componente'=>'1240','proyecto'=>'toba_testing'), 'item');
		$this->assertEqual(count($item->get_hijos()), 1);
	}
	
	/**
	*	Se le asigna un objeto a un ci
	*/	
	function test_asignar_a_ci()
	{
		$obj = toba_constructor::get_info(array('proyecto' => 'toba_testing', 'componente' => '1605'), 
											'toba_ci', true, null, true);		
		//Setup
		$ci_destino = array('tipo' => 'toba_ci',
							'objeto' =>'1605', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci');

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $ci_destino);
		$asignador->asignar();
		
		//Validacion
		$obj = toba_constructor::get_info(array('proyecto' => 'toba_testing', 'componente' => '1605'), 
											'toba_ci', true, null, true);		
		$hijos = $obj->get_hijos();
		$this->assertEqual(count($hijos), 3);
		$this->assertEqual($hijos[2]->get_id(), '1606');
		
	}
	
	/**
	*	Se le asigna un objeto a una pantalla de un ci que no tenia objetos
	*/		
	function test_asignar_a_pantalla_ci_sin_objetos_previos()
	{
		$obj = toba_constructor::get_info(array('proyecto' => 'toba_testing', 'componente' => '1605'), 
											'toba_ci');			
		//Setup
		$ci_destino = array('tipo' => 'toba_ci_pantalla',
							'objeto' =>'1605', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci',
							'pantalla' => '970');

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $ci_destino);
		$asignador->asignar();

		//Validacion
		$obj = toba_constructor::get_info(array('proyecto' => 'toba_testing', 'componente' => '1605'), 
											'toba_ci');			
		$hijos = $obj->get_hijos();
		$this->assertEqual(count($obj->get_hijos()), 2);		//Tiene dos pantallas
		$pantalla = $hijos[1];
		$this->assertEqual(count($pantalla->get_hijos()), 1);
		
	}

	/**
	*	Se le asigna un objeto a una pantalla de un ci que ya tenia un objeto
	*/			
	function test_asignar_a_pantalla_ci_con_objetos_previos()
	{	
		//Setup, se le asigna a una pantalla que ya tiene un objeto
		$ci_destino = array('tipo' => 'toba_ci_pantalla',
							'objeto' =>'1605', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci',
							'pantalla' => '960');

		//Test
		$asignador = new asignador_objetos( $this->objeto_creado, $ci_destino);
		$asignador->asignar();

		//Validacion
		$obj = toba_constructor::get_info(array('proyecto' => 'toba_testing', 'componente' => '1605'), 
											'toba_ci');
		$hijos = $obj->get_hijos();
		$this->assertEqual(count($obj->get_hijos()), 2);		//Tiene dos pantallas
		$pantalla = $hijos[0];
		$this->assertEqual($pantalla->get_id(), 'pantalla1');
		$this->assertEqual(count($pantalla->get_hijos()), 2);		
	}
}


?>
