<?php
class ci_ejemplo_1 extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- mapa -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__mapa(toba_ei_mapa $mapa)
	{
		$parametros = toba::db()->get_parametros();
		$dns_conexion = "user={$parametros['usuario']} dbname={$parametros['base']}  password={$parametros['clave']}  host={$parametros['profile']} port={$parametros['puerto']}";
		toba::logger()->debug( " Parametros conexion: \n $dns_conexion");

		$mapa->set_viewport('500', '500');
		$mapa->set_datos('Enviado en el CONF');

		//Obtengo el objeto de mapscript para setearle correctamente las direcciones de las imagenes
		$obj = $mapa->get_mapa();
		$ruta = toba::proyecto()->get_www_temp();
		$obj->web->set('imageurl', $ruta['url']);
		$obj->web->set('imageurl', $ruta['path']);

		//Obtengo los layers y ciclo para fijarle la conexion correcta de la base a modo de ejemplo
		$layers = $mapa->get_nombre_layers();					//Aca accedo al objeto toba_ei_mapa
	/*	foreach($layers as $nombre) {
			$obj->getLayerByName($nombre)->set('connection', $dns_conexion);		//Aca accedo al objeto de mapscript
		}*/					//No se puede hasta resolver el tema de los roles por schema.
	}
}

?>