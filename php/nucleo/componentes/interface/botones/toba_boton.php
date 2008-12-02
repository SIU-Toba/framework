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
			$this->datos['identificador'] = '';
		}
		$this->contenedor = $contenedor;
	}
	

	//--------- Preguntas ---------------------

	/**
	 * Un botón desactivado se muestra pero no se puede clickear
	 * @return boolean
	 */	
	function esta_activado()
	{
		return $this->activado;	
	}
	
	/**
	 * Un botón oculto se envia al cliente pero oculto via css
	 * @return boolean
	 */		
	function esta_oculto()
	{
		return $this->oculto;	
	}
	
	/**
	 * Un botón anulado no se envia al cliente
	 * @return boolean 
	 */	
	function esta_anulado()
	{
		return $this->anulado;
	}
	
	/**
	 * Indica si al presionar se muestra o no una confirmación
	 * @see set_msg_confirmacion
	 * @return boolean
	 */
	function posee_confirmacion()
	{
		return ( trim($this->datos['confirmacion']) !== '' );
	}

	//--------- Geters ---------------------
	
	/**
	 * Retorna la etiqueta asociada al botón
	 * @see set_etiqueta
	 * @return unknown
	 */
	function get_etiqueta()
	{
		return $this->datos['etiqueta'];	
	}

	/**
	 * Retorna el mensaje de ayuda contextual que tiene el botón
	 * @see set_msg_ayuda
	 * @return string
	 */
	function get_msg_ayuda()
	{
		return $this->datos['ayuda'];	
	}


	/**
	 * Retorna el tag <img> del botón, si tiene imagen asociada
	 * @see set_imagen
	 */
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

	/**
	 * Retorna la Direccion de la imagen relativa a la carpeta www/img
	 */
	function get_imagen_url_rel()
	{
		return $this->datos['imagen'];
	}

	/**
	 * Retorna si existe el  mensaje de confirmación cuando el usuario clickea el botón
	 * @see set_msg_confirmacion
	 */	
	function get_msg_confirmacion()
	{
		return $this->datos['confirmacion'];	
	}
	
	
	function get_id(){
		return $this->datos['identificador'];
	}
	
	//--------- Seters ---------------------
	
	function set_id($id)
	{
		 $this->datos['identificador'] = $id;
	}
	
	/**
	 * Etiqueta visible en el botón
	 */
	function set_etiqueta($texto)
	{
		$this->datos['etiqueta'] = $texto;
	}
	
	/**
	 * Ayuda contextual que brindará el botón
	 */
	function set_msg_ayuda($texto)
	{
		$this->datos['ayuda'] = $texto;
	}

	/**
	 * Cambia la imagen asociada al botón, se muestra al lado de la etiqueta si la posee
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

	/**
	 * Muestra un mensaje de confirmación cuando el usuario clickea el botón
	 * @param string $texto Mensaje a mostrar
	 */
	function set_msg_confirmacion($texto)
	{
		$this->datos['confirmacion'] = $texto;
	}

	/**
	 * Cambia la clase CSS del botón
	 * @param string $estilo
	 */
	function set_estilo_css($estilo)
	{
		$this->datos['estilo'] = $estilo;
	}
	
	//------ Desactivar y Ocultar

	/**
	 * Un botón desactivado se muestra pero no se puede clickear
	 */
	function desactivar()
	{
		$this->activado = false;
	}

	/**
	 * Un botón desactivado se muestra pero no se puede clickear
	 */
	function activar()
	{
		$this->activado = true;
	}
	
	/**
	 * Un botón oculto se envia al cliente pero oculto via css
	 */	
	function ocultar()
	{
		$this->oculto = true;		
	}

	/**
	 * Un botón oculto se envia al cliente pero oculto via css
	 */		
	function mostrar()
	{
		$this->oculto = false;
	}
	
	//------- Anulacion: el elemento no se envia al cliente
	
	/**
	 * Un botón anulado no se envia al cliente
	 */
	function anular()
	{
		$this->anulado = true;			
	}

	/**
	 * Deshace la anulación del botón
	 * Un botón anulado no se envia al cliente
	 * @see anular
	 */	
	function restituir()
	{
		$this->anulado = false;
	}
}
?>