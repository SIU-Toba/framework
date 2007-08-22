<?php

/**
 * Ancestro de los botones y tabs definidos en el editor
 * @package Componentes
 * @subpackage Eis
 */
class toba_boton
{
	protected $datos;
	protected $activado = true;
	protected $oculto = false;
	protected $anulado = false;
	protected $contenedor;

	function __construct($datos=null, $contenedor=null)
	{
		if (isset($datos)) {
			$this->datos = $datos;
		} else {
			$this->datos['etiqueta'] = '';
			$this->datos['maneja_datos'] = true;
			$this->datos['sobre_fila'] = false;
			$this->datos['confirmacion'] = '';
			$this->datos['estilo'] = '';
			$this->datos['imagen'] = '';
			$this->datos['en_botonera'] = true;
			$this->datos['ayuda'] = '';
			$this->datos['accion'] = '';
			$this->datos['grupo'] = '';
		}
		$this->contenedor = $contenedor;
	}
	

	//--------- Preguntas ---------------------

	function esta_desactivado()
	{
		return $this->activado;	
	}
	
	function esta_oculto()
	{
		return $this->oculto;	
	}
	
	function esta_anulado()
	{
		return $this->anulado;
	}
	
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
				return toba_recurso::imagen($img, null, null, null, null, null, 'vertical-align: middle;').' ';
			} else {
				toba::logger()->warning("No se especifico el origen de la imagen '{$this->datos['imagen']}' del botón");
			}
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
	
	function set_id($id)
	{
		 $this->datos['identificador'] = $id;
	}
	
	function set_etiqueta($texto)
	{
		$this->datos['etiqueta'] = $texto;
	}
	
	function set_msg_ayuda($texto)
	{
		$this->datos['ayuda'] = $texto;
	}

	/**
	 * Cambia la imagen asociada al botón
	 * @param string $url_relativa Direccion de la imagen relativa a la carpeta www/img
	 * @param string $origen La imagen pertenece al proyecto actual ('proyecto') o a toba ('apex')
	 */
	function set_imagen($url_relativa, $origen='apex')
	{
		if ($origen != 'apex' &&  $origen != 'proyecto' ) {
			throw new toba_error_def("EVENTO: El origen de la imagen debe ser 'apex' o 'proyecto'. Valor recibido: $origen");	
		}
		$this->datos['imagen_recurso_origen'] = $origen;
		$this->datos['imagen'] = $url_relativa;
	}

	function set_msg_confirmacion($texto)
	{
		$this->datos['confirmacion'] = $texto;
	}

	function set_estilo_css($estilo)
	{
		$this->datos['estilo'] = $estilo;
	}
	
	//------ Desactivar y Ocultar

	function desactivar()
	{
		$this->activado = false;
	}
	
	function activar()
	{
		$this->activado = true;
	}
	
	function ocultar()
	{
		$this->oculto = true;		
	}
	
	function mostrar()
	{
		$this->oculto = false;
	}
	
	//------- Anulacion: el elemento no se envia al cliente
	
	function anular()
	{
		$this->anulado = true;			
	}

	function restituir()
	{
		$this->anulado = false;
	}
}
?>