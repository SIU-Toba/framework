<?
	if($editable = $this->zona->obtener_editable_propagado()){
		include_once("nucleo/browser/clases/objeto_esquema.php");
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
		$lista =& new objeto_esquema($editable);
		$lista->conectar_fuente();
		//$lista->info();
		echo "<br>";
		$lista->obtener_html(true);
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("El objeto solicitado no existe.","error");
	}
?>