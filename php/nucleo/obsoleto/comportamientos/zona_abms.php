<?
//	CONTEXTO ZONAL = las variables locales $elemento, $existe_elemento y $indice_zona 
//	estan llenas. Las mismas se establecen en el script "zona_arriba.inc.php".
//	VARIABLES:
//		$elemento: (ID del elemento que esta circulando por la ZONA)
//		$existe_elemento: (El elemento esta establecido?)
//		$indice_zona: ID de QUERYSTRING que hay que utilizar para seguir navegando por la zona.

	include_once("zona_arriba.inc.php");
	if($existe_elemento){
		$abms = $this->cargar_objeto("objeto_mt_abms",0);
		if($abms > -1){
			$this->objetos[$abms]->procesar_evento(array($elemento));
			$this->objetos[$abms]->obtener_html(array($indice_zona=>$elemento));
			//$this->objetos[$abms]->info();
			include_once("zona_abajo.inc.php");
		}
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO (Contexto ZONA)","error");
	}
?>