<?php
require_once("nucleo/componentes/interface/interfaces.php");
require_once('menu_instancia_admin_bloqueo.php');

class menu_instancia_admin implements toba_nodo_arbol
{
	protected $padre;
	protected $hijos;
	
	function __construct($padre)
	{
		$this->padre = $padre;
		$this->hijos[] = new menu_instancia_admin_bloqueo($this);
	}
	
	function get_id()
	{
		return 'admin';	
	}
	
	function get_nombre_corto()
	{
		return 'Administracion';	
	}
	
	function get_nombre_largo()
	{
		return 'Adminisracion general';	
	}
	
	function get_info_extra()
	{
		return null;
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("configurar.gif", false),
							'ayuda' => 'Administrar usuarios de la instancia' );		
		return $iconos;	
	}
	
	/**
	 * Arreglo de utilerias (similares a los iconos pero secundarios
	 * Formato de nodos y utilerias: array('imagen' => , 'ayuda' => ,  'vinculo' => )
	 */
	function get_utilerias()
	{
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$utilerias = array();
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
			'ayuda' => 'Previsualizar el componente',
			'vinculo' => toba::vinculador()->generar_solicitud( 'toba_instancia', 3331, null, $opciones ),
			'target' => 'central'
		);
		return $utilerias;	
	}

	function get_padre()
	{
		return $this->padre;
	}
	
	function tiene_hijos_cargados()
	{
		return true;	
	}
	
	function es_hoja()
	{
		return false;
	}
	
	function get_hijos()
	{
		return $this->hijos;
	}

	//¿El nodo tiene propiedades extra a mostrar?
	function tiene_propiedades()
	{
	}
}
?>