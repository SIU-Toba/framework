<?php

/**
 * Genera un pdf a trav�s de una api b�sica
 * @package SalidaGrafica
 * @todo La numeraci�n de p�ginas no funcionar� si se cambia la orientaci�n de la misma. Habr�a que 
 * implementar un m�todo que en base al tipo de papel y orientaci�n de la p�gina, devuelva las 
 * coordenadas para una correcta visualizaci�n de la numeraci�n de p�ginas.
 * @todo El m�todo insertar_imagen esta implementado con un m�todo en estado beta de la api ezpdf. Usar
 * con discreci�n.
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