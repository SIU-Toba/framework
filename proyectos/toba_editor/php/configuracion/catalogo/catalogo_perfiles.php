<?php
require_once("nucleo/componentes/interface/interfaces.php");
require_once("modelo/consultas/dao_permisos.php");
require_once("catalogo_perfiles_grupo.php");

class catalogo_perfiles implements toba_nodo_arbol
{

	function __construct()
	{
		foreach( dao_permisos::get_grupos_acceso() as $grupo ) {
			$this->estructura[] = new catalogo_perfiles_grupo( $this, $grupo['usuario_grupo_acc'], $grupo['nombre'] );
		}
	}
	
	function get_id()
	{
		return null;
	}
	
	function get_nombre_corto()
	{
		return 'Grupos de Acceso';	
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
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("preferencias.gif", false),
							'ayuda' => 'Administrar GRUPOS de ACCESO' );		
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
			'imagen' => toba_recurso::imagen_toba("ml/agregar.gif", false),
			'ayuda' => 'Crear un nuevo grupo de acceso',
			'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '/admin/usuarios/grupo', null, $opciones ),
			'target' => apex_frame_centro
		);
		return $utilerias;	
	}

	function get_padre()
	{
		return null;
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
		return $this->estructura;
	}

	//¿El nodo tiene propiedades extra a mostrar?
	function tiene_propiedades()
	{
	}
}
?>