<?
require_once('3ros/dompdf-0.4.4/dompdf_config.inc.php');

class pdf_html
{
	private $objetos = array();
	private $configuracion = array();
	private $limpiar;
	private $debug = true;
	
	function __construct( $config = null, $limpiar = true )
	{
		if ( isset( $config ) ) {
			if (!isset( $config['papel_tamanio'] ) ) $config['hoja_tamanio'] = 'a4';
			if (!isset( $config['papel_orientacion'] ) ) $config['hoja_orientacion'] = 'portrait';
		} else {
			$config['hoja_tamanio'] = 'a4';
			$config['hoja_orientacion'] = 'portrait';
		}
		$this->configuracion = $config;
		$this->limpiar = $limpiar;	
	}
	
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}

	function generar_salida()
	{
		$html = null;
		ob_start();
		$this->generar_html();
		if ( $this->limpiar ) {
			$config = array(
			           'indent'        => true,
			           'output-xhtml'  => true,
			           'wrap'          => 200);			
			$tidy = new tidy;
			$tidy->parseString( ob_get_clean(), $config );
			$tidy->cleanRepair();
			$html = tidy_get_output( $tidy );
		} else {
			$html = ob_get_clean();
		}
		//Genero el PDF
		if ( $this->debug ) {
			echo $html;	
		} else {
			ini_set("memory_limit", "16M");
			$dompdf = new DOMPDF();
			$dompdf->load_html( $html );
			$dompdf->set_paper('a4', 'portrait');
			$dompdf->render();
			$dompdf->stream("out.pdf");
		}
	}

	private function generar_html()
	{
		$this->generar_html_encabezado();
		foreach( $this->objetos as $objeto ) {
			$objeto->obtener_pdf();	
		}
		$this->generar_html_pie();
	}

	private function generar_html_encabezado()
	{
		echo "<html><head>";
		if( $this->debug ) {
			echo recurso::link_css(apex_proyecto_estilo."_impr");						
		} else {
			echo "<link href='f:/toba_trunk/www/css/toba_impr.css' rel='stylesheet' type='text/css'/>";	
		}
		echo "</head><body>\n";
	}

	private function generar_html_pie()
	{
		echo "</body></html>";
	}


	
	function set_papel_tamanio( $tamanio )
	{
		$this->configuracion['papel_tamanio'] = $tamanio;
	}

	function set_papel_orientacion( $orientacion )
	{
		$this->configuracion['papel_orientacion'] = $orientacion;
	}

}
?>