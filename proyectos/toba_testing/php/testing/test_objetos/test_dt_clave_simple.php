<?php
require_once("base_test_datos.php");

class test_dt_clave_simple extends base_test_datos
{
	protected $dt;
	
	function get_descripcion()
	{
		return "OBJETO datos_tabla (clave simple)";
	}	

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
	
	function cargar($where=null)
	{
		$ap = $this->dt->persistidor();
		$ap->cargar_con_where($where);
	}
	
	function get_dt()
	{
		$dt = toba_constructor::get_runtime(array('proyecto' => 'toba_testing', 'componente' => '1427'), 
											'toba_datos_tabla');
		$dt->inicializar();		
		return $dt;
	}

	function get_sql_tablas()
	{
		$sql[] = "CREATE TEMPORARY TABLE test_1 (
					  id 				SMALLINT 		NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_1_pkey PRIMARY KEY(id)
					);";
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_1;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('0','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('1','Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('3','Manzanas','Las manzanas son ricas.');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_1;";
		return $sql;
	}

	function get_fila_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['id']="10";
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";

		$datos['valido_2']['id']="20";
		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";

		//- Registro invalido (nombre NULL)
		$datos['invalido_null']['id']="450";
		$datos['invalido_null']['descripcion']="Este es un Perro";
		//$datos['invalido_null']['nombre']="Hola";

		//- Registro invalido (Estructua incorrecta)
		$datos['invalido_col_inexistente']['id']="220";
		$datos['invalido_col_inexistente']['nombre']="Hola";
		$datos['invalido_col_inexistente']['descripcion']="Este es un Perro";
		$datos['invalido_col_inexistente']['columna_invalida']="Todo mal";

		//- Registro invalido para la DB (El ID es un string)
		$datos['invalido_db']['id']="xxx";
		$datos['invalido_db']['nombre']="Hola";
		$datos['invalido_db']['descripcion']="Este es un Perro";

		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito una FILA inexistente");
		}
	}


	//#############################################################
	//#    PRUEBAS    
	//#############################################################

	//-------------------------------------------------------------
	//--- Primitvas BASICAS del DT
	//-------------------------------------------------------------

	function test_recuperar_claves()
	{
		$this->assertEqualArray( $this->dt->get_clave(), array('id') );
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
		$this->cargar("id IN (0,1,2)");
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