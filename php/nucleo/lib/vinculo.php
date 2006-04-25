<?php

class vinculo
{
	private $item;
	private $proyecto;
	private $parametros;// = array();
	private $opciones;// = array();
	private $target;
	private $popup = 0;
	private $popup_parametros;

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
	
	function set_parametros( $parametros )
	{
		$this->parametros = $parametros;	
	}

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