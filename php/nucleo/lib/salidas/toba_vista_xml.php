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
