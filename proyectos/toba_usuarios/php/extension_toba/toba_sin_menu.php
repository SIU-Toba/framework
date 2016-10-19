<?php
class toba_sin_menu extends toba_tp_normal
{
	function __construct()
	{
	}
	
	protected function comienzo_cuerpo()
	{
		$this->cabecera_aplicacion();	
		$wait = toba_recurso::imagen_toba('wait.gif');
		echo "<div id='div_toba_esperar' class='div-esperar' style='display:none'>";
		echo "<img src='". toba::escaper()->escapeHtmlAttr($wait)."' style='vertical-align: middle;' alt='' /> Procesando...";
		echo "</div>\n";
	}
}

?>