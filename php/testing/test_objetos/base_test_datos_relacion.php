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
		$this->dump_relaciones();
		return;
		$this->dump_contenido();
		$this->dump();
	}

	//#############################################################
	//#    PRUEBAS    
	//#############################################################

	function test_cargar()
	{
		$this->dr->cargar( array("id"=>0) );
		$this->dump_contenido();
	}

	//-------------------------------------------------------------
	//--- Primitvas BASICAS del DT
	//-------------------------------------------------------------

}
?>