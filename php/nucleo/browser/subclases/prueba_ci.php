<?php
require_once("nucleo/browser/clases/objeto_ci_me.php");

class objeto_ci_me_prueba extends objeto_ci_me
{
	function objeto_ci_me_prueba($id,&$solicitud)
	{	
		parent::objeto_ci_me($id, $solicitud);	
	}
	//-------------------------------------------------------------------------------

	function procesar_operacion()
	{
		$sql = array();
		foreach(array_keys($this->dependencias) as $dependencia)
		{
			$sql = array_merge($this->dependencias[$dependencia]->obtener_sql(),$sql);
		}
		ei_arbol($sql);
	}
	//-------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------

	function procesar_entrada_1()
	{

 	}


/*
	function obtener_interface_1()
	{

 	}

	function procesar_salida_2()
	{

 	}

	function procesar_entrada_4()
	{
 	}
*/
}
?>