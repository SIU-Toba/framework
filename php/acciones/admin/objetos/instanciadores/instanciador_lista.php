<?
	if($editable = $this->zona->obtener_editable_propagado()){
		include_once("nucleo/browser/clases/objeto_lista.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		$lista =& new objeto_lista($editable);
		$lista->conectar_fuente();
		//$lista->info();
		echo "<br>";
		$lista->cargar_datos();
		$lista->obtener_html();
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("El objeto solicitado no existe.","error");
	}
?>