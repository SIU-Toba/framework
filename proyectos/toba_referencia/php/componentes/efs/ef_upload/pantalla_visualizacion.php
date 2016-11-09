<?php
php_referencia::instancia()->agregar(__FILE__);

class pantalla_visualizacion extends toba_ei_pantalla
{

	function generar_layout()
	{
		$img = toba::proyecto()->get_www_temp($this->controlador->s__nombre_archivo);
		echo "<img src='". toba::escaper()->escapeHtmlAttr($img['url'])."'>";
	}
}

?>
