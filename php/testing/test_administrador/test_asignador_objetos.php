<?php
require_once('admin/editores/asignador_objetos.php');
require_once('api/elemento_item.php');

class test_asignador_objetos extends test_toba
{
	protected $objeto_creado = array('id' => '1563', 'proyecto' => 'toba_testing');
	
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
		$item_destino = array('tipo' => 'item', 'id' =>'1240', 'proyecto' => 'toba_testing');
		

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $item_destino);
		$asignador->asignar();
		
		//Validacion
		$item = new elemento_item();
		$item->cargar_db('toba_testing', '1240');
		$this->assertEqual(count($item->hijos()), 1);
	}
	
	/**
	*	Se le asigna un objeto a un ci
	*/	
	function test_asignar_a_ci()
	{
		//Setup
		$ci_destino = array('tipo' => 'ci',
							'id' =>'1564', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci');

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $ci_destino);
		$asignador->asignar();
		
		//Validacion
		$obj = new elemento_objeto_ci();
		$obj->cargar_db('toba_testing', '1564');
		$hijos = $obj->hijos();
		$this->assertEqual($hijos[2]->id(), '1563');
		
	}
	
	/**
	*	Se le asigna un objeto a una pantalla de un ci que no tenia objetos
	*/		
	function test_asignar_a_pantalla_ci_sin_objetos_previos()
	{
		//Setup
		$ci_destino = array('tipo' => 'ci_pantalla',
							'id' =>'1564', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci',
							'pantalla' => '467');

		//Test
		$asignador = new asignador_objetos($this->objeto_creado, $ci_destino);
		$asignador->asignar();

		//Validacion
		$obj = new elemento_objeto_ci();
		$obj->cargar_db('toba_testing', '1564');
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
							'id' =>'1564', 
							'proyecto' => 'toba_testing',
							'id_dependencia' => 'el_ci',
							'pantalla' => '468');

		//Test
		$asignador = new asignador_objetos( $this->objeto_creado, $ci_destino);
		$asignador->asignar();

		//Validacion
		$obj = new elemento_objeto_ci();
		$obj->cargar_db('toba_testing', '1564');
		$hijos = $obj->hijos();
		$this->assertEqual(count($obj->hijos()), 2);		//Tiene dos pantallas
		$pantalla = $hijos[1];
		$this->assertEqual($pantalla->id(), 'un_objeto');
		$this->assertEqual(count($pantalla->hijos()), 2);		
	}
}


?>
