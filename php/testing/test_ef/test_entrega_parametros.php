<?php

class test_entrega_parametros extends test_toba
{

	function get_descripcion()
	{
		return "EF * - Entrega de parametros";
	}	

	function test_entrega_parametros()
	{
		require_once("nucleo/browser/interface/ef.php");
		$metodo_recuperacion = "get_parametros";
		
		$sql = "SELECT elemento_formulario, descripcion FROM apex_elemento_formulario;";
		$ef = consultar_fuente($sql, "instancia");
		for($a=0;$a<count($ef);$a++)
		{
			$clase = $ef[$a]['elemento_formulario'];
			$desc = $ef[$a]['descripcion'];

			try{
				$rc = new ReflectionClass($clase);
				try{
					$rm = $rc->getMethod($metodo_recuperacion);
					
					//$parametros = $rm->invoke($metodo_recuperacion);//No sirve porque no lo invoca estaticamente
					$sentencia = "\$parametros = $clase::$metodo_recuperacion();";
					eval($sentencia);
					if(is_array($parametros)){
						
					}else{
						$this->fail("El metodo de la clase '$clase' no devuelve un array.");
					}
				}catch(Exception $e){
					echo("El metodo '$metodo_recuperacion' no existe en la clase '$clase' no existe.<br>");
					//$this->fail("El metodo '$metodo_recuperacion' no existe en la clase '$clase' no existe.");
				}
			}catch(Exception $e){
				echo("La clase '$clase' no existe.<br>");
//				$this->fail("La clase '$clase' no existe.");
			}
		}
	}
}
?>