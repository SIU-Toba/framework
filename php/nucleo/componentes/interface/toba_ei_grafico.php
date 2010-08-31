<?php
/**
 * Genera un Grafico
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei ei
 * @wiki Referencia/Objetos/ei_grafico
 *
 *
 * 	function __conf($grafico)
  {
  // CAso normal
  $grafico->conf()->agregar_serie('Pepe', array(1,2,5,34,6,76,78,7));
  - El conf se instancia en una factory que aplica configuraciones globales del toba o del proyecto

  // Parametro extraño
  //$grafico->renderer()->

  // Grafico de otro tipo
  $g = new jpga
  $grafico->instan

  }
 *
 *
 */
class toba_ei_grafico extends toba_ei
{

	protected $_prefijo = 'esq';
	protected $_alto;
	protected $_ancho;
	protected $_contenido;	// Instrucciones GraphViz
	protected $_archivo_generado;  // Archivo generado por las instrucciones

	final function __construct($id)
	{
		parent::__construct($id);
		$this->_alto = isset($this->_info_grafico['alto']) ? $this->_info_grafico['alto'] : null;
		$this->_ancho = isset($this->_info_grafico['ancho']) ? $this->_info_grafico['ancho'] : null;
		//TODO: Hack para navegacion ajax con windows
		toba_ci::set_navegacion_ajax(false);
	}

	function ini()
	{
		// Instancia $this->grafico
	}

	/**
	 * Cambia el esquema actual
	 * @param string $datos Esquema Graphviz
	 */
	function set_datos($datos)
	{
		// PIE array una dimensiones
		// LINE N dim
		// BAR N DIM
		if (isset($datos)) {
			$this->_contenido = $datos;
			$this->_memoria['parametros'] = $parametros;
		}
		// Se lo llena con datos
	}

	function generar_html()
	{

		// Se genera un grafico y va a un archivo
		// dejar el path en $this->s__path
		//$ancho = '';
		//if (isset($this->_ancho)) {
		//	$ancho = "width ='$this->_ancho'";
		//}
		ei_arbol($this->_info_grafico, "INFO GRAFICO");
		echo "\n<table class='ei-base ei-esquema-base'>\n";
		echo"<tr><td style='padding:0'>\n";
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true, "ei-esquema-barra-sup");
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "style='display:none'" : "";
		echo "<div $colapsado id='cuerpo_{$this->objeto_js}'>";
		//Campo de sincronizacion con JS
		echo toba_form::hidden($this->_submit, '');

		if (isset($this->_contenido)) {
			echo $this->_contenido;
		}
		ei_arbol($this->_info_grafico, "INFO GRAFICO");

		$destino = array($this->_id);
		$url = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'mostrar_grafico',
					'objetos_destino' => $destino));
		echo "<img src='$url' $ancho $alto border='0'>";

		//$this->generar_botones();
		echo "</div></td></tr>\n";
		echo "</table>\n";
	}

	/**
	 * En base a la definicion que dejo el componente en el request anterior
	 * se construye el esquema y se le hace un passthru al cliente
	 * @param array $parametros
	 */
	function servicio__mostrar_grafico($parametros = null)
	{
		// Se lee $this->s__path y se hace el passthru
		// elimino el archivo


		ob_start();
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
		break;

		header("Content-type: $tipo_salida");
		header("Content-Length: " . filesize($path_completo));
		fpassthru($fp);

		ob_clean();

		ob_flush();

		toba::logger()->error("El archivo $path_completo no se encuentra");
	}

}

?>