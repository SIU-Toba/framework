<?php
require_once('nucleo/componentes/interface/interfaces.php');

class catalogo_perfiles_grupo implements toba_nodo_arbol
{
	protected $padre;
	protected $id;
	protected $nombre;
	
	function __construct($padre, $id, $nombre)
	{
		$this->padre = $padre;
		$this->id = $id;
		$this->nombre = $nombre;
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_nombre_corto()
	{
		return $this->nombre;
	}
	
	function get_nombre_largo()
	{
		return $this->nombre;
	}
	
	function get_info_extra()
	{
		return null;
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("usuarios/usuario.gif", false),
							'ayuda' => null );		
		return $iconos;
	}
	
	function get_utilerias()
	{
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$parametros = array( apex_hilo_qs_zona => toba_editor::get_proyecto_cargado() .apex_qs_separador. $this->id);
		$utilerias = array();
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("usuarios/permisos.gif", false),
			'ayuda' => 'Editar DERECHOS del grupo de acceso',
			'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '3278', $parametros, $opciones ),
			'target' => apex_frame_centro
		);
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("usuarios/grupo.gif", false),
			'ayuda' => 'Editar el acceso a ITEMs del grupo de acceso',
			'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '3288', $parametros, $opciones ),
			'target' => apex_frame_centro
		);
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
			'ayuda' => 'Editar GRUPO de ACCESO',
			'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '/admin/usuarios/grupo', $parametros, $opciones ),
			'target' => apex_frame_centro
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

	function tiene_propiedades()
	{
	}
}
?>