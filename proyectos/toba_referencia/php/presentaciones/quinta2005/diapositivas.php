<?php
require_once('nucleo/componentes/interface/objeto_ci.php'); 
//----------------------------------------------------------------
class diapositivas extends objeto_ci
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = "nombre_de_la_propiedad_a_persistir";
		return $propiedades;
	}


	function obtener_html_contenido__1()
	{
		echo recurso::imagen_pro("presentaciones/operacion.gif",true);
	}



}

?>