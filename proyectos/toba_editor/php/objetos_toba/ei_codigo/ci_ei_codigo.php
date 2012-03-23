<?php
class ci_ei_codigo extends ci_editores_toba
{
	protected $clase_actual = 'toba_ei_codigo';

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	function conf__prop_basicas()
	{
		return $this->get_entidad()->tabla('prop_basicas')->get();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}

	function get_eventos_estandar($modelo)
	{
		return toba_ei_codigo_info::get_lista_eventos_estandar($modelo);
	}
}

?>