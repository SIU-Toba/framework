<?php
require_once("nucleo/componentes/interface/interfaces.php");
require_once('menu_instancia_proyectos.php');
require_once('menu_instancia_usuarios.php');
require_once('menu_instancia_admin.php');

class menu_instancia implements toba_nodo_arbol
{
	protected $hijos;
	
	function __construct()
	{
		$this->hijos[] = new menu_instancia_usuarios($this);
		$this->hijos[] = new menu_instancia_proyectos($this);
		$this->hijos[] = new menu_instancia_admin($this);
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
		return 'menu_instancia';	
	}
	
	function get_nombre_corto()
	{
		return 'Instancia: ' . toba::sesion()->get_id_instancia();	
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
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba('dimension.gif', false),
							'ayuda' => 'Administrar usuarios de la instancia' );			
		return $iconos;	
	}
	
	/**
	 * Arreglo de utilerias (similares a los iconos pero secundarios
	 * Formato de nodos y utilerias: array('imagen' => , 'ayuda' => ,  'vinculo' => )
	 */
	function get_utilerias()
	{
		$utilerias = array();
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$utilerias = array();
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("info_chico.gif", false),
			'ayuda' => 'Previsualizar el componente',
			'vinculo' => toba::vinculador()->generar_solicitud( 'toba_instancia', '/inicio', null, $opciones ),
			'target' => 'central'
		);
		return $utilerias;	
	}
}
?>