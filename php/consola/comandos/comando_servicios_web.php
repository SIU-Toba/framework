<?php
require_once('comando_toba.php');

class comando_servicios_web extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de servicios web';
	}
	
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba servicio_web OPCION");
		$this->consola->enter();
	}

	/**
	 * Genera la documentación de los servicios web disponibles en el proyecto (requiere tener publicado el proyecto en localhost)
	 */
	function opcion__generar_doc()
	{
		$prefijo = "http://localhost";
		$sufijo = "/servicios.php/";
		$proyecto =$this->get_proyecto();
		$servicios = toba_info_editores::get_items_servicios_web();
		$carpeta_doc = $proyecto->get_dir()."/doc/servicios_web";
		if (! file_exists($carpeta_doc)) {
			mkdir($carpeta_doc, 0777, true);
		}
		$this->consola->mensaje("Generando documentacion...");		
		copy(toba_dir(). '/php/modelo/var/wsdl-viewer.xsl', $carpeta_doc.'/wsdl-viewer.xsl');
		copy(toba_dir(). '/php/modelo/var/wsdl-viewer.css', $carpeta_doc.'/wsdl-viewer.css');
		$include = '<?xml-stylesheet type="text/xsl" href="wsdl-viewer.xsl"?>';
		$search = '"utf-8"?>';
		$index_page = "<html><head>
		<link href='wsdl-viewer.css' rel='stylesheet' type='text/css' media='screen'/>
		</head>
		<body>
		<div id='header'>
		<h1>{$proyecto->get_id()}</h1><h2>Documentación Servicios Web</h2>
		</div>
		<div id='inner_box'><div class='page'>
		<ul>";
		foreach ($servicios as $servicio) {
			$this->consola->mensaje("Servicio: ".$servicio['item']);			
			$url = $prefijo.$proyecto->get_url().$sufijo.$servicio['item']."?wsdl2";
			$wsdl = file_get_contents($url);
			$wsdl = str_replace($search, $search.$include, $wsdl);
			$file = $servicio['item'].".wsdl.xml";
			file_put_contents($carpeta_doc."/".$file, $wsdl);
			$index_page .= "<li><a href='$file'>{$servicio['item']}</a></li>";			
		}
		$index_page .= "</ul></div></div></body></html>";
		file_put_contents($carpeta_doc."/index.html", $index_page);
		$this->consola->mensaje("Listo. Navegar hacia file://".$carpeta_doc."/index.html");		

	}
	
}
?>