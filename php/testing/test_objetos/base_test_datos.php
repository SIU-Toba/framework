<?php
class base_test_datos extends test_toba
{
/*
	En realidad no es necesario borrar e insertar los registros entre cada test.
	Si se pone muy lento se puede reemplazar abortando transacciones.
*/
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
	//----------------------------------------------
	
	function get_sql_tablas(){ return array(); }
	function get_sql_eliminar_tablas(){ return array(); }
	function get_sql_juego_datos(){ return array(); }
	function get_sql_eliminar_juego_datos(){ return array(); }
	//----------------------------------------------
}
?>