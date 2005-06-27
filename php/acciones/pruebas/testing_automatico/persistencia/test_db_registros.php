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
			case "01_s":					// Tabla 01, simple
				$this->llenar_tabla(1);
				require_once("dbr_test_db_registros_01.php");
				$this->dbr = new dbr_test_db_registros_01("a","instancia",0);
				break;						
			case "02_s":					// Tabla 02, simple
				$this->llenar_tabla(2);
				require_once("dbr_test_db_registros_02.php");
				$this->dbr = new dbr_test_db_registros_02("a","instancia",0);
				break;						
			case "01_mt":					// Tabla 01, multitabla
				$this->llenar_tabla(1);
				$this->llenar_tabla(2);
				require_once("dbr_test_db_registros_01_mt.php");
				$this->dbr = new dbr_test_db_registros_01_mt("a","instancia",0);
				break;						
			}
	}

	function descargar_dbr()
	{
		$this->dbr->resetear();
		unset($this->dbr);	
		$this->vaciar_tablas();
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
					  FOREIGN KEY (id)
					    REFERENCES test_db_registros_01(id)
					    ON DELETE NO ACTION
					    ON UPDATE NO ACTION
					    NOT DEFERRABLE
					);";	
		ejecutar_sql($sql);
	}

	function eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_db_registros_02;";
		$sql[] = "DROP TABLE test_db_registros_01;";
		ejecutar_sql($sql);
	}

	//----------------------------------------------

	function llenar_tabla($id)
	{
		switch($id){
			case "1":
				//-- Tabla 01
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
				//-- Tabla 02
				$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
							VALUES ('3','Vienen de Chipoletti');";
				$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
							VALUES ('2','Aparecen en el otoño');";
				$this->tablas_utilizadas[] = 2;
				break;	
		}
		ejecutar_sql($sql);
	}

	function vaciar_tablas()
	{
		foreach($this->tablas_utilizadas as $tabla)
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
		}
		ejecutar_sql($sql);
	}
			
	//----------------------------------------------
	//--  REGISTROS  -------------------------------
	//----------------------------------------------
	
	function get_tabla_registro($tabla,$registro)
	//Registros para insertar en las tablas
	{
		static $datos;
		//tabla 01
		$datos[1][0]['id']="10";
		$datos[1][0]['nombre']="Cosa";
		$datos[1][0]['descripcion']="Esta es un cosa";
		$datos[1][1]['id']="20";
		$datos[1][1]['nombre']="Hola";
		$datos[1][1]['descripcion']="Este es un Hola";
		//- Registro invalido (nombre NULL)
		$datos[1][2]['id']="20";
		$datos[1][2]['descripcion']="Este es un Perro";
		//- Registro invalido (Estructua incorrecta)
		$datos[1][3]['id']="20";
		$datos[1][3]['nombre']="Hola";
		$datos[1][3]['descripcion']="Este es un Perro";
		$datos[1][3]['columna_invalida']="Todo mal";
		//- Registro invalido para la DB (El ID es un string)
		$datos[1][4]['id']="xxx";
		$datos[1][4]['nombre']="Hola";
		$datos[1][4]['descripcion']="Este es un Perro";
		if(isset($datos[$tabla][$registro])){
			return 	$datos[$tabla][$registro];
		}else{
			throw new exception_toba("Se solicito un registro inexistente");
		}
	}
	//----------------------------------------------
}
?>