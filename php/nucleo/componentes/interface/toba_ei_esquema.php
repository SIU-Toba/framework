<?php
/**
 * Genera un esquema utilizando GraphViz
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei ei
 * @wiki Referencia/Objetos/ei_esquema
 */
class toba_ei_esquema extends toba_ei
{
	protected $_prefijo = 'esq';	
	protected $_alto;
	protected $_ancho;
	protected $_contenido;				// Instrucciones GraphViz
	protected $_archivo_generado;		// Archivo generado por las instrucciones
	protected $_incluir_mapa = false;
	
	final function __construct($id)
	{
		parent::__construct($id);
		$this->_alto = isset($this->_info_esquema['alto']) ?  $this->_info_esquema['alto'] : null;
		$this->_ancho = isset($this->_info_esquema['ancho']) ?  $this->_info_esquema['ancho'] : null;
		//TODO: Hack para navegacion ajax con windows
		toba_ci::set_navegacion_ajax(false);
	}

	/**
	 * Cambia el esquema actual
	 * @param string $datos Esquema Graphviz
	 */
	function set_datos($datos)
	{
		if (isset($datos)) {
			$this->_contenido = $datos;	
		}
	}
	
	/**
	 * 
	 * Incluir mapa de la imagen. Es necesario que esté
	 * definido un ancho y un alto.
	 * 
	 * @param boolean $incluir.  
	 */
	function set_incluir_mapa($incluir=null)
	{
		$this->_incluir_mapa = $incluir;
	}
	
	function generar_html()
	{
        $ancho = '';
        if (isset($this->_ancho)) {
        	$ancho = "width ='$this->_ancho'";
        }		
        echo "\n<table class='ei-base ei-esquema-base' $ancho>\n";		
		echo"<tr><td style='padding:0'>\n";		
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-esquema-barra-sup");
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "style='display:none'" : "";		
		echo "<div $colapsado id='cuerpo_{$this->objeto_js}'>";
		//Campo de sincronizacion con JS
		echo toba_form::hidden($this->_submit, '');
		if (isset($this->_contenido)) {
			//Se arma el archivo .dot
			toba::logger()->debug($this->get_txt() . " [ Diagrama ]:\n$this->_contenido", 'toba');
			$this->generar_esquema($this->_contenido, $this->_info_esquema['formato'],
			$this->_info_esquema['dirigido'], $this->_ancho,
									$this->_alto, $this->_incluir_mapa, $this->objeto_js);
		}
		$this->generar_botones();
		echo "</div></td></tr>\n";
		echo "</table>\n";
	}
	
	/**
	 * @ignore 
	 */
	protected function generar_esquema($contenido, $formato, $es_dirigido=true, $ancho=null, $alto=null, $incluir_mapa=null, $objeto_js=null)
	{
		$parametros = array("contenido" => $contenido, 
							'formato' => $formato,
							'es_dirigido' => $es_dirigido);
		//Vinculo a un item que hace el passthru y borra el archivo
		$destino = array($this->_id);
		$this->_memoria['parametros'] = $parametros;
		$url = toba::vinculador()->get_url(null,null,array(), array('servicio' => 'mostrar_esquema', 
																		'objetos_destino' => $destino));
		$this->generar_sentencia_incrustacion($url, $formato, $ancho, $alto, $incluir_mapa, $objeto_js);
	}

	/**
	 * Genera el tag HTML necesario para incluir el archivo generado por GrahpViz
	 * @param string $url
	 * @param string $formato
	 * @param string $ancho
	 * @param string $alto
	 */
	static function generar_sentencia_incrustacion($url, $formato, $ancho=null, $alto=null, $incluir_mapa=null, $objeto_js=null)
	{
		if(!$incluir_mapa || !($ancho && $alto)) {
			$ancho = isset($ancho) ? "width='$ancho'" : "";
			$alto = isset($alto) ? "height='$alto'" : "";
			switch ($formato) {
				case 'png':
				case 'gif':
					echo "<img src='$url' $ancho $alto border='0'>";				
				break;
				case 'svg':
					/*toba_js::cargar_consumos_globales(array("utilidades/svglib"));
					echo toba_js::abrir();
					echo "//aviso_instalacion_svg()";
					echo toba_js::cerrar();	*/
					echo "<embed src='$url' type='image/svg+xml' $ancho $alto palette='foreground' pluginspage='http://www.adobe.com/svg/viewer/install/auto'>\n";
					echo "</embed>"; 
				break;
			}
		} else {
			//Sacamos el ratio (el lado mas largo del mapa tendrá 100px)
/*			$ratio = ($ancho>$alto)?100/$ancho:100/$alto;
			$alto_mapa = $alto*$ratio;
			$ancho_mapa = $ancho*$ratio;*/
			if(substr($alto, -2) == 'px') {
				$alto = substr($alto, 0, -2); 
			}
			if(substr($ancho, -2) == 'px') {
				$ancho = substr($ancho, 0, -2); 
			}
			switch ($formato) {
				case 'png':
				case 'gif':
					$imagen_real = "<img id='imagen_real_$objeto_js' src='$url' border='0'>";				
					$imagen_mapa = "<img id='imagen_mapa_$objeto_js' src='$url' border='0'>";
				break;
				case 'svg':
					/*toba_js::cargar_consumos_globales(array("utilidades/svglib"));
					echo toba_js::abrir();
					echo "//aviso_instalacion_svg()";
					echo toba_js::cerrar();	*/
					$imagen_real = "<embed id='imagen_real_$objeto_js' src='$url' type='image/svg+xml' palette='foreground' pluginspage='http://www.adobe.com/svg/viewer/install/auto'></embed>"; 
					$imagen_mapa = "<embed id='imagen_mapa_$objeto_js' src='$url' type='image/svg+xml' palette='foreground' pluginspage='http://www.adobe.com/svg/viewer/install/auto'></embed>";
				break;
			}
			echo "
			<div class='ei-barra-sup ci-barra-sup ei-barra-sup-sin-botonera' style='height: 20px; width: {$ancho}px; color: white; position: relative'>
				<span id='colapsado_mapa_$objeto_js' style='position: absolute; width: 16px; height: 16px; left: 0px;  text-align: center; padding: 2px'>".toba_recurso::imagen_toba('colapsado.gif', true). "</span>
				<div style='height: 20px;position: absolute; left: 20px; width: ".($ancho - 20)."px'> 
					<span id='escala_mapa_$objeto_js' style='position: absolute; width: 16px; height: 16px; right: 0px; text-align: center; padding: 2px; border-right: 1px solid black'>".toba_recurso::imagen_toba('transform-move.png', true). "</span>
				</div>
			</div>
			<div style='height: {$alto}px; width: {$ancho}px; position: relative'>
				<div id='marco_$objeto_js' style='height: {$alto}px; width: {$ancho}px; overflow: hidden; position: absolute; top: 0: left: 0;'>
					$imagen_real
				</div>
				<div id='mapa_$objeto_js' style='position: absolute; top: 0; left: 0; border: 1px solid black; overflow: hidden'>
					<div id='lupa_$objeto_js' style='position: absolute; top: 0: left: 0; border: 1px solid red'></div>
					$imagen_mapa
				</div>
				
			</div>";

			$tam_mapa = '';		
			if (substr($incluir_mapa, -1) == '%') {
				$tam_mapa = ($alto<$ancho?$alto:$ancho) * ((integer)substr($incluir_mapa, 0, -1)) / 100; 
			} elseif(strtolower(substr($incluir_mapa, -2)) == 'px') {
				$tam_mapa = substr($incluir_mapa, 0, -2);
			} else {
				$tam_mapa = $incluir_mapa;
			}
			echo toba_js::incluir(toba_recurso::js('basicos/jquery-1.9.1.min.js'));
			echo toba_js::incluir(toba_recurso::js('utilidades/jquery-ui-1.10.1.custom.min.js'));
			echo toba_js::abrir();
			echo "
			document.readyFunc = function(e) {
				
				var img_real = document.getElementById('imagen_real_$objeto_js');
				if (img_real.clientHeight > 0 && img_real.clientWidth > 0) {
				
					var marco = document.getElementById('marco_$objeto_js');
					var mapa = document.getElementById('mapa_$objeto_js');
					var escala = document.getElementById('escala_mapa_$objeto_js');
					var colapsado = document.getElementById('colapsado_mapa_$objeto_js');
					var lupa = document.getElementById('lupa_$objeto_js');
					var img_mapa = document.getElementById('imagen_mapa_$objeto_js');
					var ancho_mapa = 200 > marco.clientHeight?marco.clientHeight:200;
					var ratio;
					var alto_mapa;
					if(marco.clientHeight > img_real.clientHeight && marco.clientWidth > img_real.clientWidth) {
						escala.style.display = 'none';
						mapa.style.display = 'none';
					}
					var escalar_$objeto_js = function(skipImg) {
						ratio = ancho_mapa/img_real.clientWidth;
						alto_mapa = img_real.clientHeight*ratio;
						if(alto_mapa > marco.clientHeight) {
							ratio = marco.clientHeight/img_real.clientHeight;
							alto_mapa = img_real.clientHeight*ratio;
							ancho_mapa = img_real.clientWidth*ratio;
						}
						mapa.style.height= alto_mapa+'px';
						mapa.style.width= ancho_mapa+'px';
						escala.style.left= (ancho_mapa-39)+'px';
						if(!skipImg) {
							img_mapa.style.height= alto_mapa+'px';
							img_mapa.style.width= ancho_mapa+'px';
							lupa.style.height=((marco.clientHeight*ratio)-2)+'px';//-2: Hay que tener en cuenta el borde
							lupa.style.width=((marco.clientWidth*ratio)-2)+'px';
							lupa.style.top=(marco.scrollTop*ratio)+'px';
							lupa.style.left=(marco.scrollLeft*ratio)+'px';
						}
					};
					escalar_$objeto_js();
					
					lupa.date = new Date();
					$(lupa).draggable({
						containment: 'parent',
						/*drag: function() {
							var d = new Date();
							if(d.getTime() - lupa.date.getTime() > 100) {
								var t = parseInt(lupa.style.top.substr(0, lupa.style.top.length-2))/ratio; 
								var l = parseInt(lupa.style.left.substr(0, lupa.style.left.length-2))/ratio;
								marco.scrollTop = t;
								marco.scrollLeft = l;
								lupa.date = d;
							} 
						},*/ 
						stop: function() {
							var t = parseInt(lupa.style.top.substr(0, lupa.style.top.length-2))/ratio; 
							var l = parseInt(lupa.style.left.substr(0, lupa.style.left.length-2))/ratio;
							marco.scrollTop = t;
							marco.scrollLeft = l;
						}
					});
					$(colapsado).click(function() {
						$(mapa).slideToggle('slow', function() {
							escala.style.display = this.style.display;
						});
					});
					$(escala).draggable({
						containment: 'parent', 
						axis: 'x',
						drag: function() {
							var left = escala.style.left;
							ancho_mapa = parseInt(left.substr(0, left.length - 2))+39;
							escalar_$objeto_js(true);
						},
						stop: function() {
							escalar_$objeto_js();
						}
					});
					$(img_mapa).click(function(event) {
						var x = event.offsetX?event.offsetX:event.layerX;
						var y = event.offsetY?event.offsetY:event.layerY;
						var lh = parseInt(lupa.style.height.substr(0, lupa.style.height.length-2)); 
						var lw = parseInt(lupa.style.width.substr(0, lupa.style.width.length-2));
						
						var l = x - (lw/2) - 1; //-1: Hay que tener en cuenta el borde 
						var t = y - (lh/2) - 1; 
						if(l < 0) {
							l = 0;
						} else if (l + lw + 2 > ancho_mapa) {
							l = ancho_mapa - lw - 2;
						}
						if(t < 0) {
							t = 0;
						} else if (t + lh + 2 > alto_mapa) {
							t = alto_mapa - lh - 2;
						}
						lupa.style.top = t+'px';
						lupa.style.left = l+'px';
						marco.scrollTop = t/ratio;
						marco.scrollLeft = l/ratio;
					});
				} else { 
					setTimeout('$(document).ready(document.readyFunc)', 100);
				}
			};
			$(document).ready(document.readyFunc);
			";
			echo toba_js::cerrar();
		}
	}

	/**
	 * Genera el grafico utilizando el comando graphviz y lo almacena en un archivo temporal
	 *
	 * @param string $contenido Grafico graphviz
	 * @param string $formato Parametro -T del comando graphviz
	 * @param boolean $es_dirigido
	 * @return string Nombre del archivo temporal generado
	 */
	static function generar_archivo($contenido, $formato, $es_dirigido = true)
	{
		$nombre_archivo = mt_rand() . '.' . $formato;
		$dir_temp = toba::instalacion()->get_path_temp();
		$grafico = toba_manejador_archivos::path_a_unix( $dir_temp . "/" . mt_rand() . '.dot' );
		$salida = toba_manejador_archivos::path_a_unix( $dir_temp . "/" . $nombre_archivo );
		
		if (!file_put_contents($grafico, $contenido)) {
			toba::logger()->error("No se tiene permiso de escritura sobre el archivo $grafico");
		}
		
		$comando  = ($es_dirigido) ? "dot" : "neato";
		$llamada = $comando . " -Gcharset=latin1 -T". $formato . " -o$salida $grafico";
		
		//Se analiza la salida
		$salida = array();
		$status = 0;
		exec($llamada . " 2>&1 ", $salida, $status);
		if ($status !== 0) {
			$ayuda = toba_parser_ayuda::parsear("[wiki:Referencia/Objetos/ei_esquema esquema]");
			echo "Recuerde que para poder visualizar el $ayuda debe instalar
					<a href='http://www.graphviz.org/Download.php' target='_blank'>GraphViz</a> en el servidor.";
			echo "<pre>";
			echo implode("\n", $salida);
			echo "</pre>";
			toba::logger()->error(implode("\n", $salida));
		}
		
		//Se elimina el archivo .dot
		unlink($grafico);
		return $nombre_archivo;
	}	
	
	/**
	 * En base a la definicion que dejo el componente en el request anterior
	 * se construye el esquema y se le hace un passthru al cliente
	 * @param array $parametros
	 */
	function servicio__mostrar_esquema($parametros = null)
	{
		toba::memoria()->desactivar_reciclado();
		if (!isset($parametros)) {
			if (!isset($this->_memoria['parametros'])) {
				throw new toba_error_seguridad("No se pueden obtener los parámetros");
			}
			$contenido = $this->_memoria['parametros']['contenido'];
			$formato = $this->_memoria['parametros']['formato'];
			$es_dirigido = $this->_memoria['parametros']['es_dirigido'];
		} else {
			$contenido = $parametros['contenido'];
			$formato = $parametros['formato'];
			$es_dirigido = $parametros['es_dirigido'];
		}
	    $tipo_salida = null;
		switch ($formato) {
			case 'png':
				$tipo_salida = "image/png";
			case 'gif':
				$tipo_salida = "image/gif";
			break;
			case 'svg':
				$tipo_salida = "image/svg+xml";				
			break;
		}
		
		$archivo = self::generar_archivo($contenido, $formato, $es_dirigido);
		$dir_temp = toba::instalacion()->get_path_temp();
		$path_completo = $dir_temp . "/" . $archivo;
		if (file_exists($path_completo)) {
			$fp = fopen($path_completo, 'rb');
			if (isset($tipo_salida)) {
				header("Content-type: $tipo_salida");
			}
			header("Content-Length: " . filesize($path_completo));	
			fpassthru($fp);
			fclose($fp);
			unlink($path_completo);
		} else {
			toba::logger()->error("El archivo $path_completo no se encuentra");
		}
	}
	
	//---------------------------------------------------------------
	//------------------------- SALIDA XML --------------------------
	//---------------------------------------------------------------

	/**
	 * Genera el xml del componente
	 * @param boolean $inicial Si el elemento es el primero llamado desde vista_xml
	 * @param string $xmlns Namespace para el componente
	 * @return string XML del componente
	 */
	function vista_xml($inicial=false, $xmlns=null) 
	{
		if ($xmlns) {
			$this->xml_set_ns($xmlns);
		}
		if (isset($this->_memoria['parametros'])) {
			$contenido = $this->_memoria['parametros']['contenido'];
			$formato = $this->_memoria['parametros']['formato'];
			$es_dirigido = $this->_memoria['parametros']['es_dirigido'];
			$archivo = self::generar_archivo($contenido, 'svg', $es_dirigido);
			$dir_temp = toba::instalacion()->get_path_temp();
			$xml = '<'.$this->xml_ns.'img type ="svg"'.$this->xml_ns_url;
			if(isset($this->xml_caption)) {
				$xml .= ' caption="'.$this->xml_caption.'"';
			}
			$xml .= $this->xml_get_att_comunes();
			$xml .= ' src="'.toba_manejador_archivos::path_a_unix( $dir_temp . "/" . $archivo ).'">';
			$xml .= $this->xml_get_elem_comunes();
			$svg = file_get_contents(toba_manejador_archivos::path_a_unix( $dir_temp . "/" . $archivo ));
			$svg = substr($svg, stripos($svg, '<svg'));
			$svg = substr($svg, 0, strripos($svg, '</svg>')+6);
			$enc = mb_detect_encoding($svg);
			if (strtolower(substr($enc, 0, 8)) != 'iso-8859') {
				$svg = iconv($enc, 'iso-8859-1', $svg);
			}
			$xml .= $svg.'</'.$this->xml_ns.'img>';
			return $xml;
		}
	}
	
	/**
	 * Permite definir una leyenda para la imagen
	 * @param string $caption 
	 */
	function xml_set_caption($caption)
	{
		$this->xml_caption = $caption;
	}
}

?>
