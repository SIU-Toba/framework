<?php

class toba_ei_grafico_conf_lineas extends toba_ei_grafico_conf_especifico
{
	/************************************************************************/
	// METODOS INTERNOS
	/************************************************************************/
	protected function set_up_jpgraph()
	{
		parent::set_up_jpgraph();
		require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph_line.php');
	}
	
	protected function get_plot($datos)
	{
		return new LinePlot($datos);
	}

	/************************************************************************/
	// METODOS DE API SERIES
	/************************************************************************/
	/**
	 * @param string $color es un color escrito en inglés
	 * @return toba_ei_grafico_conf_lineas
	 */
	function serie__set_color($color)
	{
		$this->serie__get_activa()->SetColor($color);

		return $this;
	}

	function serie__set_leyenda($leyenda)
	{
		$this->serie__get_activa()->SetLegend($leyenda);
		return $this;
	}
	
}
?>
