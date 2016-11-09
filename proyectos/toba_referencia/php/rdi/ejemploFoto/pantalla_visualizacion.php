<?php
php_referencia::instancia()->agregar(__FILE__);

class pantalla_visualizacion extends toba_ei_pantalla
{

	function generar_layout()
	{
		$img = $this->controlador()->recuperar_contenido();
		echo "<img src='". toba::escaper()->escapeHtmlAttr($img['url'])."'>";
	}
}

?>
