<?php
require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 */
class toba_vista_pdf
{
	protected $objetos = array();
	protected $configuracion = array('hoja_tamanio' => 'a4', 'hoja_orientacion' => 'portrait');
	protected $pdf;
	protected $texto_pie;
	protected $nombre_archivo = 'archivo.pdf';
	protected $tipo_descarga = 'attachment';
	
	function __construct()
	{
		$this->pdf = new Cezpdf($this->configuracion['hoja_tamanio'], $this->configuracion['hoja_orientacion']);
		$this->inicializar();
	}
	
	/**
	 * @ventana Lugar donde se puede cambiar alguna configuracion del objeto Cezpdf
	 */
	function inicializar(){
		$this->set_pdf_fuente();
	}
	
	/**
	 * @ignore 
	 */
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}
	
	//------------------------------------------------------------------------
	//-- Configuracion
	//------------------------------------------------------------------------
	/**
	 * 
	 */
	
	/**
	 * Genera el encabezado y pie del pdf
	 * @todo Implementar
	 */	
	protected function generar_pdf_encabezado_pie(){}
	
	
	/**
	 * @ignore
	 * @todo Implementar junto a un set_texto_encabezado
	 */
	function set_texto_pie( $texto ){
		$this->texto_pie = 	$texto;
	}
	
	/**
	 * @param string $nombre Nombre del archivo pdf + la extension del mismo (pdf)
	 */
	
	function set_nombre_archivo( $nombre )
	{
		$this->nombre_archivo = $nombre;
	}
	
	/**
	 * Permite setear el tipo de descarga pdf desde el browser, inline o attachment
	 * @param string $tipo inline o attachment
	 */
	function set_tipo_descarga( $tipo )
	{
		$this->tipo_salida = $tipo;
	}
	
	/**
	 * @param string $tamanio Tipo de página (por defecto a4)
	 */
	function set_papel_tamanio( $tamanio )
	{
		$this->configuracion['papel_tamanio'] = $tamanio;
	}

	/**
	 * @param string $orientacion portrait o landscape
	 */
	function set_papel_orientacion( $orientacion )
	{
		$this->configuracion['papel_orientacion'] = $orientacion;
	}
	
	/**
	 * Cambia la fuente para futuras inserciones de texto
	 * @param string $fuente Nombre del archivo de la fuente (estan en la carpeta fonts de la libreria ezpdf)
	 */
	function set_pdf_fuente( $fuente='Helvetica.afm' )
	{
		$this->configuracion['fuente'] = $fuente;
		$this->pdf->selectFont(toba_dir() . '/php/3ros/ezpdf/fonts/' . $this->configuracion['fuente']);
	}
	

	//------------------------------------------------------------------------
	//-- Generacion del pdf
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{	
		$this->generar_pdf_encabezado_pie();
		foreach( $this->objetos as $objeto ) {
			$objeto->vista_pdf( $this );	
		}
		$this->crear_pdf();		
	}
	
	protected function crear_pdf( $opciones=null )
	{
		if (!is_array($opciones)){
    		$opciones = array();
  		}
  		if ( isset($opciones['compress']) && $opciones['compress']==0){
    		$tmp = $this->pdf->output(1);
  		} else {
    		$tmp = $this->pdf->output();
  		}
   		$this->cabecera_http( strlen(ltrim($tmp)) );
   		echo ltrim($tmp);
	}
	
	protected function cabecera_http( $longuitud )
	{
  		header("Content-type: application/pdf");
  		header("Content-Length: $longuitud");	
   		header("Content-Disposition: {$this->tipo_descarga}; filename={$this->nombre_archivo}");
  		//header("Accept-Ranges: $longuitud"); 
  		header("Pragma: no-cache");
		header("Expires: 0");
	}

	//------------------------------------------------------------------------
	//-- Primitivas graficas
	//------------------------------------------------------------------------
	
	function salto_linea() 
	{
		$this->pdf->ezText("\n");
	}
	
	function separacion( $espacio=6 ) 
	{
		$this->pdf->ezSetDy( -$espacio, 'makeSpace' );
	}
	
	function salto_pagina() 
	{
		$this->pdf->ezNewPage();
	}
	
	function titulo( $texto )
	{
		$this->pdf->ezText("<b>$texto</b>", 11, array( 'justification' => 'center' ));					
	}
	
	function subtitulo( $texto )
	{
		$this->pdf->ezText("<b>$texto</b>", 9, array( 'justification' => 'left' ));					
	}

	function mensaje( $texto )
	{
		$this->pdf->ezText("<i>$texto</i>", 8, array( 'justification' => 'left' ));					
	}
	
	function texto( $texto, $tamanio=8, $opciones=array( 'justification' => 'left'))
	{
		$this->pdf->ezText($texto, $tamanio, $opciones);	
	}
	
	/**
	 * Genera una tabla para impresion en pdf
	 *
	 * @param array $datos arreglo asociativo el cual contiene:
	 * 'datos_tabla' => arreglo estilo recordset, donde cada elemento del mismo es un arreglo asociativo 'nombre_columna' => 'valor'
	 * 'titulo_tabla' => texto con el titulo de la tabla
	 * 'titulos_columnas' => arreglo asociativo 'nombre_columna' => 'descripcion_columna'
	 * @param boolean $ver_titulos_col indica si se imprimiran los titulos de las columnas, por defecto no
	 * @param integer $tamanio tamaño de la letra de la tabla
	 * @param array $opciones arreglo asociativo con estilos de la tabla
	 */
	function tabla( $datos, $ver_titulos_col=false, $tamanio=8, $opciones=null ){
		$ver_tit_col = $ver_titulos_col? 1 : 0;
		$texto_tit_col = isset($datos['titulos_columnas'])? $datos['titulos_columnas'] : array('clave' => '', 'valor' => '');
		$texto_titulo_tabla = isset($datos['titulo_tabla'])? $datos['titulo_tabla'] : '';
		
		if (!isset($opciones)) {
			$opciones = array(
						'splitRows'=>0,
						'rowGap' => 1,
						'showHeadings' => $ver_tit_col,	
						'titleFontSize' => 9,
						'fontSize' => $tamanio,
						'shadeCol' => array(0.9,0.9,0.9),
						'outerLineThickness' => 0.7,
						'innerLineThickness' => 0.7,
	                	'xOrientation' => 'center',
	                	'width' => 500
		            );
		}

		$this->pdf->ezTable($datos['datos_tabla'], $texto_tit_col, $texto_titulo_tabla, $opciones);
	}

}
?>