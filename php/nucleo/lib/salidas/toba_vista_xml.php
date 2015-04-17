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
	protected $xml_externo;
	protected $temp_salida;
	
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

	/**
	 * Permite setear un string conteniendo XML generado externamente, esto anula la generacion
	 * interna por medio de la vista_xml de los componentes
	 * @param string $xml
	 */
	function set_xml_pre_generado($xml)
	{
		$this->xml_externo = $xml;
	}

	//------------------------------------------------------------------------
	//-- Generacion del xml
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{	
		$this->temp_salida = $this->generar_xml();
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
	function generar_xml() 
	{
		if (! isset($this->xml_externo)) {																//Si no existe XML pre-generado externamente
			$xml = '<?xml version="1.0" encoding="ISO-8859-1"?><raiz>';
			foreach( $this->objetos as $objeto ) {
				if(method_exists($objeto, 'vista_xml')) {
					$xml .= $objeto->vista_xml(true);
				}
			}
			$xml .= '</raiz>';
		} else {
			$xml = $this->xml_externo;
		}
		return $xml;
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
