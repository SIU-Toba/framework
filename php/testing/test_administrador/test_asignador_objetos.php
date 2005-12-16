<?php
require_once('admin/objetos_toba/asignador_objetos.php');

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
		$item_destino = array('tipo' => 'item', 'objeto' =>'1240', 'proyecto' => 'toba_testing');
		

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $item_destino);
		$asignador->asignar();
		
		//Validacion
		$item = constructor_toba::get_info( array('componente'=>'1240','proyecto'=>'toba_testing')), 'item')
		$this->assertEqual(count($item->hijos()), 1);
	}
	
	/**
	*	Se le asigna un objeto a un ci
	*/	
	function test_asignar_a_ci()
	{
		//Setup
		$ci_destino = array('tipo' => 'ci',
							'objeto' =>'1605', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci');

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $ci_destino);
		$asignador->asignar();
		
		//Validacion
		$obj = new elemento_objeto_ci();
		$obj->cargar_db('toba_testing', '1605');
		$hijos = $obj->hijos();
		$this->assertEqual($hijos[2]->id(), '1606');
		
	}
	
	/**
	*	Se le asigna un objeto a una pantalla de un ci que no tenia objetos
	*/		
	function test_asignar_a_pantalla_ci_sin_objetos_previos()
	{
		//Setup
		$ci_destino = array('tipo' => 'ci_pantalla',
							'objeto' =>'1605', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci',
							'pantalla' => '470');

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $ci_destino);
		$asignador->asignar();

		//Validacion
		$obj = new elemento_objeto_ci();
		$obj->cargar_db('toba_testing', '1605');
		$hijos = $obj->hijos();
		$this->assertEqual(count($obj->hijos()), 2);		//Tiene dos pantallas
		$pantalla = $hijos[0];
		$this->assertEqual(count($pantalla->hijos()), 1);
		
	}

	/**
	*	Se le asigna un objeto a una pantalla de un ci que ya tenia un objeto
	*/			
	function test_asignar_a_pantalla_ci_con_objetos_previos()
	{	
		//Setup, se le asigna a una pantalla que ya tiene un objeto
		$ci_destino = array('tipo' => 'ci_pantalla',
							'objeto' =>'1605', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci',
							'pantalla' => '471');

		//Test
		$asignador = new asignador_objetos( $this->objeto_creado, $ci_destino);
		$asignador->asignar();

		//Validacion
		$obj = new elemento_objeto_ci();
		$obj->cargar_db('toba_testing', '1605');
		$hijos = $obj->hijos();
		$this->assertEqual(count($obj->hijos()), 2);		//Tiene dos pantallas
		$pantalla = $hijos[1];
		$this->assertEqual($pantalla->id(), 'un_objeto');
		$this->assertEqual(count($pantalla->hijos()), 2);		
	}
}


?>
