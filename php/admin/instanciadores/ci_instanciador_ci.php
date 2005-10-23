<?php
require_once('ci_instanciadores.php'); 
//----------------------------------------------------------------
class ci_instanciador_ci extends ci_instanciadores
{
	
	/**
	*	Este simulador trata de responder todas las llamadas del ci hijo
	*	sin que este se rompa o falle
	*/
	function __call($metodo, $parametros)
	{
		toba::get_logger()->debug("Simulación - Se invoca el metodo $metodo");
		return new objeto_de_mentira();
	}

}

?>