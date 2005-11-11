<?php
require_once("base_test_datos.php");
require_once('nucleo/persistencia/objeto_datos_relacion.php');

class base_test_datos_relacion extends base_test_datos
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
		ei_arbol($this->dr->get_conjunto_datos_interno(), $mensaje);
	}

	//-------------------------------------------------------------
	// Herramientas
	//-------------------------------------------------------------

	function control_cambios($estado_esperado)
	{
		foreach( $estado_esperado as $tabla => $cambios_esperados ){
			$a=0;
			foreach( $this->dr->tabla($tabla)->get_cambios() as $cambios){
				$this->AssertEqual($cambios['estado'], $cambios_esperados[$a] );
				$a++;
			}
		}
	}

	function test_info()
	{
		return;
		$this->dump_relaciones();
		$this->dump_contenido();
		$this->dump();
	}
	//-------------------------------------------------------------

}
?>