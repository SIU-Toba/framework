<?php
require_once("toba_tp_basico.php");

/**
* 
* Incluye una barra con nombre y ayuda contextual del item, 
* y centraliza el contenido de la salida del item
* 
* @package SalidaGrafica
*/
class toba_tp_basico_titulo extends toba_tp_basico
{
	protected $clase_encabezado = 'encabezado';	

	protected function barra_superior()
	{
		echo "<div class='barra-superior barra-superior-tit'>\n";		
		$info = toba::solicitud()->get_datos_item();
		echo "<div class='item-barra'>";
		if (trim($info['item_descripcion']) != '') {
			$ayuda = toba_recurso::ayuda(null, trim($info['item_descripcion']), 'item-barra-ayuda', 0);
			echo "<div $ayuda>";
			echo toba_recurso::imagen_toba("ayuda_grande.gif", true);
			echo "</div>";
		}		
		echo "<div class='item-barra-tit'>".$this->titulo_item()."</div>";
		$this->info_version();
		echo "</div>\n\n";
	}
	
	/**
	 * Retorna el título del item actual, utilizado en la barra superior
	 */
	protected function titulo_item()
	{
		return toba::solicitud()->get_datos_item('item_nombre');
	}

	protected function info_version()
	{
		$version = toba::proyecto()->get_parametro('version');
		if( $version && ! toba_editor::acceso_recursivo() ) {
			$info = '';
			$version_fecha = toba::proyecto()->get_parametro('version_fecha');
			if($version_fecha) {
				$info .= "Lanzamiento: <strong>$version_fecha</strong> <br>";	
			}			
			$version_detalle = toba::proyecto()->get_parametro('version_detalle');
			if($version_detalle) {
				$info .= "<hr>$version_detalle<br>";	
			}
			$version_link = toba::proyecto()->get_parametro('version_link');
			if($version_link) {
				$info .= "<hr><a href=\'http://$version_link\' target=\"_bank\">Más información</a><br>";	
			}
			if($info) {
				$info = "Version: <strong>$version</strong><br>" . $info;
				$info = toba_recurso::ayuda(null, $info, 'enc-version');
			}else{
				$info = "class='enc-version'";
			}
			echo "<div $info >";		
			echo 'Versión <strong>' . $version .'</strong>';
			echo '</div>';		
		}
	}	
		
	function pre_contenido()
	{
		echo "\n<div align='center' class='cuerpo'>\n";
	}
	
	function post_contenido()
	{
		echo "\n</div>\n";		
	}
			
}
?>