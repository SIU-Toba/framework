<?php

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 * @todo La numeración de páginas no funcionará si se cambia la orientación de la misma. Habría que 
 * implementar un método que en base al tipo de papel y orientación de la página, devuelva las 
 * coordenadas para una correcta visualización de la numeración de páginas.
 * @todo El método insertar_imagen esta implementado con un método en estado beta de la api ezpdf. Usar
 * con discreción.
 */
class toba_vista_xslfo
{
	protected $nombre_archivo = 'archivo.pdf';
	protected $tipo_descarga = 'attachment';
	protected $fop;
	protected $xsl_proyecto;
	protected $xml;
	protected $objetos = array();
	
	function __construct()
	{
		$this->xml = new toba_vista_xml();
		//$this->fop se debe obtener desde la variable en instalacion.ini
		$fop = toba::instalacion()->get_xslfo_fop();
		$this->fop = ($fop)?$fop:(toba_manejador_archivos::es_windows()?'fop.bat':'fop');
		$prxsl = toba::proyecto()->get_path().'/exportaciones/pdf_proyecto.xsl';
		$toxsl = toba::nucleo()->toba_dir().'/exportaciones/pdf.xsl';
		$this->xsl_proyecto = (toba_manejador_archivos::existe_archivo_en_path($prxsl))?$prxsl:$toxsl;
	}
	

	/**
	 * @ignore 
	 */
	function asignar_objetos( $objetos )
	{
		$this->xml->asignar_objetos($objetos);
		$this->objetos = $objetos;
	}
	
	//------------------------------------------------------------------------
	//-- Configuracion
	//------------------------------------------------------------------------
	
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
	 * Permite setear el xsl a utilizar desde fuera de la clase
	 * @param string $xsl Nombre del archivo xsl a utilizar
	 */
	function set_xsl($xsl)
	{
		$this->xsl_proyecto = $xsl;
	}
	
	//------------------------------------------------------------------------
	//-- Generacion del pdf
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{	
		$xml = $this->xml->generar_xml();
		
		//Callback de los eis
		foreach( $this->objetos as $objeto ) {
			if(method_exists($objeto, 'vista_xslfo')) {
				$objeto->vista_xslfo($this);	
			}
		}
		
		if (preg_match('&^https?://.*$&',$this->fop)) {
			$tmp = $this->obtener_pdf($xml);
		} else {
			$tmp = $this->crear_pdf($xml);
		}
		$this->cabecera_http( strlen(ltrim($tmp)) );
		echo ltrim($tmp);
	}
	
	protected function crear_pdf($xml)
	{
  		$fxml = tempnam(toba::nucleo()->toba_dir().'/temp', 'xml');
		if (file_put_contents($fxml, $xml) === false) {
			throw new toba_error("Error al guardar archivo xml", "No es posible escribir en ".$fxml);
		}
		$archivo_pdf = toba::nucleo()->toba_dir().'/temp/'.$this->nombre_archivo;
		$salida = array();
		$status = 0;
		@exec($this->fop.' -xml '.$fxml.' -xsl "'.$this->xsl_proyecto.'" -pdf '.$archivo_pdf, $salida, $status);
		if ($status != 0) {
			throw new toba_error_usuario("Error al ejecutar {$this->fop}", "Status: $status. Mensaje: ".implode("\n", $salida));
		}
		return file_get_contents($archivo_pdf);
	}
	
	protected function obtener_pdf($xml) 
	{
		/*
		  Hay problemas para procesar en servidores remotos... por tema de recursos, es decir,
		  si queremos que el pdf contenga una imagen, o que llame a otro xsl, etc. 
		  Tal vez se podría enviar un tar.gz, un jar, o zip en donde se arme la estructura de 
		  directorios necesaria, y se indique en un archivo tipo INF cual es el path al xsl 
		  inicial, tipo de transformación, etc.

		  habría que hablarlo...

		  por ahora se procesa el xml con el xsl y se envía el fo
		*/
		$xsl = new DOMDocument();
		$xsl->loadXML($this->xsl_proyecto);

		$doc = new DOMDocument();
		$doc->loadXML($xml);

		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xsl);

		//El servicio fop debe tener una funcion procesar que acepte 2 atributos:
		// 1. el archivo fo
		// 2. el tipo de transformación.
		$cliente = new SoapClient($this->fop);
		$pdf = $cliente->procesar(array('fo'=>$xslt->transformToXML($doc), 'tipo'=>'pdf'));
		return base64_decode($pdf);
	}

	protected function cabecera_http( $longuitud )
	{
		header("Cache-Control: private");
  		header("Content-type: application/pdf");
  		header("Content-Length: $longuitud");	
   		header("Content-Disposition: {$this->tipo_descarga}; filename={$this->nombre_archivo}");
  		//header("Accept-Ranges: $longuitud"); 
  		header("Pragma: no-cache");
		header("Expires: 0");
	}
	

}
?>