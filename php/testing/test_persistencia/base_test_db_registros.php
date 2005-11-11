<?php
class base_test_db_registros extends test_toba
{
/*
	En realidad no es necesario borrar e insertar los registros entre cada test.
	Si se pone muy lento se puede reemplazar abortando transacciones.
*/
	protected $dbr;
	
	function get_descripcion()
	{
		return "";
	}	

	//----------------------------------------------
	//-- Configuracion general de todos los TESTs
	//----------------------------------------------

	function pre_run()
	{
		//Si el test fallo, los objetos de la base no se borraron...
		foreach( $this->get_sql_eliminar_tablas() as $sql ){
			try{
				ejecutar_sql($sql);
			}catch(excepcion_toba $e){
				//todo bien! el elemento no existia
			}
		}
		//Creo los elementos		
		ejecutar_sql( $this->get_sql_tablas() );
	}
	
	function post_run()
	{
		ejecutar_sql( $this->get_sql_eliminar_tablas() );
	}

	function SetUp()
	{
		ejecutar_sql( $this->get_sql_juego_datos() );
		$this->dbr = $this->get_dbr();
	}

	function TearDown()
	{
		ejecutar_sql( $this->get_sql_eliminar_juego_datos() );
		$this->dbr->resetear();
		unset($this->dbr);
	}

	//----------------------------------------------
	//-- Mostrar Informacion
	//----------------------------------------------

	function dump($mensaje="Info")
	{
		ei_arbol($this->dbr->info(true),$mensaje);	
	}

	function dump_definicion_externa($mensaje="definicion EXTERNA")
	{
		ei_arbol($this->dbr->get_definicion(),$mensaje);	
	}

	function dump_definicion($mensaje="Info DEFINICION")
	{
		ei_arbol($this->dbr->info_definicion(),$mensaje);	
	}

	function dump_control($mensaje="Estructura CONTROL")
	{
		ei_arbol($this->dbr->get_estructura_control(),$mensaje);	
	}

	function dump_datos($mensaje="Registros")
	{
		ei_arbol($this->dbr->get_registros(null, true),$mensaje);	
	}
	
	function dump_datos_col($mensaje="Registros")
	{
		ei_arbol($this->dbr->get_registros(),$mensaje);	
	}
	
	function dump_tabla($tabla)
	{
		ei_arbol( $this->get_datos_tabla($tabla),"Datos TABLA: $tabla" );		
	}

	//----------------------------------------------
	//-- Herramientas
	//----------------------------------------------

	function get_datos_tabla($tabla)
	{
		return consultar_fuente( "SELECT * FROM $tabla;" );	
	}
	//----------------------------------------------
}
?>