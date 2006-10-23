<?php
require_once('nucleo/componentes/interface/interfaces.php');
require_once('menu/arbol/menu_proyecto.php');

class menu_instancia_proyectos implements toba_nodo_arbol
{
	protected $padre;
	protected $estructura;
	
	function __construct($padre)
	{
		$this->padre = $padre;
		foreach( admin_instancia::ref()->get_lista_proyectos() as $proyecto ) {
			$this->estructura[] = new menu_proyecto( $proyecto, $this );
		}
	}

	function es_hoja()
	{
		return false;
	}
	
	function get_hijos()
	{
		return $this->estructura;
	}
	
	function get_padre()
	{
		return $this->padre;	
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
		return 'menu_proyectos';	
	}
	
	function get_nombre_corto()
	{
		return 'Proyectos';	
	}
	
	function get_nombre_largo()
	{
		return 'Proyectos disponibles en la instancias';	
	}
	
	function get_info_extra()
	{
		return null;	
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("seleccionar.gif", false),
							'ayuda' => null );		
		return $iconos;
	}
	
	function get_utilerias()
	{
		$utilerias = array();
		return $utilerias;	
	}
}
?>