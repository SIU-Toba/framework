<?php
/**
 * Genera un Grafico
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei ei
 * @wiki Referencia/Objetos/ei_grafico
 *
 */
class toba_ei_grafico extends toba_ei
{
	protected $_tipo;
	protected $_alto;
	protected $_ancho;

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
				break;
			case 'lin':
				$this->_conf = new toba_ei_grafico_conf_lineas($this->_ancho, $this->_alto);
				break;
			case 'otro':
				$this->_conf = new toba_ei_grafico_conf();
			default: break;
		}
	}

	/**
	 *
	 * @return toba_ei_grafico_conf
	 */
	function conf()
	{		
		return $this->_conf;
	}

	/**
	 * Cambia el esquema actual
	 * @param string $datos 
	 */
	function set_datos($datos)
	{}

	function generar_html()
	{
		echo "\n<table class='ei-base ei-esquema-base'>\n";
		echo"<tr><td style='padding:0'>\n";
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true, "ei-esquema-barra-sup");
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "style='display:none'" : "";
		echo "<div $colapsado id='cuerpo_{$this->objeto_js}'>";
		//Campo de sincronizacion con JS
		echo toba_form::hidden($this->_submit, '');

		$this->s__path = toba_dir().'/temp/'.uniqid().'.png';
		try {
			$this->_conf->imagen__generar($this->s__path);
		} catch (JpGraphException $e) {

			throw new toba_error("TOBA EI GRAFICO: Error en la librería jpgraph. 
				El error reportado fue el siguiente: '".$e->getMessage()."'. Si este
				es un error de fuentes intente definir el path de las fuentes en
				su sistema a través de la entrada fonts_path en el archivo instancia.ini.
				Ejemplo: 'fonts_path = /usr/share/fonts/truetype/msttcorefonts/'");
		}
		

		$destino = array($this->_id);
		$url = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'mostrar_imagen',
					'objetos_destino' => $destino));
		echo "<img src='$url' $this->_ancho $this->_alto border='0'>";

		//$this->generar_botones();
		echo "</div></td></tr>\n";
		echo "</table>\n";
	}

	/**
	 * Elimina la imagen generada del gráfico
	 */
	function servicio__mostrar_imagen($parametros = null)
	{
		$handle = fopen($this->s__path, 'rb');
		fpassthru($handle);
		fclose($handle);
		unlink($this->s__path);
	}
	
	/**
	 * Realiza la exportacion a pdf del gráfico
	 * @param toba_vista_pdf $salida 
	 */
	function vista_pdf(toba_vista_pdf $salida )
	{
		$this->_conf->imagen__generar($this->s__path);
		$salida->insertar_imagen($this->s__path);
	}
}

?>