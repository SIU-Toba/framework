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
	protected $_tipo;
	protected $_alto;
	protected $_ancho;
	protected $_contenido;	// Instrucciones GraphViz
	/**
	 * @var toba_ei_grafico_conf
	 */
	protected $_conf;
	
	protected $_archivo_generado;  // Archivo generado por las instrucciones
	protected $s__path;

	final function __construct($id)
	{
		parent::__construct($id);
		$this->_alto  = get_var($this->_info_grafico['alto'], 300);
		$this->_ancho = get_var($this->_info_grafico['ancho'], 650);
		$this->_tipo  = get_var($this->_info_grafico['grafico']);
		$this->ini_conf();
		//TODO: Hack para navegacion ajax con windows
		toba_ci::set_navegacion_ajax(false);
	}

	function ini()
	{
		// Instancia $this->grafico
	}

	protected function ini_conf()
	{
		// pie | bar | lin | otro
		switch ($this->_tipo) {
			case 'pie':
				$this->_conf = new toba_ei_grafico_conf_torta($this->_ancho, $this->_alto);
				break;
			case 'bar':
				$this->_conf = new toba_ei_grafico_conf_barras($this->_ancho, $this->_alto);
			default: break;
		}
	}

	/**
	 *
	 * @return toba_ei_grafico_conf
	 */
	function conf($id_serie = null)
	{
		if (!is_null($id_serie)) {
			$this->_conf->set_id_serie($id_serie);
		}
		
		return $this->_conf;
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
		// Se lee un flag del editor para ver si se aplica o no la configuración global
		$this->conf()->aplicar_conf_global();
	}

	function generar_html()
	{

		// Se genera un grafico y va a un archivo
		// dejar el path en $this->s__path
		//$ancho = '';
		//if (isset($this->_ancho)) {
		//	$ancho = "width ='$this->_ancho'";
		//}
//		ei_arbol($this->_info_grafico, "INFO GRAFICO");
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
		
		$this->_conf->generar_imagen();
		$this->s__path = $this->_conf->get_path();

		$destino = array($this->_id);
		$url = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'eliminar_imagen',
					'objetos_destino' => $destino));
		echo "<img src='$url' $this->_ancho $this->_alto border='0'>";

		//$this->generar_botones();
		echo "</div></td></tr>\n";
		echo "</table>\n";
	}

	/**
	 * Elimina la imagen generada del gráfico
	 */
	function servicio__eliminar_imagen($parametros = null)
	{
		$handle = fopen($this->s__path, 'rb');
		fpassthru($handle);
		fclose($handle);
		unlink($this->s__path);
	}

}

?>