<?php
require_once("test_db_registros.php");
/*
	FALTA
	-----

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
	//--- Definicion
	//-------------------------------------------------------------

	function test_recuperar_claves()
	{
		$this->assertEqualArray( $this->dbr->get_clave(), array("id") );
	}

	function test_recuperar_tope_registros()
	{
		$this->assertEqual( $this->dbr->get_tope_registros(), 0 );
	}
	
	function test_recuperar_clave_valor()
	{
		$this->dbr->cargar_datos_clave(0);
		$this->assertEqual( $this->dbr->get_clave_valor(0), array("id"=>0) );
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

	function test_carga_registros_clave()
	/*
		Carga completa de registros con WHERE
	*/
	{
		$this->dbr->cargar_datos_clave(1);
		$this->AssertEqual($this->dbr->cantidad_registros(), 1);
		$registro = $this->dbr->obtener_registro(0);		
		$this->AssertEqual($registro['nombre'], "Naranjas");
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "db");
	}

	function test_obtencion_registros()
	/*
		Obtener los registros cargados
	*/
	{
		$where[] = "id IN (0,1,2)";
		$this->dbr->cargar_datos($where);
		$datos = $this->dbr->obtener_registros();
		$this->AssertEqual( count($datos), 3);
		//Vino la columna que indica el ID interno
		for($a=0;$a<count($datos);$a++){
			$this->AssertTrue( isset($datos[$a][apex_db_registros_clave] ) );
		}
	}

	function test_obtencion_registro()
	/*
		Obtener un registro
	*/
	{
		$where[] = "id IN (0,1,2)";
		$this->dbr->cargar_datos($where);
		$datos = $this->dbr->obtener_registro(0);
		$this->AssertTrue( is_array($datos) );
		$this->AssertTrue( isset($datos[apex_db_registros_clave]) );
	}

	function test_obtencion_registro_valor()
	/*
		Obtener un registro
	*/
	{
		$this->dbr->cargar_datos_clave(1);
		$this->AssertEqual($this->dbr->obtener_registro_valor(0,"nombre"), "Naranjas");
	}

	function test_obtencion_registro_inexistente()
	/*
		Obtener un registro
	*/
	{
		$where[] = "id IN (0,1,2)";
		$this->dbr->cargar_datos($where);
		try{
			$datos = $this->dbr->obtener_registro(4);
			$this->fail();
		}catch(excepcion_toba $e){
			$this->pass();		
		}
	}

	function test_obtencion_registro_id_interno()
	/*
		Obtener los registros cargados
	*/
	{
		$where[] = "id IN (0,1,2)";
		$this->dbr->cargar_datos($where);
		$datos = $this->dbr->obtener_registros(null, true);
		$this->AssertEqual( count($datos), 3);
		$control = $this->dbr->get_estructura_control();
		foreach(array_keys($datos) as $id){
			if(!isset($control[$id])){
				$this->fail();	
			}
		}
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

	function test_modificar_registro_valor_puntual()
	{
		$this->dbr->cargar_datos();
		$this->dbr->establecer_registro_valor( 0, "nombre", "pizza" );
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "u");
		$registro = $this->dbr->obtener_registros(array("nombre"=>"pizza"));
		$this->AssertEqual( count($registro), 1);
	}
	
	function test_modificar_columnas()
	{
		$this->dbr->cargar_datos();
		$this->dbr->establecer_valor_columna("nombre", "pizza");
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "u");
		$this->AssertEqual($control[1]['estado'], "u");
		$this->AssertEqual($control[2]['estado'], "u");
		$this->AssertEqual($control[3]['estado'], "u");
		$registro = $this->dbr->obtener_registros(array("nombre"=>"pizza"));
		$this->AssertEqual( count($registro), 4);
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