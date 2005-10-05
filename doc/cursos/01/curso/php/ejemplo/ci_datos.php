<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class ci_datos extends objeto_ci
{
	private $tabla;
	
	private function get_tabla() 
	{
		if(!isset($this->tabla)) {
			$this->cargar_dependencia("datos");
			$this->tabla = $this->dependencias["datos"];			
		}
		return $this->tabla;		
	}
	
	function evt__datos()
	{
		ei_arbol( consulta::get_jurisdicciones() );
	}

	function evt__procesar()
	{
		//$this->insertar();
		$this->eliminar();
	}


	function eliminar()
	{
		$t = $this->get_tabla();
		$clave['jurisdiccion'] = 8;
		$t->cargar($clave);
		$t->eliminar_filas();
		$t->sincronizar();
	}

	function insertar()
	{
		$t = $this->get_tabla();
		$datos['jurisdiccion'] = "4444444";
		$datos['descripcion'] = "hola";
		$datos['estado'] = "a";	
		$t->nueva_fila( $datos );
		$datos['jurisdiccion'] = "4444";
		$datos['descripcion'] = "hola";
		$datos['estado'] = "a";	
		$t->nueva_fila( $datos );
		$datos['jurisdiccion'] = "4555";
		$datos['descripcion'] = "hola";
		$datos['estado'] = "a";	
		$t->nueva_fila( $datos );
		//$datos = $t->get_filas();
		//ei_arbol($datos);
		$t->sincronizar();
	}

	function get_datos()
	{
		return $datos;
	}

	function cargar()
	{
		$t = $this->get_tabla();
		$clave['jurisdiccion'] = 8;
		$t->cargar($clave);
		$datos = $t->get_filas();
		ei_arbol($datos);
		//echo $t->get_cantidad_filas();
	}
}

?>