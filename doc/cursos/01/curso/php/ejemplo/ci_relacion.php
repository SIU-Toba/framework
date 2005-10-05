<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class ci_relacion extends objeto_ci
{
	private $relacion;

	private function get_relacion()
	{
		if(!isset($this->relacion)) {
			$this->cargar_dependencia("datos");
			$this->relacion = $this->dependencias["datos"];			
		}
		return $this->relacion;		
	}


	function evt__procesar()
	{
		$this->cargar();
		$this->insertar();
	}	

	function cargar()
	{
		$clave['institucion'] = 1;
		$clave['sede'] = 1;
		$r = $this->get_relacion();
		$r->cargar($clave);
	}

	function insertar()
	{
		$edificio['institucion'] = 1;
		$edificio['sede'] = 1;
		$edificio['nombre'] = 'Nuevo';
		$this->get_relacion()->tabla('edificios')->nueva_fila($edificio);
		$this->get_relacion()->sincronizar();
	}

	//Llenar cuadro
	
	function evt__cuadro__carga()
	{
		return $this->get_relacion()->tabla('edificios')->get_filas();
	}	

}
?>