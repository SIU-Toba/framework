<?php
require_once('nucleo/componentes/interface/interfaces.php');

class catalogo_general_derechos implements toba_nodo_arbol
{
	protected $padre;
	
	function __construct($padre)
	{
		$this->padre = $padre;
	}
	
	function get_id()
	{
		return null;
	}
	
	function get_nombre_corto()
	{
		return 'Derechos';
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
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("usuarios/permisos.gif", false),
							'ayuda' => null );		
		return $iconos;
	}
	
	function get_utilerias()
	{
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$utilerias = array();
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
			'ayuda' => 'Editar DERECHOS globales',
			'vinculo' => toba::vinculador()->crear_vinculo( toba_editor::get_id(), '3276', $opciones ),
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