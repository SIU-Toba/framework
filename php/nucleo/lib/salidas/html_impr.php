<?
require_once('impresion_toba.php');

class html_impr implements impresion_toba
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
		$estilo = info_proyecto::instancia()->get_parametro('estilo');
		echo recurso::link_css($estilo."_impr", 'print');
		echo recurso::link_css($estilo."_impr");
		echo "<style type='text/css' media='print'>
			.barra-impresion {
				display: none;				
			}
			</style>
			<style type='text/css'>
			.barra-impresion {
				padding: 7px;
				background-color: #cccccc;
				text-align: right;
			}
			.marco-impresion {
				padding: 10px;
			}
			</style>\n";
		js::cargar_consumos_basicos();
		echo "</head><body>\n";
		echo "<div class='barra-impresion'>";
		echo "<button onclick='window.print()'>".
					recurso::imagen_apl('impresora.gif',true,null,null).
			"    Imprimir</button>";		
		echo "</div>";
		echo "<div class='marco-impresion'>";
	}

	private function generar_html_pie()
	{
		echo "</div>";
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
