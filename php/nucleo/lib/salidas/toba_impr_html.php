<?php
/**
 * Genera un HTML básico pensado para impresión con un browser
 * Toma un conjunto de componentes e les invoca el método <pre>vista_impresion</pre>
 * @package SalidaGrafica
 */
class toba_impr_html implements toba_impresion
{
	private $objetos = array();
	private $configuracion = array();
	private $limpiar;
	private $debug = true;
	
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}

	/**
	 * Envia al browser el HTML con estructura de impresión
	 */
	function generar_salida()
	{
	    header('Content-Type: text/html; charset=iso-8859-1', true); 
		$this->generar_html_encabezado();
		foreach( $this->objetos as $objeto ) {
			$objeto->vista_impresion( $this );	
		}
		$this->generar_html_pie();
	}

	protected function generar_html_encabezado()
	{
		echo "<html><head>";
		$estilo = toba::proyecto()->get_parametro('estilo');
		echo toba_recurso::link_css("toba_impr", 'screen');
		echo toba_recurso::link_css("toba_impr", 'print');
		 echo "<style type='text/css' media='print'>
			.barra-impresion {
				display: none;				
			}
			</style>\n";
		toba_js::cargar_consumos_basicos();
		echo "</head><body>\n";
		echo "<div class='barra-impresion'>";
		echo "<button onclick='window.print()'>".
					toba_recurso::imagen_toba('impresora.gif',true,null,null).
			"    Imprimir</button>";		
		echo "</div>";
		echo $this->encabezado();
	}

	private function generar_html_pie()
	{
		$this->pie();
		echo "</div>";
		echo "</body></html>";
	}

	protected function encabezado()
	{
	}
	
	protected function pie()
	{
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
		if( trim($texto) != '' ) {
			echo "<div class='imp-titulo'>$texto</div>\n";			
		}
	}
	
	function subtitulo( $texto )
	{
		if( trim($texto) != '' ) {
			echo "<div class='imp-subtitulo'>$texto</div>\n";			
		}
	}

	function mensaje( $texto )
	{
		if( trim($texto) != '' ) {
			echo "<div class='imp-mensaje'>$texto</div>\n";			
		}
	}
}
?>