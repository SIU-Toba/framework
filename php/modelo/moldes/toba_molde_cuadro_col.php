<?php
/*
*	
*/
class toba_molde_cuadro_col
{
	private $datos;

	function __construct($identificador, $tipo)
	{
		$this->datos['columna'] = $identificador;
		$this->datos['tipo'] = $tipo;
	}

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------

	function set_etiqueta($etiqueta)
	{
		$this->datos['etiqueta'] = $etiqueta;
	}
	
	function set_orden($orden)
	{
		$this->datos['orden'] = $orden;
	}

	function maneja_datos()
	{
		$this->datos['maneja_datos'] = 1;
	}
	
	function en_botonera()
	{
		$this->datos['en_botonera'] = 1;
	}

	function implicito()
	{
		$this->datos['implicito'] = 1;
	}

	function set_imagen($url_relativa, $origen='apex')
	{
		if ($origen != 'apex' &&  $origen != 'proyecto' ) {
			throw new toba_error_def("Molde EVENTO: El origen de la imagen debe ser 'apex' o 'proyecto'. Valor recibido: $origen");	
		}
		$this->datos['imagen_recurso_origen'] = $origen;
		$this->datos['imagen'] = $url_relativa;
	}

	//---------------------------------------------------
	
	function get_datos()
	{
		return $this->datos;	
	}
}
?>