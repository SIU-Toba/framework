<?php

	use JpGraph\BarPlot;

class toba_ei_grafico_conf_barras extends toba_ei_grafico_conf_especifico
{
	/************************************************************************/
	// METODOS INTERNOS
	/************************************************************************/
	protected function set_up_jpgraph()
	{
		parent::set_up_jpgraph();
		require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph_bar.php');
	}

	protected function get_plot($datos)
	{
		return new BarPlot($datos);
	}
	/************************************************************************/
	// METODOS DE API SERIES
	/************************************************************************/
	
	/**
	 * @param string $id
	 * @param array $datos
	 * @return toba_ei_grafico_conf_barras
	 */
	function serie__agregar($id, $datos)
	{
		if (count($this->series) > 0) {
			throw new toba_error("Los gráficos de barras soportan sólo una serie");
		}

		parent::serie__agregar($id, $datos);

		return $this;
	}
	
	/**
	 * @param string $color es un color escrito en inglés
	 * @return toba_ei_grafico_conf_barras
	 */
	function serie__set_color($color)
	{
		$this->serie__get_activa()->SetFillColor($color);

		return $this;
	}

}
?>
