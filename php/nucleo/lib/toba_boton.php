<?php

/**
*	Ancestro de los botones y tabs definidos en el editor
*/
class toba_boton
{
	protected $datos;
	protected $activado = true;

	function __construct($datos)
	{
		$this->datos = $datos;
	}

	//--------- Preguntas ---------------------

	function posee_confirmacion()
	{
		return ( trim($this->datos['confirmacion']) !== '' );
	}

	//--------- Geters ---------------------
	
	function get_etiqueta()
	{
		return $this->datos['etiqueta'];	
	}

	function get_msg_ayuda()
	{
		return $this->datos['ayuda'];	
	}

	function get_imagen()
	{
		if (isset($this->datos['imagen']) && $this->datos['imagen'] != '') {
			if (isset($this->datos['imagen_recurso_origen'])) {
				$img = toba_recurso::imagen_de_origen($this->datos['imagen'], $this->datos['imagen_recurso_origen']);
			} else {
				$img = $this->datos['imagen'];
			}
			return toba_recurso::imagen($img, null, null, null, null, null, 'vertical-align: middle;').' ';
		}
	}

	function get_imagen_url_rel()
	{
		return $this->datos['imagen'];
	}

	function get_msg_confirmacion()
	{
		return $this->datos['confirmacion'];	
	}
	
	//--------- Seters ---------------------

	function set_etiqueta($texto)
	{
		$this->datos['etiqueta'] = $texto;
	}
	
	function set_msg_ayuda($texto)
	{
		$this->datos['ayuda'] = $texto;
	}

	function set_imagen($url_relativa, $origen=null)
	{
		if (isset($origen) && ( ($origen != 'apex') || ( $origen != 'proyecto') ) ) {
			throw new toba_error_def("EVENTO: El origen de la imagen debe ser 'apex' o 'proyecto'. Valor recibido: $origen");	
		} else {
			$origen = 'apex';	
		}
		$this->datos['imagen_recurso_origen'] = $origen;
		$this->datos['imagen'] = $url_relativa;
	}

	function set_msg_confirmacion($texto)
	{
		$this->datos['confirmacion'] = $texto;
	}

	function desactivar()
	{
		$this->activado = false;			
	}

	function activar()
	{
		$this->activado = true;
	}
}
?>