<?php
require_once("base_test_datos.php");
require_once('nucleo/persistencia/objeto_datos_tabla.php');

class base_test_datos_tabla extends base_test_datos
{
	protected $dt;

	function get_descripcion()
	{
		return "";
	}	

	function SetUp()
	{
		ejecutar_sql( $this->get_sql_juego_datos() );
		$this->dt = $this->get_dt();
	}

	function TearDown()
	{
		ejecutar_sql( $this->get_sql_eliminar_juego_datos() );
		$this->dt->resetear();
		unset($this->dt);
	}

	//----------------------------------------------
	//-- Mostrar Informacion
	//----------------------------------------------

	function dump($mensaje="Info")
	{
		$this->dt->info();
	}

	function dump_cambios($mensaje="Cambios")
	{
		ei_arbol($this->dt->get_cambios());
	}
	
	//-------------------------------------------------------------
	// Herramientas
	//-------------------------------------------------------------

	function test_info()
	{
		return;
		$this->dump();
		$this->dump_cambios();
	}

	function insertar_2_filas()
	{
		$this->dt->nueva_fila( $this->get_fila_test("valido_1") );
		$this->dt->nueva_fila( $this->get_fila_test("valido_2") );
	}
	
	function control_cambios($estado_esperado)
	//Se le pasa el estado esperado de la tabla de cambios, ordenados a partir de CERO	
	{
		$a=0;
		foreach( $this->dt->get_cambios() as $cambios)
		{
			$this->AssertEqual($cambios['estado'], $estado_esperado[$a] );
			$a++;
		}
	}

	function control_get_fila($cantidad_filas)
	{
		$filas = $this->dt->get_filas();
		$this->AssertEqual( count($filas), $cantidad_filas);
	}

	function sincronizar($cantidad_filas)
	{
		$resultado = $this->dt->sincronizar();
		$this->AssertEqual( $resultado, $cantidad_filas);
	}
	
	function cargar($where=null)
	{
		$ap = $this->dt->get_persistidor();
		$ap->cargar_db($where);
	}

	//#############################################################
	//#    PRUEBAS    
	//#############################################################

	//-------------------------------------------------------------
	//--- Primitvas BASICAS del DT
	//-------------------------------------------------------------

	function test_recuperar_claves()
	{
		$this->assertEqualArray( $this->dt->get_clave(), $this->get_clave_test() );
	}

	//-------------------------------------------------------------
	//--- Trabajo sobre FILAS
	//-------------------------------------------------------------

	function test_insertar()
	{
		// 1
		$id = $this->dt->nueva_fila( $this->get_fila_test("valido_1") );
		$this->AssertEqual($id, 0);
		$this->AssertTrue($this->dt->existe_fila(0));
		// 2
		$id = $this->dt->nueva_fila( $this->get_fila_test("valido_2") );
		$this->AssertEqual($id, 1);
		$this->AssertTrue($this->dt->existe_fila(1));
		//Control
		$this->AssertFalse($this->dt->existe_fila(2));
		$this->AssertEqual($this->dt->get_cantidad_filas(), 2);
		$this->control_cambios( array("i","i") );
	}
	
	function test_insertar_2_filas()
	{
		$this->insertar_2_filas();
		$this->control_cambios( array("i","i") );
		$this->control_get_fila(2);
	}

	function test_insertar_y_modificar()
	{
		$this->insertar_2_filas();
		$fila = $this->dt->get_fila(0);
		$this->dt->modificar_fila(0, $fila);
		$this->control_cambios( array("i","i") );
	}

	function test_insertar_y_eliminar()
	{
		$this->insertar_2_filas();
		$this->dt->eliminar_fila(0);
		$this->control_cambios( array("i") );
	}
	
	//-------------------------------------------------------------
	//--- Obtencion de datos
	//-------------------------------------------------------------

	function test_obtencion_datos()
	{
		$this->cargar();
		$this->control_cambios( array("db","db","db","db") );
		$this->control_get_fila(4);
	}

	function test_obtencion_datos_where()
	{
		$this->cargar( $this->get_where_test() );
		$this->control_cambios( array("db","db","db") );
		$this->control_get_fila(3);
	}

	//-------------------------------------------------------------
	//--- Sincronizacion de datos alterados
	//-------------------------------------------------------------

	function test_insertar_db()
	{
		$this->insertar_2_filas();
		$this->sincronizar(2);
		$this->control_cambios( array("db","db") );
	}

	function test_modificar_db()
	{
		$this->cargar();
		$this->dt->modificar_fila(0, $this->get_fila_test("valido_1") );
		$this->dt->modificar_fila(1, $this->get_fila_test("valido_2") );
		$this->control_cambios( array("u","u","db","db") );
		$this->sincronizar(2);
		$this->control_cambios( array("db","db","db","db") );
	}

	function test_eliminar_db()
	{
		$this->cargar();
		$this->dt->eliminar_fila(0);
		$this->dt->eliminar_fila(1);
		$this->control_cambios( array("d","d","db","db") );
		$this->sincronizar(2);
		$this->control_cambios( array("db","db","db","db") );
	}
	//-------------------------------------------------------------
}
?>