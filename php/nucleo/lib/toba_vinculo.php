<?php

/**
 * Mantiene un vinculo especifico y brinda una api para poder manipularlo
 * @package Centrales
 */
class toba_vinculo
{
	private $item;
	private $proyecto;
	private $parametros;// = array();
	private $opciones;// = array();
	private $target;
	private $popup = 0;
	private $popup_parametros = array();
	private $popup_parametros_validos = array('width','height','scrollbars','resizable');

	function __construct($proyecto=null, $item=null, $popup=null, $opciones_popup=null)
	{
		if(isset($proyecto)&&isset($item)){
			$this->set_item($proyecto, $item);	
		}
		if($popup){
			$this->activar_popup();
		}
		if(isset($opciones_popup)){
			//Parseo del formato actual de definicion
			$temp = explode(',',$opciones_popup);
			$temp = array_map('trim',$temp);
			foreach($temp as $opcion) {
				$o = explode(':',$opcion);
				$o = array_map('trim',$o);
				$popup_parametros[$o[0]] = $o[1];
			}	
			$this->set_popup_parametros( $popup_parametros );
		}
	}
	
	/**
	 * Cambia el item destino del vinculo
	 */
	function set_item( $proyecto, $item )
	{
		$this->item = $item;
		$this->proyecto = $proyecto;
	}

	function get_item()
	{
		return $this->item;
	}
	
	function get_proyecto()
	{
		return $this->proyecto;
	}
	
	/**
	 * Cambia los parametros de la URL generada por el vinculo
	 */	
	function set_parametros( $parametros )
	{
		$this->parametros = $parametros;	
	}

	/**
	 * Agrega parametros a la URL generada por el vinculo
	 */
	function agregar_parametro($clave, $valor)
	{
		$this->parametros[$clave] = $valor;
	}

	function get_parametros()
	{
		return $this->parametros;	
	}

	function set_opciones($datos)
	{
		$this->opciones = $datos;
	}
	
	function get_opciones()
	{
		return $this->opciones;
	}

	function agregar_opcion($clave, $valor)
	{
		$this->opciones[$clave] = $valor;
	}

	function activar_popup()
	{
		$this->popup = 1;	
	}

	function desactivar_popup()
	{
		$this->popup = 0;	
	}

	function estado_popup()
	{
		return $this->popup;
	}

	function set_popup_parametros($parametros)
	{
		$this->popup_parametros = $parametros;	
	}

	function get_popup_parametros()
	{
		return $this->popup_parametros;	
	}
	
	function set_target($id)
	{
		$this->target = $id;	
	}
	
	function get_target()
	{
		return $this->target;	
	}
	
}
?>