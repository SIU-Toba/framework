<?php
require_once("base_test_datos_relacion.php");
require_once('nucleo/persistencia/objeto_datos_relacion.php');

class base_test_datos_relacion_std extends base_test_datos_relacion
{
	//#############################################################
	//#    PRUEBAS    
	//#############################################################

	function test_carga()
	{
		$this->dr->cargar( array("id"=>0) );
		$this->control_cambios(	array(	"maestro" => array("db"),
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
		$this->control_cambios(	array(	"maestro" => array("i"),
										"detalle_a" => array("i", "i"),
										"detalle_b" => array("i", "i")));
		$this->dr->sincronizar();
		$this->control_cambios(	array(	"maestro" => array("db"),
										"detalle_a" => array("db", "db"),
										"detalle_b" => array("db", "db")));
	}

	function test_edicion_sobre_datos_cargados()
	{
		$this->dr->cargar( array("id"=>0) );
		//Eliminar e insertar en A
		$this->dr->tabla('detalle_a')->eliminar_fila(0);
		$fila_da = $this->get_fila_test("detalle_a", 'valido_1');
		$this->dr->tabla('detalle_a')->nueva_fila( $fila_da );
		//Insertar en B
		$fila_db = $this->get_fila_test("detalle_b", 'valido_1');
		$this->dr->tabla('detalle_b')->nueva_fila( $fila_db );
		//Modificar en M
		$fila_m = $this->dr->tabla('maestro')->get_fila(0);
		$fila_m['nombre'] = "Repollo";
		$this->dr->tabla('maestro')->modificar_fila(0, $fila_m );
		$this->control_cambios(	array(	"maestro" => array("u"),
										"detalle_a" => array("d", "db", "i"),
										"detalle_b" => array("db", "i")));
		$this->dr->sincronizar();
		$this->control_cambios(	array(	"maestro" => array("db"),
										"detalle_a" => array("db", "db", "db"),
										"detalle_b" => array("db", "db")));
	}

}
?>