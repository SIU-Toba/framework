<?php
require_once("nucleo/componentes/interface/interfaces.php");
require_once('catalogo_general_tipo_pagina.php');
require_once('catalogo_general_basicos.php');
require_once('catalogo_general_zona.php');
require_once('catalogo_general_mensajes.php');
require_once('catalogo_general_efs.php');
require_once('catalogo_general_derechos.php');

class catalogo_general implements toba_nodo_arbol
{
	protected $hijos;
	
	function __construct()
	{
		$this->hijos[] = new catalogo_general_basicos($this);
		$this->hijos[] = new catalogo_general_tipo_pagina($this);
		$this->hijos[] = new catalogo_general_zona($this);
		$this->hijos[] = new catalogo_general_mensajes($this);
		$this->hijos[] = new catalogo_general_efs($this);
		$this->hijos[] = new catalogo_general_derechos($this);
	}

	function es_hoja()
	{
		return false;
	}
	
	function get_hijos()
	{
		return $this->hijos;
	}
	
	function get_padre()
	{
		return null;	
	}
	
	function tiene_hijos_cargados()
	{
		return true;	
	}
	
	//¿El nodo tiene propiedades extra a mostrar?
	function tiene_propiedades()
	{
	}
	
	function get_id()
	{
		return null;	
	}
	
	function get_nombre_corto()
	{
		return 'Configuracion General';
	}
	
	function get_nombre_largo()
	{
		return null;
	}
	
	function get_info_extra()
	{
		return null;	
	}

	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba('configurar.gif', false),
							'ayuda' => 'Administrar usuarios de la instancia' );			
		return $iconos;	
	}
	
	function get_utilerias()
	{
		return null;
	}
}
?>