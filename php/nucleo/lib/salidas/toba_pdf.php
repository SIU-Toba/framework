<?
//require_once('3ros/dompdf-0.4.4/dompdf_config.inc.php');

class toba_pdf
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
		$this->generar_html();
		/*
		$html = null;
		ob_start();
		$this->generar_html();
		if ( $this->limpiar ) {
			$config = array(
			           'indent'        => true,
			           'output-xhtml'  => true,
			           'wrap'          => 200);			
			$tidy = new tidy();
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
		}*/
	}

	private function generar_html()
	{
		$this->generar_html_encabezado();
		foreach( $this->objetos as $objeto ) {
			$objeto->obtener_pdf( $this );	
		}
		$this->generar_html_pie();
	}

	private function generar_html_encabezado()
	{
		echo "<html><head>";
		$estilo = toba_proyecto::instancia()->get_parametro('estilo');
		echo toba_recurso::link_css($estilo."_impr", 'print');
		echo toba_recurso::link_css($estilo."_impr");
		echo "</head><body>\n";
		/*
		echo "<div class='barra-print' width='100%'>";
		echo "<button onclick='window.print()'>Imprimir ".
					toba_recurso::imagen_apl('impresora.gif',true,null,null,'Imprimir').
			"</button>";		
		echo "</div>";
		*/
	}

	private function generar_html_pie()
	{
		echo "</body></html>";
	}

	//------------------------------------------------------------------------
	//-- Primitivas graficas
	//------------------------------------------------------------------------
	
	function salto_pagina()
	{
		echo "<div class='salto-pagina'></div>\n";			
	}
	
	function titulo( $texto )
	{
		echo "<div class='imp-titulo'>$texto</div>\n";			
	}
	
	function subtitulo( $texto )
	{
		echo "<div class='imp-subtitulo'>$texto</div>\n";			
	}

	function mensaje( $texto )
	{
		echo "<div class='imp-mensaje'>$texto</div>\n";			
	}

	//------------------------------------------------------------------------
	//-- Primitivas de configuracion
	//------------------------------------------------------------------------
		
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