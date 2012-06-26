<?php
/*
*	
*/
class toba_molde_evento
{
	private $datos;

	function __construct($identificador)
	{
		$this->datos['identificador'] = $identificador;
	}

	function get_identificador()
	{
		return $this->datos['identificador'];
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
	
	function en_botonera($activar=true)
	{
		$estado = $activar ? 1 : 0;
		$this->datos['en_botonera'] = $estado;
		$this->datos['sobre_fila'] = 1 - $estado;
	}

	function sobre_fila()
	{
		$this->datos['sobre_fila'] = 1;
		$this->datos['en_botonera'] = 0;
	}

	function implicito()
	{
		$this->datos['implicito'] = 1;
		$this->datos['sobre_fila'] = 0;
		$this->datos['en_botonera'] = 0;		
	}
	
	function set_predeterminado()
	{
		$this->datos['defecto'] = 1;
	}

	function set_imagen($url_relativa, $origen='apex')
	{
		if ($origen != 'apex' &&  $origen != 'proyecto' ) {
			throw new toba_error_asistentes("Molde EVENTO: El origen de la imagen debe ser 'apex' o 'proyecto'. Valor recibido: $origen");	
		}
		$this->datos['imagen_recurso_origen'] = $origen;
		$this->datos['imagen'] = $url_relativa;
	}

	function set_grupos($grupos)
	{
		if(is_array($grupos)){
			$grupos = implode(',',$grupos);	
		}
		$this->datos['grupo'] = $grupos;
	}

	function set_confirmacion($mensaje)
	{
		$this->datos['confirmacion'] = $mensaje;
	}
	
	function set_estilo($estilo)
	{
		$this->datos['estilo'] = $estilo;
	}

	//---------------------------------------------------
	
	function get_datos()
	{
		return $this->datos;	
	}
}
?>