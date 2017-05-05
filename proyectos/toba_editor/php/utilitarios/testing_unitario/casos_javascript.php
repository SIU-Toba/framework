<?php
	
$url = toba_recurso::url_toba().'/js/jsunit/testRunner.html?autorun=true&showTestFrame=50&testPage=';
$path = toba::instalacion()->get_path().'/www/js/testing/';
$casos = glob($path.'*.html');
$escapador = toba::escaper();
echo '<ul>';
if (is_array($casos)) { 
	foreach ($casos as $caso) {
		$nombre = basename($caso);
		$direccion = $escapador->escapeHtmlAttr($url.substr(toba_recurso::url_toba()."/js/testing/$nombre", 7));	//Para sacar el protocolo
		echo "<li><a href='#' onclick=\"solicitar_item_popup('$direccion', 700, 600, 'yes', 'yes');\">";
		echo $escapador->escapeHtml(substr($nombre, 0, -5));
		echo '</a></li>';
	}
}
echo '</ul>';


?>