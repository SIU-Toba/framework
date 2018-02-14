<?php
/**
* 
* Incluye una barra con nombre y ayuda contextual de la operación, 
* y centraliza el contenido de la salida de la operación
* 
* @package SalidaGrafica
*/
class toba_tp_basico_titulo extends toba_tp_basico
{
	protected $clase_encabezado = 'encabezado';	

	function barra_superior()
	{
		echo "<div id='barra_superior' class='barra-superior barra-superior-tit'>\n";		
		$this->info_version();
		echo "<div class='item-barra'>";
		$this->generar_ayuda();		
		echo "<div class='item-barra-tit'>".$this->titulo_item()."</div>";
		echo "</div>\n\n";
	}
	
	protected function estilos_css()
	{
		parent::estilos_css();
		echo "
		<style type='text/css'>
			#barra_superior {
				display:block;
			}
		</style>			
		";
	}	
	
	protected function generar_ayuda()
	{
		$mensaje = toba::mensajes()->get_operacion_actual();
		if (isset($mensaje)) {
			if (strpos($mensaje, ' ') !== false) {	//Detecta si es una url o un mensaje completo
				$desc = toba_parser_ayuda::parsear($mensaje);
				$ayuda = toba_recurso::ayuda(null, $desc, 'item-barra-ayuda', 0);
				echo "<div $ayuda>";
				echo toba_recurso::imagen_toba("ayuda_grande.gif", true);
				echo "</div>";
			} else {
				if (! toba_parser_ayuda::es_texto_plano($mensaje)) {
					$mensaje = toba_parser_ayuda::parsear($mensaje, true); //Version resumida
				}
				$js = "abrir_popup('ayuda', '$mensaje', {width: 800, height: 600, scrollbars: 1})";
				echo "<a class='barra-superior-ayuda' href='#' onclick=\"$js\" title='Abrir ayuda'>".toba_recurso::imagen_toba("ayuda_grande.gif", true)."</a>";
			}
		}	
	}
	
	/**
	 * Retorna el título de la opreación actual, utilizado en la barra superior
	 */
	protected function titulo_item()
	{
		return toba::solicitud()->get_datos_item('item_nombre');
	}

	protected function info_version()
	{
		$version = toba::proyecto()->get_parametro('version');
		if( $version && ! (toba::proyecto()->get_id() == 'toba_editor') ) {
			$info = '';
			$version_fecha = toba::proyecto()->get_parametro('version_fecha');
			if($version_fecha) {
				$info .= "Lanzamiento: <strong>$version_fecha</strong> <br />";	
			}			
			$version_detalle = toba::proyecto()->get_parametro('version_detalle');
			if($version_detalle) {
				$info .= "<hr />$version_detalle<br>";	
			}
			$version_link = toba::proyecto()->get_parametro('version_link');
			if($version_link) {
				$info .= "<hr /><a href=\'http://$version_link\' target=\"_bank\">Más información</a><br>";	
			}
			if($info) {
				$info = "Versión: <strong>$version</strong><br>" . $info;
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