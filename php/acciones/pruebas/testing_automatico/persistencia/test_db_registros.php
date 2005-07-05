<?php
class test_db_registros extends test_toba
{
	protected $dbr;
	protected $tablas_utilizadas = array();
	protected $dbr_a_utilizar;

	function pre_run()
	{
		$this->crear_tablas();	
	}
	
	function post_run()
	{
		$this->eliminar_tablas();
	}

	//----------------------------------------------

	function SetUp()
	{
		//abrir_transaccion();
		$this->cargar_dbr();
	}

	function TearDown()
	{
		$this->descargar_dbr();
	}

	//----------------------------------------------
	//--  db_registros  ----------------------------
	//----------------------------------------------

	function cargar_dbr()
	{
		switch($this->dbr_a_utilizar){
			case "01_s":					// Tabla 01, simple.
				$this->llenar_tabla(1);
				require_once("dbr_test_db_registros_01.php");
				$this->dbr = new dbr_test_db_registros_01("a","instancia",0);
				break;						
			case "01_mt_1":					// Tabla 01, multitabla con IDs identicos.
				$this->llenar_tabla(1);
				$this->llenar_tabla(2);
				$this->llenar_tabla(3);
				require_once("dbr_test_db_registros_01_mt_1.php");
				$this->dbr = new dbr_test_db_registros_01_mt_1("a","instancia",0);
				break;						
			case "01_mt_2":					// Tabla 01, multitabla con IDs distintos.
				$this->llenar_tabla(1);
				$this->llenar_tabla(4);
				$this->llenar_tabla(5);
				require_once("dbr_test_db_registros_01_mt_2.php");
				$this->dbr = new dbr_test_db_registros_01_mt_2("a","instancia",0);
				break;						
			case "02_s":					// Tabla 02, simple.
				$this->llenar_tabla(2);
				require_once("dbr_test_db_registros_02.php");
				$this->dbr = new dbr_test_db_registros_02("a","instancia",0);
				break;						
			}
	}

	function descargar_dbr()
	{
		$this->dbr->resetear();
		unset($this->dbr);	
		$this->vaciar_tablas();
	}

	function dump_control()
	{
		ei_arbol($this->dbr->get_estructura_control(),"Estructura CONTROL");	
	}

	function dump_datos()
	{
		ei_arbol($this->dbr->obtener_registros(null, true),"Registros");	
	}

	//----------------------------------------------
	//--  Base de Datos  ---------------------------
	//----------------------------------------------

	function crear_tablas()
	{
		//Tabla principal
		$sql[] = "CREATE TEMPORARY TABLE test_db_registros_01 (
					  id 				SMALLINT 		NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_db_registros_01_pkey PRIMARY KEY(id)
					);";
		//Extension
		$sql[] = "CREATE TEMPORARY TABLE test_db_registros_02 (
					  id SMALLINT NOT NULL, 
					  extra VARCHAR(20) NOT NULL, 
					  CONSTRAINT test_db_registros_02_pkey PRIMARY KEY(id), 
					  FOREIGN KEY (id) REFERENCES test_db_registros_01(id) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";	
		$sql[] = "CREATE TEMPORARY TABLE test_db_registros_02b (
					  id_2 SMALLINT NOT NULL, 
					  extra VARCHAR(20) NOT NULL, 
					  CONSTRAINT test_db_registros_02b_pkey PRIMARY KEY(id_2), 
					  FOREIGN KEY (id_2) REFERENCES test_db_registros_01(id) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";	
		ejecutar_sql($sql);
	}

	function eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_db_registros_02b;";
		$sql[] = "DROP TABLE test_db_registros_02;";
		$sql[] = "DROP TABLE test_db_registros_01;";
		ejecutar_sql($sql);
	}

	//----------------------------------------------

	function llenar_tabla($id)
	{
		switch($id){
			case "1":
				//-- Tabla 01 COMPLETA
				$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
						VALUES ('3','Manzanas','Las manzanas son ricas.');";
				$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
						VALUES ('0','Peras','Las peras son ricas.');";
				$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
						VALUES ('2','Mandarinas','Las mandarinas son ricas.');";
				$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
						VALUES ('1','Naranjas','Las naranjas son ricas.');";
				$this->tablas_utilizadas[] = 1;
				break;
			case "2":
				//-- Tabla 02 MITAD
				$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
							VALUES ('3','Vienen de Chipoletti');";
				$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
							VALUES ('2','Aparecen en el otoño');";
				$this->tablas_utilizadas[] = 2;
				break;	
			case "3":
				//-- Tabla 02 MITAD
				$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
							VALUES ('0','Peras!!');";
				$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
							VALUES ('1','Increibles');";
				$this->tablas_utilizadas[] = 2;
				break;	
			case "4":
				//-- Tabla 02 MITAD
				$sql[] = "INSERT INTO test_db_registros_02 (id_2, extra)
							VALUES ('3','Vienen de Chipoletti');";
				$sql[] = "INSERT INTO test_db_registros_02 (id_2, extra)
							VALUES ('2','Aparecen en el otoño');";
				$this->tablas_utilizadas[] = 3;
				break;	
			case "5":
				//-- Tabla 02 MITAD
				$sql[] = "INSERT INTO test_db_registros_02 (id_2, extra)
							VALUES ('0','Peras!!');";
				$sql[] = "INSERT INTO test_db_registros_02 (id_2, extra)
							VALUES ('1','Increibles');";
				$this->tablas_utilizadas[] = 3;
				break;	
		}
		ejecutar_sql($sql);
	}

	function vaciar_tablas()
	{
		rsort($this->tablas_utilizadas);
		foreach( $this->tablas_utilizadas as $tabla)
		{
			$this->vaciar_tabla($tabla);	
		}			
	}

	function vaciar_tabla($id)
	{
		switch($id){
			case "1":
				//-- Tabla 01
				$sql[] = "DELETE FROM test_db_registros_01;";
				break;
			case "2":
				//-- Tabla 02
				$sql[] = "DELETE FROM test_db_registros_02;";
				break;	
			case "3":
				//-- Tabla 02
				$sql[] = "DELETE FROM test_db_registros_02b;";
				break;	
		}
		ejecutar_sql($sql);
	}
}
?>