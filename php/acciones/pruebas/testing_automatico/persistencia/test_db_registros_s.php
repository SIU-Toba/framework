<?php
require_once("test_db_registros.php");
/*
	FALTA
	-----

	- modificacion por acceso via columnas

	- reset
	
	- sincronizacion con la base
		- alta
		- baja
		- modificacion
	
	- columnas cosmeticas

	- Se llama a los eventos

	-- SACAR FACTOR COMUN de con las pruebas del MT!!!

	dbr_mt
	
		- relacion estricta
		- relacion debil
*/

class test_db_registros_s extends test_db_registros
{
	
	function __construct()
	{
		parent::__construct();
		$this->dbr_a_utilizar = "01_s";
	}

	//-------------------------------------------------------------
	//--- Recuperacion de datos
	//-------------------------------------------------------------

	function test_carga_registros()
	/*
		Carga completa de registros
	*/
	{
		$this->dbr->cargar_datos();
		$this->AssertEqual($this->dbr->cantidad_registros(), 4);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "db");
		$this->AssertEqual($control[1]['estado'], "db");
		$this->AssertEqual($control[2]['estado'], "db");
		$this->AssertEqual($control[3]['estado'], "db");
	}

	function test_carga_registros_where()
	/*
		Carga completa de registros con WHERE
	*/
	{
		$where[] = "id IN (0,1,2)";
		$this->dbr->cargar_datos($where);
		$this->AssertEqual($this->dbr->cantidad_registros(), 3);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "db");
		$this->AssertEqual($control[1]['estado'], "db");
		$this->AssertEqual($control[2]['estado'], "db");
	}

	function test_obtencion_datos()
	/*
		Obtener los registros cargados
	*/
	{
		$where[] = "id IN (0,1,2)";
		$this->dbr->cargar_datos($where);
		$datos = $this->dbr->obtener_registros();
		$this->AssertEqual( count($datos), 3);
	}

	function test_obtencion_datos_filtro()
	/*
		Obtener los registros cargados aplicando un FILTRO
	*/
	{
		$where[] = "id IN (0,1,2)";
		$this->dbr->cargar_datos($where);
		$condicion["id"] = "0";
		$this->AssertEqual($this->dbr->cantidad_registros(), 3);
		$datos = $this->dbr->obtener_registros($condicion);
		$this->AssertEqual(count($datos), 1);
	}

	//-------------------------------------------------------------
	//--- Modificacion de datos, sin sincro con la DB
	//-------------------------------------------------------------

	//**** INSERTAR ************************************************************

	function test_insertar_registros_dbr_vacio()
	/*
		Insertar registros
	*/
	{
		$this->dbr->agregar_registro( $this->get_tabla_registro(1,0) );
		$this->dbr->agregar_registro( $this->get_tabla_registro(1,1) );
		$this->AssertEqual($this->dbr->cantidad_registros(), 2);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "i");
		$this->AssertEqual($control[1]['estado'], "i");
	}

	function test_insertar_registros()
	/*
		Insertar registros
	*/
	{
		$this->dbr->cargar_datos();
		$this->dbr->agregar_registro( $this->get_tabla_registro(1,0) );
		$this->dbr->agregar_registro( $this->get_tabla_registro(1,1) );
		$this->AssertEqual($this->dbr->cantidad_registros(), 6);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "db");
		$this->AssertEqual($control[1]['estado'], "db");
		$this->AssertEqual($control[2]['estado'], "db");
		$this->AssertEqual($control[3]['estado'], "db");
		$this->AssertEqual($control[4]['estado'], "i");
		$this->AssertEqual($control[5]['estado'], "i");
	}

	function test_insertar_registro_campo_invalido_nulo()
	/*
		Insertar registros invalido: un campo not null es nulo
	*/
	{
		try{
			$this->dbr->agregar_registro( $this->get_tabla_registro(1,2) );
			$this->fail();
		}catch( excepcion_toba $e ){
			$this->pass();	
			$this->AssertEqual($this->dbr->cantidad_registros(), 0);
			$this->AssertEqual( count( $this->dbr->get_estructura_control() ), 0);
		}
	}

	function test_insertar_registro_campo_invalido_no_existente()
	/*
		Insertar registros invalido: un campo no existe en la definicion
	*/
	{
		try{
			$this->dbr->agregar_registro( $this->get_tabla_registro(1,3) );
			$this->fail();
		}catch( excepcion_toba $e ){
			$this->pass();	
			$this->AssertEqual($this->dbr->cantidad_registros(), 0);
			$this->AssertEqual( count( $this->dbr->get_estructura_control() ), 0);
		}
	}

	function test_insertar_registro_conservacion_datos()
	/*
		Control de la conservacion de los datos insertados
	*/
	{
		$datos = $this->get_tabla_registro(1,0);
		$this->dbr->agregar_registro( $datos );
		$datos_dbr = $this->dbr->obtener_registro( 0 );
		unset($datos_dbr[apex_db_registros_clave]);
		$this->assertEqualArray($datos, $datos_dbr);
	}

	//**** ELIMINAR ****************************************************+

	function test_eliminar_registros()
	/*
		Eliminar todos los registros
	*/
	{
		$this->dbr->cargar_datos();
		$this->dbr->eliminar_registros();		
		$this->AssertEqual($this->dbr->cantidad_registros(), 0);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "d");
		$this->AssertEqual($control[1]['estado'], "d");
		$this->AssertEqual($control[2]['estado'], "d");
		$this->AssertEqual($control[3]['estado'], "d");
	}

	function test_eliminar_registro_puntual()
	/*
		Eliminar registros puntuales
	*/
	{
		$this->dbr->cargar_datos();
		$this->dbr->eliminar_registro(0);		
		$this->dbr->eliminar_registro(1);
		$this->AssertEqual($this->dbr->cantidad_registros(), 2);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "d");
		$this->AssertEqual($control[1]['estado'], "d");
		$this->AssertEqual($control[2]['estado'], "db");
		$this->AssertEqual($control[3]['estado'], "db");
	}

	function test_eliminar_registro_inexistente()
	/*
		Eliminar un registro que no existe
	*/
	{
		try{
			$this->dbr->eliminar_registro( 0 );
			$this->fail();
		}catch( excepcion_toba $e ){
			$this->AssertEqual($this->dbr->cantidad_registros(), 0);
			$this->AssertEqual( count( $this->dbr->get_estructura_control() ), 0);
			$this->pass();	
		}
	}

	//**** MODIFICAR ************************************************************

	function test_modificar_registro()
	/*
		Modificar registros
	*/
	{
		$this->dbr->cargar_datos();
		$this->dbr->modificar_registro( $this->get_tabla_registro(1,0), 0);
		$this->dbr->modificar_registro( $this->get_tabla_registro(1,1), 1);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "u");
		$this->AssertEqual($control[1]['estado'], "u");
		$this->AssertEqual($control[2]['estado'], "db");
		$this->AssertEqual($control[3]['estado'], "db");
	}
	
	function test_modificar_registro_inexistente()
	/*
		Modificar un registro que no existe
	*/
	{
		try{
			$this->dbr->modificar_registro( $this->get_tabla_registro(1,3),0 );
			$this->fail();
		}catch( excepcion_toba $e ){
			$this->pass();	
			$this->AssertEqual($this->dbr->cantidad_registros(), 0);
			$this->AssertEqual( count( $this->dbr->get_estructura_control() ), 0);
		}
	}

	function test_modificar_registro_campo_invalido_nulo()
	/*
		Modificar registros con un campo un campo "not null" nulo
	*/
	{
		try{
			$this->dbr->cargar_datos();
			$this->dbr->modificar_registro( $this->get_tabla_registro(1,2), 0);
			$this->fail();
		}catch( excepcion_toba $e ){
			$this->pass();	
			$control = $this->dbr->get_estructura_control();
			$this->AssertEqual($control[0]['estado'], "db");
			$this->AssertEqual($control[1]['estado'], "db");
			$this->AssertEqual($control[2]['estado'], "db");
			$this->AssertEqual($control[3]['estado'], "db");
		}
	}

	function test_modificar_registro_campo_invalido_no_existente()
	/*
		Modificar registros con un campo un campo que no existe en la definicion
	*/
	{
		try{
			$this->dbr->cargar_datos();
			$this->dbr->modificar_registro( $this->get_tabla_registro(1,3), 0);
			$this->fail();
		}catch( excepcion_toba $e ){
			$this->pass();	
			$control = $this->dbr->get_estructura_control();
			$this->AssertEqual($control[0]['estado'], "db");
			$this->AssertEqual($control[1]['estado'], "db");
			$this->AssertEqual($control[2]['estado'], "db");
			$this->AssertEqual($control[3]['estado'], "db");
		}
	}

	function test_modificar_registro_conservacion_datos()
	/*
		Control de la conservacion de los datos insertados
	*/
	{
		$this->dbr->cargar_datos();
		$datos = $this->get_tabla_registro(1,0);
		$this->dbr->modificar_registro( $datos, 0 );
		$datos_dbr = $this->dbr->obtener_registro( 0 );
		unset($datos_dbr[apex_db_registros_clave]);
		$this->assertEqualArray($datos, $datos_dbr);
	}

	//-------------------------------------------------------------
	//--- Modificacion de datos con sincronizacion con la DB
	//-------------------------------------------------------------

	//**** INSERTAR ************************************************************

	function test_sync_insertar_registros()
	/*
		Insertar registros
	*/
	{
		$this->dbr->cargar_datos();
		$this->AssertEqual($this->dbr->cantidad_registros(), 4);
		$this->dbr->agregar_registro( $this->get_tabla_registro(1,0) );
		$this->dbr->agregar_registro( $this->get_tabla_registro(1,1) );
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[4]['estado'], "i");
		$this->AssertEqual($control[5]['estado'], "i");
		$diff = $this->dbr->sincronizar();
		$this->AssertEqual($diff, 2);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[4]['estado'], "db");
		$this->AssertEqual($control[5]['estado'], "db");
		//Compruebo que los nuevos registros existan en la DB
		$this->dbr->resetear();
		$this->dbr->cargar_datos();
		$this->AssertEqual($this->dbr->cantidad_registros(), 6);
	}

	function test_sync_insertar_registros_error_db()
	/*
		Insertar un registro que la base va a rechazar
	*/
	{
		try{
			$this->dbr->agregar_registro( $this->get_tabla_registro(1,4) );
			$control = $this->dbr->get_estructura_control();
			$this->AssertEqual($control[0]['estado'], "i");
			$diff = $this->dbr->sincronizar();
			$this->fail();
		}catch( excepcion_toba $e ){
			$this->pass();
			$control = $this->dbr->get_estructura_control();
			$this->AssertEqual($control[0]['estado'], "i");
		}
	}

	//**** ELIMINAR ****************************************************+
	//**** MODIFICAR ************************************************************

}
//ei_arbol($this->dbr->info(true),"db_registros");
?>