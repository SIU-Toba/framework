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
	protected $temp_salida;
	protected $callback_preproceso;
	protected $xml_proyecto;
	
	function __construct()
	{
		$this->xml = new toba_vista_xml();

		//$this->fop se debe obtener desde la variable en instalacion.ini
		$fop = toba::instalacion()->get_xslfo_fop();
		$this->fop = $fop ? $fop : (toba_manejador_archivos::es_windows() ? 'fop.bat' : 'fop');
		$this->fop = toba_manejador_archivos::path_a_plataforma($this->fop);

		$prxsl = toba::proyecto()->get_path().'/exportaciones/pdf_proyecto.xsl';
		$toxsl = toba::nucleo()->toba_dir().'/exportaciones/pdf.xsl';
		$this->xsl_proyecto = toba_manejador_archivos::existe_archivo_en_path($prxsl) ? $prxsl : $toxsl;
		$this->xsl_proyecto = toba_manejador_archivos::path_a_plataforma($this->xsl_proyecto);
		$this->xml_proyecto = toba_manejador_archivos::path_a_plataforma(tempnam(toba::nucleo()->toba_dir().'/temp', 'xml'));
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

	/**
	 * @param toba_vista_xslfo_callback_generacion $object 
	 * @ignore
	 */
	function set_callback_preproceso($object)
	{
		$this->callback_preproceso = $object;		
	}

	//------------------------------------------------------------------------
	//-- Obtencion de datos
	//------------------------------------------------------------------------
	/**
	 * Devuelve el nombre del archivo pdf destino con la ruta absoluta
	 * @return string 
	 */
	function get_nombre_archivo_destino()
	{
		return toba_manejador_archivos::path_a_plataforma(toba::nucleo()->toba_dir().'/temp/'.$this->nombre_archivo);		
	}
	
	/**
	 * Devuelve el nombre del archivo xml con ruta absoluta
	 * @return string
	 */
	function get_nombre_archivo_xml()
	{
		return $this->xml_proyecto;
	}

	/**
	 * Devuelve el nombre del archivo xsl con ruta absoluta
	 * @return string
	 */
	function get_nombre_archivo_xsl()
	{
		return $this->xsl_proyecto;
	}
	
	/**
	 * Devuelve el comando para realizar una llamada a fop
	 * @return string
	 */
	function get_path_to_fop()
	{
		return $this->fop;
	}	
	
	/**
	 * Devuelve una instancia de la clase que maneja la vista_xml de los componentes
	 * @return toba_vista_xml  
	 */
	function get_manejador_vista_xml()
	{
		return $this->xml;
	}
	
	//------------------------------------------------------------------------
	//-- Generacion del pdf
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{			
		//Callback de los eis
		foreach( $this->objetos as $objeto ) {
			if(method_exists($objeto, 'vista_xslfo')) {
				$objeto->vista_xslfo($this);	
			}
		}
		$xml = $this->xml->generar_xml();
		if (preg_match('&^https?://.*$&',$this->fop)) {
			$this->temp_salida = $this->obtener_pdf($xml);
		} else {
			$this->temp_salida = $this->crear_pdf($xml);
		}
	}

	/**
	 * @ignore
	 */
	function enviar_archivo()
	{	
		$this->cabecera_http( strlen(ltrim($this->temp_salida)) );
		echo ltrim($this->temp_salida);
	}
	
	/**
	 * @ignore 
	 */
	protected function crear_pdf($xml)
	{
		//Escribo el xml
  		$fxml = $this->get_nombre_archivo_xml();
		if (file_put_contents($fxml, $xml) === false) {
			throw new toba_error("Error al guardar archivo xml", "No es posible escribir en ".$fxml);
		}

		//Si existe el archivo pdf, lo borro.
		$archivo_pdf = $this->get_nombre_archivo_destino();
		if (file_exists($archivo_pdf)) {
			unlink($archivo_pdf);
		}

		//Si se configuro una forma alternativa de generacion mediante fop
		if (isset($this->callback_preproceso)) {
			$this->callback_preproceso->generar($this);
		} else {
			$comando = $this->fop.' -xml '.$fxml.' -xsl '.$this->xsl_proyecto.' -pdf '.$archivo_pdf;
			shell_exec($comando);		
		}
		
		if (!file_exists($archivo_pdf)) {
			throw new toba_error_usuario("Error al ejecutar el comando '".$comando."'");
		}
		if (file_exists($fxml)) {
			unlink($fxml);
		}

		return file_get_contents($archivo_pdf);
	}
	
	/**
	 * @ignore 
	 */
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

	/**
	 * @ignore 
	 */
	protected function cabecera_http( $longitud )
	{
		toba_http::headers_download($this->tipo_descarga, $this->nombre_archivo, $longitud);
	}
	

}
?>
