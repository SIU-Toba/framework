<?php
class ci_ejemplo_1 extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- mapa -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	protected $fuente_no_configurada = false;
	
	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
		try {
			$parametros = toba::db('fuente_gis')->get_parametros();
		}catch(toba_error $e) {
			$msg = 'Para utilizar el ejemplo GIS es necesario instalar la base de datos disponible en: <a href="http://repositorio.siu.edu.ar/trac/toba/attachment/wiki/Descargar/">Ejemplo GIS</a> <BR>';
			$msg .= 'Luego configure apropiadamente la fuente de datos para GIS';
			$pantalla->set_descripcion($msg, 'info');
			$pantalla->eliminar_dep('mapa');
			$this->fuente_no_configurada = true;
		}
	}
	
	function conf__mapa(toba_ei_mapa $mapa)
	{
		$parametros = toba::db('fuente_gis')->get_parametros();
		$dns_conexion = "user={$parametros['usuario']} dbname={$parametros['base']}  password={$parametros['clave']}  host={$parametros['profile']} port={$parametros['puerto']}";
		toba::logger()->debug( " Parametros conexion: \n $dns_conexion");

		$mapa->set_viewport('500', '500');
		$mapa->set_datos('Enviado en el CONF');

		//Obtengo el objeto de mapscript para setearle correctamente las direcciones de las imagenes
		$obj = $mapa->get_mapa();
		$ruta = toba::proyecto()->get_www_temp();
		$obj->web->set('imageurl', $ruta['url']);
		$obj->web->set('imagepath', $ruta['path']);

		//Obtengo los layers y ciclo para fijarle la conexion correcta de la base a modo de ejemplo
		$layers = $mapa->get_nombre_layers();					//Aca accedo al objeto toba_ei_mapa
		foreach($layers as $nombre) {
			$obj->getLayerByName($nombre)->set('connection', $dns_conexion);		//Aca accedo al objeto de mapscript
		}
	}

	function evt__mapa__nosenada($datos)
	{
		ei_arbol($datos);
	}

	function generar_html()
	{
		try {
			 parent::generar_html();
		} catch(toba_error_def $e) {
			if ($this->fuente_no_configurada) {
				throw new toba_error('Por favor configure la fuente de datos para ejecutar el ejemplo');
			} else {
				throw $e;
			}
		}
	}
	
}

?>