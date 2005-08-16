<?php
require_once("test_db_registros.php");
/*
	Multitabla ESTRICTO con clave SIMPLE IDENTICA.
*/
class test_db_registros_mt_seq extends test_db_registros
{
	function get_sql_tablas()
	{
		$sql[] = "CREATE SEQUENCE seq_maestro INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;";		
		$sql[] = "CREATE TEMPORARY TABLE test_maestro (
					  id 				int4			DEFAULT nextval('\"seq_maestro\"'::text)	NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_maestro_pkey PRIMARY KEY(id)
					);";
		$sql[] = "CREATE TEMPORARY TABLE test_detalle (
					  id 				SMALLINT		NOT NULL, 
					  extra 			VARCHAR(20)		NOT NULL, 
					  CONSTRAINT test_detalle_pkey PRIMARY KEY(id), 
					  FOREIGN KEY (id) REFERENCES test_maestro(id) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";	
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP SEQUENCE seq_maestro;";
		$sql[] = "DROP TABLE test_detalle;";
		$sql[] = "DROP TABLE test_maestro;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "SELECT setval('seq_maestro', 0, false);";
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Manzanas','Las manzanas son ricas.');";
		$sql[] = "INSERT INTO test_detalle (id, extra) VALUES ('0','Peras!!');";
		$sql[] = "INSERT INTO test_detalle (id, extra) VALUES ('1','Increibles');";
		$sql[] = "INSERT INTO test_detalle (id, extra) VALUES ('2','Aparecen en el otoño');";
		$sql[] = "INSERT INTO test_detalle (id, extra) VALUES ('3','Vienen de Chipoletti');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_detalle;";
		$sql[] = "DELETE FROM test_maestro;";
		return $sql;
	}
	
	function get_dbr()
	{
		require_once("test_db_registros_mt_seq_dbr.php");
		return new test_db_registros_mt_seq_dbr("instancia");
	}

	function get_registro_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";
		$datos['valido_1']['extra']="Cossaaaaa!";

		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";
		$datos['valido_2']['extra']="Hollaaaa!";
		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito un registro inexistente");
		}
	}
	//------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------

	function test_info()
	{
		//$this->dump_definicion_externa();
		//$this->dump_definicion();		
		//$this->dump_tabla("test_maestro");
	}
	
	function test_insert()
	{
		$this->dbr->agregar_registro( $this->get_registro_test("valido_1") );
		$this->dbr->agregar_registro( $this->get_registro_test("valido_2") );
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "i");
		$this->AssertEqual($control[1]['estado'], "i");
		$diff = $this->dbr->sincronizar();
		$this->AssertEqual($diff, 2);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "db");
		$this->AssertEqual($control[1]['estado'], "db");
		$datos = $this->dbr->get_registros();
		$this->AssertEqual($datos[0]['id'], 4);
		$this->AssertEqual($datos[1]['id'], 5);
	}
}
?>