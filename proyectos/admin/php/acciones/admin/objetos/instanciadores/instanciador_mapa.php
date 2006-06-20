<?
	if($editable = $this->zona->obtener_editable_propagado()){

		include_once("nucleo/browser/clases/objeto_mapa.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();

		$mapa = new objeto_mapa($editable);
		$mapa->cargar_datos();
		$mapa->obtener_html();

		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("INSTANCIADOR de MAPAS: No se explicito el objeto a a cargar","error");
	}
?>