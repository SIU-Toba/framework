<?php
require_once("base_test_datos.php");

class test_dt_tabla_minima extends base_test_datos
{

	function SetUp()
	{
		ejecutar_fuente( $this->get_sql_juego_datos() );
		$this->dt = $this->get_dt();
	}

	function TearDown()
	{
		ejecutar_fuente( $this->get_sql_eliminar_juego_datos() );
		$this->dt->resetear();
		unset($this->dt);
	}

	function get_descripcion()
	{
		return "OBJETO datos_tabla (tabla mínima)";
	}	

	function get_dt()
	{
		$dt = toba_constructor::get_runtime(array('proyecto' => 'toba_testing', 'componente' => '1746'), 
											'toba_datos_tabla');
		$dt->inicializar();
		return $dt;
	}

	function get_sql_tablas()
	{
		$sql[] = "CREATE TABLE test_todas_claves (
						id					INT4 NOT NULL,
						CONSTRAINT test_todas_caves PRIMARY KEY(id)						
					);";
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_todas_claves;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test_todas_claves (id) VALUES (100);";
		$sql[] = "INSERT INTO test_todas_claves (id) VALUES (101);";
		$sql[] = "INSERT INTO test_todas_claves (id) VALUES (102);";
		$sql[] = "INSERT INTO test_todas_claves (id) VALUES (103);";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_todas_claves;";
		return $sql;
	}

	function get_fila_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['id']="10";
		$datos['valido_2']['id']="20";

		//- Registro invalido para la DB (El ID es un string)
		$datos['invalido_db']['id']="xxx";

		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito una FILA inexistente");
		}
	}
	
	function insertar_2_filas()
	{
		$this->dt->nueva_fila( $this->get_fila_test("valido_1") );
		$this->dt->nueva_fila( $this->get_fila_test("valido_2") );
	}
	
	function cargar($where=null)
	{
		$ap = $this->dt->persistidor();
		$ap->cargar_con_where($where);
	}
	//#############################################################
	//#    PRUEBAS    
	//#############################################################

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
		$this->control_cambios_dt( array("i","i") );
	}
	
	function test_insertar_2_filas()
	{
		$this->insertar_2_filas();
		$this->control_cambios_dt( array("i","i") );
		$this->AssertEqual( $this->dt->get_cantidad_filas(), 2);
	}

	function test_insertar_y_modificar()
	{
		$this->insertar_2_filas();
		$fila = $this->dt->get_fila(0);
		$this->dt->modificar_fila(0, $fila);
		$this->control_cambios_dt( array("i","i") );
	}

	function test_insertar_y_eliminar()
	{
		$this->insertar_2_filas();
		$this->dt->eliminar_fila(0);
		$this->control_cambios_dt( array("i") );
	}
	
	//-------------------------------------------------------------
	//--- Obtencion de datos
	//-------------------------------------------------------------

	function test_obtencion_datos()
	{
		$this->cargar();
		$this->control_cambios_dt( array("db","db","db","db") );
		$this->AssertEqual(  $this->dt->get_cantidad_filas(), 4);
	}

	function test_obtencion_datos_where()
	{
		$this->cargar("id IN (100,101,102)");
		$this->control_cambios_dt( array("db","db","db") );
		$this->AssertEqual(  $this->dt->get_cantidad_filas(), 3);
	}

	//-------------------------------------------------------------
	//--- Sincronizacion de datos alterados
	//-------------------------------------------------------------

	function test_insertar_db()
	{
		$this->insertar_2_filas();
		$this->AssertEqual($this->dt->sincronizar(), 2);
		$this->control_cambios_dt( array("db","db") );
	}

	function test_modificar_db()
	{
		$this->cargar();
		$this->dt->modificar_fila(0, $this->get_fila_test("valido_1") );
		$this->dt->modificar_fila(1, $this->get_fila_test("valido_2") );
		$this->control_cambios_dt( array("u","u","db","db") );
		$this->AssertEqual($this->dt->sincronizar(), 2);
		$this->control_cambios_dt( array("db","db","db","db") );
	}

	function test_eliminar_db()
	{
		$this->cargar();
		$this->dt->eliminar_fila(0);
		$this->dt->eliminar_fila(1);
		$this->control_cambios_dt( array("d","d","db","db") );
		$this->AssertEqual($this->dt->sincronizar(), 2);
		$this->control_cambios_dt( array("db","db","db","db") );
	}
	//-------------------------------------------------------------


}
?>