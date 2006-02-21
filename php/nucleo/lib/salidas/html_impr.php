<?
require_once('impresion.php');

class html_impr implements impresion
{
	private $objetos = array();
	private $configuracion = array();
	private $limpiar;
	private $debug = true;
	
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}

	function generar_salida()
	{
		$this->generar_html_encabezado();
		foreach( $this->objetos as $objeto ) {
			$objeto->vista_impresion( $this );	
		}
		$this->generar_html_pie();
	}

	private function generar_html_encabezado()
	{
		echo "<html><head>";
		echo recurso::link_css(apex_proyecto_estilo."_impr", 'print');
		echo recurso::link_css(apex_proyecto_estilo."_impr");
		echo "</head><body>\n";
		/*
		echo "<div class='barra-print' width='100%'>";
		echo "<button onclick='window.print()'>Imprimir ".
					recurso::imagen_apl('impresora.gif',true,null,null,'Imprimir').
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
}
?>