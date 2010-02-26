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
				ejecutar_fuente($sql);
			}catch(toba_error $e){
				//todo bien! el elemento no existia
			}
		}
		//Creo los elementos		
		ejecutar_fuente( $this->get_sql_tablas() );
	}
	
	function post_run()
	{
		ejecutar_fuente( $this->get_sql_eliminar_tablas() );
	}
	//----------------------------------------------
	
	function control_cambios_dr($estado_esperado)
	{
		foreach( $estado_esperado as $tabla => $cambios_esperados ){
			$cant_esperados = count($cambios_esperados);
			$cant_actuales = count($this->dr->tabla($tabla)->get_cambios());
			if ($cant_esperados != $cant_actuales) {
				$this->fail("Se esperaban $cant_esperados y se obtuvieron $cant_actuales");	
				break;
			}
			$a=0;
			foreach( $this->dr->tabla($tabla)->get_cambios() as $cambios){
				$this->AssertEqual($cambios['estado'], $cambios_esperados[$a] );
				$a++;
			}
		}
	}
	
	function control_cambios_dt($estado_esperado)
	//Se le pasa el estado esperado de la tabla de cambios, ordenados a partir de CERO	
	{
		$a=0;
		foreach( $this->dt->get_cambios() as $cambios)
		{
			$this->AssertEqual($cambios['estado'], $estado_esperado[$a] );
			$a++;
		}
	}

	
	
	function get_sql_tablas(){ return array(); }
	function get_sql_eliminar_tablas(){ return array(); }
	function get_sql_juego_datos(){ return array(); }
	function get_sql_eliminar_juego_datos(){ return array(); }
	//----------------------------------------------
}
?>
