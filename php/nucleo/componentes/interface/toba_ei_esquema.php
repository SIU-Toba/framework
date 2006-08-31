<?
require_once("toba_ei.php");						//Ancestro de todos los OE
require_once("lib/manejador_archivos.php");

/**
 * Genera un esquema utilizando GraphViz
 * @package Objetos
 * @subpackage Ei
 */
class toba_ei_esquema extends toba_ei
{
	protected $prefijo = 'esq';	
	protected $alto;
	protected $ancho;
	protected $contenido;				// Instrucciones GraphViz
	protected $archivo_generado;		// Archivo generado por las instrucciones
	
	function __construct($id)
	{
		parent::__construct($id);
		$this->alto = isset($this->info_esquema['alto']) ?  $this->info_esquema['alto'] : null;
		$this->ancho = isset($this->info_esquema['ancho']) ?  $this->info_esquema['ancho'] : null;
	}

	function set_datos($datos)
	{
		if (isset($datos)) {
			$this->contenido = $datos;	
		}
	}
	
	function generar_html($cabecera=true)
	{
		echo "<table class='objeto-base' id='{$this->objeto_js}_cont'>";
		echo "<tr><td>";
		$this->barra_superior(null, true,"ei-esquema-barra-sup");
		echo "</td></tr>\n";
		$colapsado = (isset($this->colapsado) && $this->colapsado) ? "style='display:none'" : "";		
		echo "<tr><td><div $colapsado id='cuerpo_{$this->objeto_js}'>";
		//Campo de sincronizacion con JS
		echo toba_form::hidden($this->submit, '');
		if (isset($this->contenido)) {
			//Se arma el archivo .dot
			toba::get_logger()->debug($this->get_txt() . " [ Diagrama ]:\n$this->contenido", 'toba');
			$this->generar_esquema($this->contenido, $this->info_esquema['formato'], 
									$this->info_esquema['dirigido'], $this->ancho,
									$this->alto);
		}
		$this->generar_botones();
		echo "</div></td></tr>\n";
		echo "</table>\n";
	}
	
	function generar_esquema($contenido, $formato, $es_dirigido=true, $ancho=null, $alto=null)
	{
		$parametros = array("contenido" => $contenido, 
							'formato' => $formato,
							'es_dirigido' => $es_dirigido);
		//Vinculo a un item que hace el passthru y borra el archivo
		$destino = array($this->id);
		$this->memoria['parametros'] = $parametros;
		$url = toba::get_vinculador()->crear_autovinculo(array(), array('servicio' => 'mostrar_esquema', 
																		'objetos_destino' => $destino));
		$this->generar_sentencia_incrustacion($url, $formato, $ancho, $alto);
	}

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
				toba_js::cargar_consumos_globales(array("utilidades/svglib"));
				echo toba_js::abrir();
				echo "//aviso_instalacion_svg()";
				echo toba_js::cerrar();				
				echo "<embed src='$url' type='image/svg+xml' $ancho $alto palette='foreground' pluginspage='http://www.adobe.com/svg/viewer/install/auto'>\n";
				echo "</embed>"; 
			break;
		}
	}
	
	static function generar_archivo($contenido, $formato, $es_dirigido = true)
	{
		$nombre_archivo = mt_rand() . '.' . $formato;
		$dir_temp = toba::get_hilo()->obtener_path_temp();
		$grafico = manejador_archivos::path_a_unix( $dir_temp . "/" . mt_rand() . '.dot' );
		$salida = manejador_archivos::path_a_unix( $dir_temp . "/" . $nombre_archivo );
		
		file_put_contents($grafico, $contenido);
		
		$comando  = ($es_dirigido) ? "dot" : "neato";
		$llamada = $comando . " -T". $formato . " -o$salida $grafico";
		
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
			toba::get_logger()->error(implode("\n", $salida));
		}
		
		//Se elimina el archivo .dot
		unlink($grafico);
		return $nombre_archivo;
	}	
	
	/**
	 * En base a la definicion que dejo el componente en el request anterior
	 * se construye el esquema y se le hace un passthru
	 */
	function servicio__mostrar_esquema($parametros = null)
	{
		if (!isset($parametros)) {
			if (!isset($this->memoria['parametros'])) {
				throw new toba_excepcion("No se pueden obtener los parámetros");
			}
			$contenido = $this->memoria['parametros']['contenido'];
			$formato = $this->memoria['parametros']['formato'];
			$es_dirigido = $this->memoria['parametros']['es_dirigido'];
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
		$dir_temp = toba::get_hilo()->obtener_path_temp();
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
			toba::get_logger()->error("El archivo $path_completo no se encuentra");
		}
	}

}
//################################################################################
?>
