<?php
require_once('nucleo/componentes/interface/toba_ci.php');

class extension_ci extends toba_ci
{
	protected $dia;
	protected $semana;
	
	function mantener_estado_sesion() 
	{ 
		$propiedades = parent::mantener_estado_sesion(); 
		$propiedades[] = "dia";	
		$propiedades[] = "semana";	
		return $propiedades; 	
	}

	// La idea es que el evento "carga" del calendario recupere los contenidos de la base de datos.
	// Para dar un ejemplo concreto de cómo se visulizan los contenidos, se setea un contenido fijo
	// para el día de la fecha.
	function conf__calendario()
	{
        $this->dependencia("calendario")->set_ver_contenidos(true);
		$hoy = date("Y-m-d", mktime());
		$datos = array();
		$datos[] = array('dia' => $hoy, 'contenido' => "Actividad");
		return $datos;
    }

	function evt__calendario__seleccionar_dia($seleccion)
	{
		$this->dia = "{$seleccion['anio']}-{$seleccion['mes']}-{$seleccion['dia']}";
		unset($this->semana);
	}
	
	function evt__calendario__seleccionar_semana($seleccion)
	{
		$this->semana = "{$seleccion['semana']}-{$seleccion['anio']}";
		unset($this->dia);
	}

	function conf__formulario()
	{
		if (isset($this->dia))
			return array( 'dia' => $this->dia );
		elseif (isset($this->semana))
			return array( 'semana' => $this->semana );
    }
	
	// La modificación del formulario debería actualizar los contenidos en la base de datos, 
	// para que luego se reflejen los cambios ante un evento de carga del calendario.
	function evt__formulario__modificacion($datos)
	{
		if( isset($this->dia) ) // carga semanal
			// Ejemplo de asignación de contenidos, sólo para visulaización, no es la forma correcta.
			$this->dependencia('calendario')->calendario->setEventContent($this->dia, $datos['contenido']);
	}

}
  
?>