<?php

/**
 * El perfil funcional permite agrupar derechos y restricciones a acceder a determinados elementos de un proyecto.
 * Un usuario puede pertenecer a distintos perfiles de acceso
 * @package Seguridad
 */
class toba_perfil_funcional
{
	
	protected $item;
	protected $id_restricciones = array();
	protected $rf_pantallas 	= array();
	protected $rf_eis			= array();
	protected $rf_eventos		= array();
	protected $rf_cuadro_cols	= array();
	protected $rf_form_efs		= array();
	protected $rf_filtro_cols	= array();
	
	function __construct()
	{
		$this->item = toba::memoria()->get_item_solicitado();
		$this->id_restricciones = toba::manejador_sesiones()->get_restricciones_funcionales();
		if (! empty($this->id_restricciones)) {
			$this->cargar_info_restricciones();
		}
	}
	
	/**
	 * Dado el usuario actual carga aquellas restricciones aplicables a los componentes del item actual
	 *
	 */
	function cargar_info_restricciones()
	{
		$this->rf_pantallas 	= toba_proyecto_implementacion::get_rf_pantallas($this->item[0], $this->item[1], $this->id_restricciones);
		$this->rf_eis			= toba_proyecto_implementacion::get_rf_eis($this->item[0], $this->item[1], $this->id_restricciones);
		$this->rf_eventos		= toba_proyecto_implementacion::get_rf_eventos($this->item[0], $this->item[1], $this->id_restricciones);
		$this->rf_cuadro_cols 	= toba_proyecto_implementacion::get_rf_cuadro_cols($this->item[0], $this->item[1], $this->id_restricciones);
		$this->rf_form_efs		= toba_proyecto_implementacion::get_rf_form_efs($this->item[0], $this->item[1], $this->id_restricciones);
		$this->rf_filtro_cols 	= toba_proyecto_implementacion::get_rf_filtro_cols($this->item[0], $this->item[1], $this->id_restricciones);
	}
	
	
	/**
	 * Dado el id de un ci retorna las pantallas no visibles según perfil funcional
	 * @param integer $ci
	 * @return array
	 */
	function get_rf_pantallas_no_visibles($ci) 
	{
		$pantallas = array();
		foreach ($this->rf_pantallas as $rf) {
			if ($rf['objeto_ci'] == $ci && $rf['no_visible']) {
				$pantallas[] = $rf['pantalla'];
			}
		}
		return $pantallas;
	}
	
	/**
	 * Retorna los componentes no visibles de el item actual según perfil funcional
	 * @return array
	 */	
	function get_rf_eis_no_visibles() 
	{
		$eis = array();
		foreach ($this->rf_eis as $rf) {
			if ($rf['no_visible']) {
				$eis[] = $rf['objeto'];
			}
		}
		return $eis;
	}
	
	function get_rf_eventos_no_visibles()
	{
		$eventos = array();
		foreach ($this->rf_eventos as $rf) {
			if ($rf['no_visible']) {
				$eventos[] = $rf['evento_id'];
			}
		}
		return $eventos;		
	}
	
	function get_rf_cuadro_cols_no_visibles($id_cuadro)
	{
		$columnas = array();
		foreach ($this->rf_cuadro_cols as $rf) {
			if ($rf['objeto_cuadro'] == $id_cuadro && $rf['no_visible']) {
				$columnas[] = $rf['objeto_cuadro_col'];
			}
		}
		return $columnas;
	}

	function get_rf_form_efs_no_visibles($id_form)
	{
		$efs = array();
		foreach ($this->rf_form_efs as $rf) {
			if ($rf['objeto_ei_formulario'] == $id_form && $rf['no_visible']) {
				$efs[] = $rf['objeto_ei_formulario_fila'];
			}
		}
		return $efs;
	}
	
	function get_rf_form_efs_no_editables($id_form)
	{
		$efs = array();
		foreach ($this->rf_form_efs as $rf) {
			if ($rf['objeto_ei_formulario'] == $id_form && $rf['no_editable']) {
				$efs[] = $rf['objeto_ei_formulario_fila'];
			}
		}
		return $efs;
	}	
	
	function get_rf_filtro_cols_no_visibles($id_filtro)
	{
		$columnas = array();
		foreach ($this->rf_filtro_cols as $rf) {
			if ($rf['objeto_ei_filtro'] == $id_filtro && $rf['no_visible']) {
				$columnas[] = $rf['objeto_ei_filtro_col'];
			}
		}
		return $columnas;		
	}
	

}

?>