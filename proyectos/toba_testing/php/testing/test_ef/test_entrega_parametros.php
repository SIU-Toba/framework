<?php

class test_entrega_parametros extends test_toba
{

	function get_descripcion()
	{
		return "EF * - Entrega de parametros";
	}	

	function test_entregar_parametros()
	{
		$metodo_recuperacion = "get_parametros";
		
		$sql = 'SELECT elemento_formulario, descripcion FROM apex_elemento_formulario WHERE obsoleto = 0;';
		$ef = consultar_fuente($sql, "instancia");
		for($a=0;$a<count($ef);$a++)
		{
			$clase = 'toba_' . $ef[$a]['elemento_formulario'];
			$desc = $ef[$a]['descripcion'];

			try{
				$rc = new ReflectionClass($clase);
				try{
					$metodos = $rc->getMethods();
					$encontrado = false;
					foreach ($metodos as $metodo) {
						if ($metodo->getName() == $metodo_recuperacion)
							$encontrado = true;
					}
					/*
					if ($encontrado) {
						$sentencia = "\$parametros = $clase::$metodo_recuperacion();";
						eval($sentencia);
						$parametros = call_user_func(array($clase, $metodo_recuperacion));
						if(! is_array($parametros)){
							$this->fail("El metodo de la clase '$clase' no devuelve un array.");
						}
					} else {
						$this->fail("El metodo '$metodo_recuperacion' no existe en la clase '$clase' no existe.");
					}
					*/
				}catch(Exception $e){
//					echo("El metodo '$metodo_recuperacion' no existe en la clase '$clase' no existe.<br>");
					$this->fail("El metodo '$metodo_recuperacion' no existe en la clase '$clase' no existe.");
				}
			}catch(Exception $e){
				//echo("La clase '$clase' no existe.<br>");
				$this->fail("La clase '$clase' no existe.");
			}
		}
	}

}
?>