<?php
class ci_ejemplo_1 extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- mapa -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__mapa(toba_ei_mapa $mapa)
	{
		$mapa->set_viewport('400', '400');
		$mapa->set_datos('Enviado en el CONF');
		$obj = $mapa->get_mapa();
		$ruta = toba::proyecto()->get_www_temp();
		$obj->web->set('imageurl', $ruta['url']);
		$obj->web->set('imageurl', $ruta['path']);
	}
}

?>