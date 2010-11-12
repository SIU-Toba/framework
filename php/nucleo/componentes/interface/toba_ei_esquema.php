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
									$this->_alto);
		}
		$this->generar_botones();
		echo "</div></td></tr>\n";
		echo "</table>\n";
	}
	
	/**
	 * @ignore 
	 */
	protected function generar_esquema($contenido, $formato, $es_dirigido=true, $ancho=null, $alto=null)
	{
		$parametros = array("contenido" => $contenido, 
							'formato' => $formato,
							'es_dirigido' => $es_dirigido);
		//Vinculo a un item que hace el passthru y borra el archivo
		$destino = array($this->_id);
		$this->_memoria['parametros'] = $parametros;
		$url = toba::vinculador()->get_url(null,null,array(), array('servicio' => 'mostrar_esquema', 
																		'objetos_destino' => $destino));
		$this->generar_sentencia_incrustacion($url, $formato, $ancho, $alto);
	}

	/**
	 * Genera el tag HTML necesario para incluir el archivo generado por GrahpViz
	 * @param string $url
	 * @param string $formato
	 * @param string $ancho
	 * @param string $alto
	 */
	static function generar_sentencia_incrustacion($url, $formato, $ancho=null, $alto=null)
	{
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
