<?php
require_once("nucleo/browser/clases/objeto_mt_me.php");

class objeto_mt_me_prueba extends objeto_mt_me
{
	function objeto_mt_me_prueba($id,&$solicitud)
	{	
		parent::objeto_mt_me($id, $solicitud);	
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

	function obtener_interface_1()
	{
		echo "<table class='tabla-0'  width='100%'>\n";
		echo "<tr><td>";
		$this->dependencias["actividades"]->obtener_html();	
		echo "</td></tr>\n";
		if($clave = $this->dependencias["actividades"]->obtener_clave_registro_activo() ){
			echo "<tr><td>";
			//$this->dependencias["actividades"]->obtener_html();	
			ei_arbol($clave);
			echo "</td></tr>\n";
		}
		echo "</table>\n";
 	}
	//-------------------------------------------------------------------------------
/*
	function procesar_entrada_2()
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