<?php
require_once("base_test_datos.php");

class test_dr_1n_simple extends base_test_datos
{
	protected $dr;

	function SetUp()
	{
		ejecutar_sql( $this->get_sql_juego_datos() );
		$this->dr = $this->get_dr();
	}

	function TearDown()
	{
		ejecutar_sql( $this->get_sql_eliminar_juego_datos() );
		unset($this->dr);
	}	
	
	function get_descripcion()
	{
		return "OBJETO datos_relacion (1-N simple)";
	}	

	function get_dr()
	{
		$dr = toba_constructor::get_runtime(array('proyecto' => 'toba_testing', 'componente' => '1516'), 
											'toba_datos_relacion');	
		$dr->inicializar();
		return $dr;
	}

	function get_sql_tablas()
	{
		$sql[] = "CREATE SEQUENCE seq_maestro INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;";
		$sql[] = "CREATE TABLE test_maestro (
						id 				int4			DEFAULT nextval('\"seq_maestro\"'::text) NOT NULL, 
						nombre			VARCHAR(20) 	NOT NULL, 
						descripcion 		VARCHAR(80), 
						CONSTRAINT test_maestro_pkey PRIMARY KEY(id)
					);";
		$sql[] = "CREATE TABLE test_detalle_a (
						id 					SMALLINT 		NOT NULL, 
						maestro				INT4			NOT NULL,
					  	nombre				VARCHAR(20) 	NOT NULL, 
					  	descripcion 		VARCHAR(80), 
					  	CONSTRAINT test_a_pkey PRIMARY KEY(id),
						FOREIGN KEY (maestro) REFERENCES test_maestro(id) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";
		$sql[] = "CREATE TABLE test_detalle_b (
						id 					SMALLINT 		NOT NULL, 
						maestro				INT4			NOT NULL,
					  	nombre				VARCHAR(20) 	NOT NULL, 
					  	descripcion 		VARCHAR(80), 
					  	CONSTRAINT test_b_pkey PRIMARY KEY(id),
						FOREIGN KEY (maestro) REFERENCES test_maestro(id) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_detalle_a;";
		$sql[] = "DROP TABLE test_detalle_b;";
		$sql[] = "DROP TABLE test_maestro;";
		$sql[] = "DROP SEQUENCE seq_maestro;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "SELECT setval('seq_maestro', 4, false);";
		$sql[] = "INSERT INTO test_maestro (id, nombre, descripcion) VALUES ('0','Peras','Las peras.');";
		$sql[] = "INSERT INTO test_maestro (id, nombre, descripcion) VALUES ('1','Naranjas','Las naranjas.');";
		$sql[] = "INSERT INTO test_maestro (id, nombre, descripcion) VALUES ('2','Mandarinas','Las mandarinas.');";
		$sql[] = "INSERT INTO test_maestro (id, nombre, descripcion) VALUES ('3','Manzanas','Las manzanas.');";
		$sql[] = "INSERT INTO test_detalle_a (id, maestro, nombre, descripcion) VALUES ('0','0','Gusano','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_detalle_a (id, maestro, nombre, descripcion) VALUES ('1','0','Polilla','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_detalle_a (id, maestro, nombre, descripcion) VALUES ('2','1','Mosca','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_detalle_a (id, maestro, nombre, descripcion) VALUES ('3','2','Rata','Las manzanas son ricas.');";
		$sql[] = "INSERT INTO test_detalle_b (id, maestro, nombre, descripcion) VALUES ('0','0','Viento','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_detalle_b (id, maestro, nombre, descripcion) VALUES ('1','2','Fuego','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_detalle_b (id, maestro, nombre, descripcion) VALUES ('2','3','Invasion','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_detalle_b (id, maestro, nombre, descripcion) VALUES ('3','3','Inundacion','Las manzanas son ricas.');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_detalle_a;";
		$sql[] = "DELETE FROM test_detalle_b;";
		$sql[] = "DELETE FROM test_maestro;";
		return $sql;
	}

	function get_fila_test($tabla, $concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['maestro']['valido_1']['id']="10";
		$datos['maestro']['valido_1']['nombre']="TOMATE";
		$datos['maestro']['valido_1']['descripcion']="Esta es una cosa";
		$datos['detalle_a']['valido_1']['id']="10";
		$datos['detalle_a']['valido_1']['nombre']="TOMATE";
		$datos['detalle_a']['valido_1']['maestro']="0";
		$datos['detalle_a']['valido_1']['descripcion']="Esta es una cosa";
		$datos['detalle_b']['valido_1']['id']="11";
		$datos['detalle_b']['valido_1']['nombre']="TOMATE";
		$datos['detalle_b']['valido_1']['maestro']="0";
		$datos['detalle_b']['valido_1']['descripcion']="Esta es una cosa";
		$datos['detalle_a']['valido_2']['id']="21";
		$datos['detalle_a']['valido_2']['nombre']="TOMATESSSSSSSSs";
		$datos['detalle_a']['valido_2']['maestro']="0";
		$datos['detalle_a']['valido_2']['descripcion']="Esta es una cosa";
		$datos['detalle_b']['valido_2']['id']="22";
		$datos['detalle_b']['valido_2']['nombre']="TOMATESSSSSSSSS";
		$datos['detalle_b']['valido_2']['maestro']="0";
		$datos['detalle_b']['valido_2']['descripcion']="Esta es una cosa";
		if(isset($datos[$tabla][$concepto])){
			return 	$datos[$tabla][$concepto];
		}else{
			throw new exception_toba("Se solicito una FILA inexistente");
		}
	}
	

	//#############################################################
	//#    PRUEBAS    
	//#############################################################

	function test_carga()
	{
		$this->dr->cargar( array("id"=>0) );
		$this->control_cambios_dr(	array(	"maestro" => array("db"),
										"detalle_a" => array("db", "db"),
										"detalle_b" => array("db")));
	}

	function test_edicion_desde_cero()
	{
		$this->dr->tabla('maestro')->nueva_fila( $this->get_fila_test("maestro", 'valido_1') );
		$this->dr->tabla('detalle_a')->nueva_fila( $this->get_fila_test("detalle_a", 'valido_1') );
		$this->dr->tabla('detalle_a')->nueva_fila( $this->get_fila_test("detalle_a", 'valido_2') );
		$this->dr->tabla('detalle_b')->nueva_fila( $this->get_fila_test("detalle_b", 'valido_1') );
		$this->dr->tabla('detalle_b')->nueva_fila( $this->get_fila_test("detalle_b", 'valido_2') );
		$this->control_cambios_dr(	array(	"maestro" => array("i"),
										"detalle_a" => array("i", "i"),
										"detalle_b" => array("i", "i")));
		$this->dr->sincronizar();
		$this->control_cambios_dr(	array(	"maestro" => array("db"),
										"detalle_a" => array("db", "db"),
										"detalle_b" => array("db", "db")));
	}

	function test_edicion_sobre_datos_cargados()
	{
		$this->dr->cargar( array("id"=>0) );
		//Eliminar e insertar en A
		$this->dr->tabla('detalle_a')->eliminar_fila(0);
		$this->dr->tabla('detalle_a')->nueva_fila( $this->get_fila_test("detalle_a", 'valido_1') );
		//Insertar en B
		$this->dr->tabla('detalle_b')->nueva_fila( $this->get_fila_test("detalle_b", 'valido_1') );
		try{
			//Como ya hay uno en la base, este supera el tope
			$fila_db = $this->get_fila_test("detalle_b", 'valido_2');
			$this->dr->tabla('detalle_b')->nueva_fila( $fila_db );
			$this->fail('Control del TOPE maximo');
		}catch(toba_error $e){
			$this->pass();
		}
		//Modificar en M
		$fila_m = $this->dr->tabla('maestro')->get_fila(0);
		$fila_m['nombre'] = "Repollo";
		$this->dr->tabla('maestro')->modificar_fila(0, $fila_m );
		$this->control_cambios_dr(	array(	"maestro" => array("u"),
										"detalle_a" => array("d", "db", "i"),
										"detalle_b" => array("db", "i")));
		$this->dr->sincronizar();
		$this->control_cambios_dr(	array(	"maestro" => array("db"),
										"detalle_a" => array("db", "db"),
										"detalle_b" => array("db", "db")));
	}	
}
?>
