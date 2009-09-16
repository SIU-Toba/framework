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
class toba_vista_xml
{
	protected $objetos = array();
	protected $tipo_descarga = 'attachment';
	protected $nombre_archivo = 'archivo.xml';
	
	function __construct()
	{
	}
	

	/**
	 * @ignore 
	 */
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}
	
	/**
	 * @param string $nombre Nombre del archivo pdf + la extension del mismo (pdf)
	 */
	
	function set_nombre_archivo( $nombre )
	{
		$this->nombre_archivo = $nombre;
	}
	

	//------------------------------------------------------------------------
	//-- Generacion del xml
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{	
		$xml = $this->generar_xml();
		$this->cabecera_http( strlen(ltrim($xml)) );
		echo ltrim($xml);
	}
	

	function generar_xml() {
		$xml = '<?xml version="1.0" encoding="ISO-8859-1"?><raiz>';
		foreach( $this->objetos as $objeto ) {
			if(method_exists($objeto, 'vista_xml')) {
				$xml .= $objeto->vista_xml(true);	
			}
		}
		$xml .= '</raiz>';
		return $xml;
	}

	protected function cabecera_http( $longuitud )
	{
		header("Cache-Control: private");
  		header("Content-type: text/xml");
  		header("Content-Length: $longuitud");	
   		header("Content-Disposition: {$this->tipo_descarga}; filename={$this->nombre_archivo}");
  		//header("Accept-Ranges: $longuitud"); 
  		header("Pragma: no-cache");
		header("Expires: 0");
	}
	

}
?>