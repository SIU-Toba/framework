<?php
require_once('nucleo/componentes/interface/interfaces.php');

class catalogo_fuentes_fuente implements toba_nodo_arbol
{
	protected $padre;
	protected $id;
	protected $datos;
	protected $estructura;
	
	function __construct($padre, $id)
	{
		$this->padre = $padre;
		$this->id = $id;
		$this->datos = dao_editores::get_info_fuente_datos($this->id);
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_nombre_corto()
	{
		return $this->datos['descripcion_corta'];
	}
	
	function get_nombre_largo()
	{
		return $this->datos['descripcion_corta'];
	}
	
	function get_info_extra()
	{
		return null;
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("fuente.gif", false),
							'ayuda' => null );		
		return $iconos;
	}
	
	function get_utilerias()
	{
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$parametros = array( apex_hilo_qs_zona => $this->datos['proyecto'] .apex_qs_separador. $this->id);
		$utilerias = array();
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
			'ayuda' => 'Editar fuente de datos',
			'vinculo' => toba::vinculador()->generar_solicitud( 'toba_editor', '/admin/datos/fuente', $parametros, $opciones ),
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