<?php
require_once("nucleo/componentes/interface/interfaces.php");

class menu_proyecto implements toba_nodo_arbol
{
	protected $proyecto;
	protected $padre;
	protected $datos;
	
	function __construct($proyecto, $padre)
	{
		$this->proyecto = $proyecto;
		$this->padre = $padre;
		$this->datos = consultas_instancia::get_datos_proyecto($this->proyecto);
	}
	
	function get_id()
	{
		return $this->datos['proyecto'];
	}
	
	function get_nombre_corto()
	{
		return $this->datos['proyecto'];
	}
	
	function get_nombre_largo()
	{
		return $this->datos['proyecto'];
	}
	
	function get_info_extra()
	{
		return null;
	}
	
	function get_iconos()
	{
		$iconos = array();
		$iconos[] = array( 'imagen' => 	toba_recurso::imagen_toba("Proyecto.gif", false),
							'ayuda' => null );		
		return $iconos;
	}
	
	function get_utilerias()
	{
		$opciones['menu'] = true;
		$opciones['celda_memoria'] = 'central';
		$utilerias = array();
		$utilerias[] = array(
			'imagen' => toba_recurso::imagen_toba("info_chico.gif", false),
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