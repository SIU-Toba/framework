<?php
require_once('nucleo/componentes/interface/toba_ci.php');

class extension_ci extends toba_ci
{
	protected $s__dia;
	protected $s__semana;
	
	// La idea es que en la carga del calendario recupere los contenidos de la base de datos.
	// Para dar un ejemplo concreto de c�mo se visulizan los contenidos, se setea un contenido fijo
	// para el d�a de la fecha.
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
		$this->s__dia = "{$seleccion['anio']}-{$seleccion['mes']}-{$seleccion['dia']}";
		unset($this->s__semana);
	}
	
	function evt__calendario__seleccionar_semana($seleccion)
	{
		$this->s__semana = "{$seleccion['semana']}-{$seleccion['anio']}";
		unset($this->s__dia);
	}

	function conf__formulario()
	{
		if (isset($this->s__dia))
			return array( 'dia' => $this->s__dia );
		elseif (isset($this->s__semana))
			return array( 'semana' => $this->s__semana );
    }
	
	// La modificaci�n del formulario deber�a actualizar los contenidos en la base de datos, 
	// para que luego se reflejen los cambios ante un evento de carga del calendario.
	function evt__formulario__modificacion($datos)
	{
		if( isset($this->s__dia) ) // carga semanal
			// Ejemplo de asignaci�n de contenidos, s�lo para visulaizaci�n, no es la forma correcta.
			$this->dependencia('calendario')->calendario->setEventContent($this->s__dia, $datos['contenido']);
	}

}
  
?>