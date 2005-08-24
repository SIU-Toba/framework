<?php
require_once("base_test_datos_relacion.php");

class test_datos_relacion_1 extends base_test_datos_relacion
{

	function get_descripcion()
	{
		return "OBJETO datos_relacion";
	}	

	function get_dr()
	{
		$dt = new objeto_datos_relacion(array('toba_testing','1516'));
		return $dt;
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

	function get_tablas_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['id']="10";
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";
		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito una FILA inexistente");
		}
	}
}
?>