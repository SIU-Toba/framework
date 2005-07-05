<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ci extends elemento_objeto
{
	function eventos_predefinidos()
	{
		return array('procesar', 'cancelar');	
	}

	function generar_constructor()
	{
		$constructor = 
'	function __construct($id)
	{
		!#c2//Zona apta para inicializaciones por defecto
		parent::__construct($id);
		!#c2//Aqu� ya se restauraron los valores de las propiedades mantenidas en sesi�n
	}
';			
		return $this->filtrar_comentarios($constructor);

	}	
	
	function generar_metodos_basicos()
	{
		$basicos = parent::generar_metodos_basicos();
		$basicos[] =
'	function mantener_estado_sesion()
	!#c2//Declarar todas aquellas propiedades de la clase que se desean persistir autom�ticamente
	!#c2//entre los distintos pedidos de p�gina en forma de variables de sesi�n.
	{
		$propiedades = parent::mantener_estado_sesion();
		!#c1//$propiedades[] = "nombre_de_la_propiedad_a_persistir";
		return $propiedades;
	}
';
		return $this->filtrar_comentarios($basicos);
	}

	function generar_eventos()
	{
		$eventos = parent::generar_eventos();
		
				
		return $eventos;
	}	
}


?>