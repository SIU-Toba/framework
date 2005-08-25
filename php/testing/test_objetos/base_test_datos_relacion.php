<?php
require_once("base_test_datos.php");
require_once('nucleo/persistencia/objeto_datos_relacion.php');

class base_test_datos_relacion extends base_test_datos
{
	protected $dr;

	function get_descripcion()
	{
		return "";
	}	

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

	//----------------------------------------------
	//-- Mostrar Informacion
	//----------------------------------------------

	function dump($mensaje="Info")
	{
		$this->dr->info();
	}

	function dump_relaciones($mensaje="RELACIONES")
	{
		ei_arbol($this->dr->get_relaciones(),$mensaje);
	}

	function dump_contenido($mensaje="TABLAS")
	{
		foreach($this->dr->get_lista_tablas() as $tabla){
			$temp[$tabla] = $this->dr->tabla($tabla)->get_cambios();	
		}
		ei_arbol($temp, $mensaje);
	}

	//-------------------------------------------------------------
	// Herramientas
	//-------------------------------------------------------------

	function test_info()
	{
		return;
		$this->dump_relaciones();
		$this->dump_contenido();
		$this->dump();
	}

	//#############################################################
	//#    PRUEBAS    
	//#############################################################

	function test_edicion_completa()
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
		//$this->dump_contenido();
		$this->dr->sincronizar();
	}

	//-------------------------------------------------------------
	//--- Primitvas BASICAS del DT
	//-------------------------------------------------------------

}
?>