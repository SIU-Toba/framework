<?php
require_once("test_datos.php");
require_once('nucleo/persistencia/objeto_datos_tabla.php');

class test_datos_tabla extends test_datos
{
	//-------------------------------------------------------------
	// Herramientas
	//-------------------------------------------------------------

	function test_1()
	{
		return;
		$this->dump();
		$this->dump_cambios();
	}

	function insertar_2_filas()
	{
		$this->dt->nueva_fila( $this->get_fila("valido_1") );
		$this->dt->nueva_fila( $this->get_fila("valido_2") );
	}
	
	function control_cambios($cambios_esperados)
	//Se le pasa el estado esperado de la tabla de cambios, ordenados a partir de CERO	
	{
		$a=0;
		foreach( $this->dt->get_cambios() as $cambios)
		{
			$this->AssertEqual($cambios['estado'], $cambios_esperados[$a] );
			$a++;
		}
	}

	function control_get_fila($cantidad)
	{
		$filas = $this->dt->get_filas();
		$this->AssertEqual( count($filas), $cantidad);
	}

	//-------------------------------------------------------------
	//--- Primitvas BASICAS
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
		$id = $this->dt->nueva_fila( $this->get_fila("valido_1") );
		$this->AssertEqual($id, 0);
		$this->AssertTrue($this->dt->existe_fila(0));
		// 2
		$id = $this->dt->nueva_fila( $this->get_fila("valido_2") );
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
	}

	function test_insertar_y_obtener()
	{
		$this->insertar_2_filas();
		$filas = $this->dt->get_filas();
		$this->AssertEqual( count($filas), 2);
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
		$this->dt->cargar_datos();
		//$this->dump_cambios();
		$this->control_cambios( array("db","db","db","db") );
		$this->control_get_fila(4);
	}

	//-------------------------------------------------------------
	//--- Alteracion DATOS
	//-------------------------------------------------------------
	
}
?>