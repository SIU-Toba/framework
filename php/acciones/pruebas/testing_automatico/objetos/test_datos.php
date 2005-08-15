<?php
class test_datos extends test_toba
{
/*
	En realidad no es necesario borrar e insertar los registros entre cada test.
	Si se pone muy lento se puede reemplazar abortando transacciones.
*/
	protected $dbr;

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
		$this->dbr->info(true),$mensaje;
	}
}
?>