<?php
require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 * @todo La numeración de páginas no funcionará si se cambia la orientación de la misma. Habría que 
 * implementar un método que en base al tipo de papel y orientación de la página, devuelva las 
 * coordenadas para una correcta visualización de la numeración de páginas.
 * @todo El método insertar_imagen esta implementado con un método en estado beta de la api ezpdf. Usar
 * con discreción.
 */
class toba_vista_pdf
{
	protected $objetos = array();
	protected $configuracion = array('hoja_tamanio' => 'a4', 'hoja_orientacion' => 'portrait');
	/**
	 * @var Cezpdf
	 */
	protected $pdf;	
	protected $texto_pie;
	protected $nombre_archivo = 'archivo.pdf';
	protected $tipo_descarga = 'attachment';
	protected $temp_salida;
	
	function __construct()
	{
		$this->inicializar();
	}
	
	/**
	 * @ventana Lugar donde se puede cambiar alguna configuracion del objeto Cezpdf
	 */
	function inicializar()
	{
		$this->pdf = new Cezpdf($this->configuracion['hoja_tamanio'], $this->configuracion['hoja_orientacion']);
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
	 * Cambia el tamaño del papel, se debe llamar a un inicializar 
	 * para que tenga efecto sobre una hoja ya creada (la inicial por ejemplo)
	 * @param string $tamanio Tipo de página (por defecto a4)
	 */
	function set_papel_tamanio( $tamanio )
	{
		$this->configuracion['hoja_tamanio'] = $tamanio;
	}

	/**
	 * Cambia la orientacion del papel, se debe llamar a un inicializar 
	 * para que tenga efecto sobre una hoja ya creada (la inicial por ejemplo)
	 * @param string $orientacion portrait o landscape
	 */
	function set_papel_orientacion($orientacion)
	{
		$this->configuracion['hoja_orientacion'] = $orientacion;
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
		$this->parar_numeracion_paginas();
		return $this->crear_pdf();
	}
	
	/**
	 * Devuelve el objeto pdf para manipular a gusto y piachere.
	 * @return Cezpdf
	 */
	function get_pdf()
	{
		return $this->pdf;
	}
	
	/**
	 * Indica el momento en el que se comienzan a numerar las páginas.
	 * @param string $posicion Indica la posicion de la numeracion de las paginas (left,right)
	 * @param string $formato Como se visualizará la numeración de las páginas. Los formatos posibles
	 * pueden variar siemrpe respetando el nombre de las variables. Es posible por ejemplo setear
	 * $formato='{PAGENUM}'. Solo se visualizará el número de página.
	 */
	function numerar_paginas($posicion='right',$formato='{PAGENUM} de {TOTALPAGENUM}')
	{
		$this->pdf->ezStartPageNumbers(500,20,8,$posicion,$formato,1);
	}
	
	function parar_numeracion_paginas(){
		$this->pdf->ezStopPageNumbers(1,1);
	}
	
	protected function crear_pdf()
	{
		toba::logger()->debug("Mensajes PDF: ".$this->pdf->messages);
  		$this->temp_salida = $this->pdf->ezOutput(0);
	}

	/**
	 * @ignore
	 */
	function enviar_archivo()
	{
   		$this->cabecera_http( strlen(ltrim($this->temp_salida)) );
   		echo ltrim($this->temp_salida);
	}
	
	protected function cabecera_http( $longitud )
	{
		toba_http::headers_download($this->tipo_descarga, $this->nombre_archivo, $longitud);
	}

	//------------------------------------------------------------------------
	//-- Primitivas graficas
	//------------------------------------------------------------------------
	
	/**
	 * Dado un porcentaje, retorna el valor absoluto del ancho de la pagina segun sus medidas actuales
	 * @param int $porcentaje
	 */
	function get_ancho($porcentaje)
	{
		$ancho_visible = ($this->pdf->ez['pageWidth'] - $this->pdf->ez['rightMargin']) - $this->pdf->ez['leftMargin'];
		return $ancho_visible * $porcentaje / 100;
	}
	
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
	 * Inserta una imagen siguiendo el flujo del texto en el documento.
	 * @version OJO! ezImage es un método en estado beta de la clase ezpdf y por lo que pude ver
	 * solo funciona con archivos jpg. Utilizar con cuidado.
	 * @param string $archivo full path de la imagen
	 * @param string $alineacion left, right, center
	 */
	function insertar_imagen($archivo,$pad=5,$width=0,$alineacion='left')
	{
		$this->pdf->ezImage("$archivo",$pad,$width,'none',$alineacion); 	
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
	function tabla( $datos, $ver_titulos_col=false, $tamanio=8, $opciones=array() ){
		$ver_tit_col = $ver_titulos_col? 1 : 0;
		$texto_tit_col = isset($datos['titulos_columnas'])? $datos['titulos_columnas'] : '';
		$texto_titulo_tabla = isset($datos['titulo_tabla'])? $datos['titulo_tabla'] : '';
		$opciones_def = array(
						'splitRows'=>0,
						'rowGap' => 1,
						'showHeadings' => $ver_tit_col,	
						'titleFontSize' => 9,
						'fontSize' => $tamanio,
						'shadeCol' => array(0.9,0.9,0.9),
						'outerLineThickness' => 0.7,
						'innerLineThickness' => 0.7,
	                	'xOrientation' => 'center',
	                	'maxWidth' => $this->get_ancho(100)
		            );
		$opciones = array_merge($opciones_def, $opciones);
		$this->pdf->ezTable($datos['datos_tabla'], $texto_tit_col, $texto_titulo_tabla, $opciones);
	}

}
?>