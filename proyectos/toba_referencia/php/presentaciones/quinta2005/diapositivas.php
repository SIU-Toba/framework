<?php
require_once('nucleo/componentes/interface/toba_ci.php'); 
//----------------------------------------------------------------
class diapositivas extends toba_ci
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = "nombre_de_la_propiedad_a_persistir";
		return $propiedades;
	}


	function obtener_html_contenido__1()
	{
		echo toba_recurso::imagen_proyecto("presentaciones/operacion.gif",true);
	}



}

?>