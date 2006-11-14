<?php
require_once("nucleo/componentes/interface/interfaces.php");
require_once('catalogo_fuentes.php');

class catalogo implements toba_nodo_arbol
{
	protected $hijos;
	
	function __construct()
	{
		$this->hijos[] = new catalogo_fuentes($this);
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
	
	/**
	 * ¿Los hijos del nodo estan cargados o cuando se requieran hay que ir a buscarlos al server?
	 * @return boolean
	 */
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
		return $this->get_nombre_corto();
	}
	
	function get_info_extra()
	{
		return 'info_extra';	
	}

	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba('configurar.gif', false),
							'ayuda' => 'Administrar usuarios de la instancia' );			
		return $iconos;	
	}
	
	/**
	 * Arreglo de utilerias (similares a los iconos pero secundarios
	 * Formato de nodos y utilerias: array('imagen' => , 'ayuda' => ,  'vinculo' => )
	 */
	function get_utilerias()
	{
		return null;
	}
}
?>