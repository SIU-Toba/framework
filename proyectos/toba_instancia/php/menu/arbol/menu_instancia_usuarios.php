<?php
require_once("nucleo/componentes/interface/interfaces.php");

class menu_instancia_usuarios implements toba_nodo_arbol
{
	protected $padre;
	
	function __construct($padre)
	{
		$this->padre = $padre;
	}
	
	function get_id()
	{
		return 'usuarios';	
	}
	
	function get_nombre_corto()
	{
		return 'Usuarios';	
	}
	
	function get_nombre_largo()
	{
		return 'Adminisracion de usuarios';	
	}
	
	function get_info_extra()
	{
		return null;
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("usuarios/usuario.gif", false),
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
			'frame' => 'central'
		);
		return $utilerias;	
	}

	function get_padre()
	{
		return $this->padre;	
	}
	
	function tiene_hijos_cargados()
	{
		return false;	
	}
	
	function es_hoja()
	{
		return true;
	}
	
	function get_hijos()
	{
		return null;
	}

	//¿El nodo tiene propiedades extra a mostrar?
	function tiene_propiedades()
	{
	}
}
?>