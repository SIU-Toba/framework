<?
	if($editable = $this->zona->obtener_editable_propagado())
	{
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
	   	require_once("api/estructura_item.php");
		$elemento = new estructura_item($editable[0], $editable[1]);
		echo "<div width='600'>";
		$elemento->generar_html();
		echo "</div>";
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}
?>