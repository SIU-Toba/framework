<?php

/**
 * Description of toba_ei_grafico_torta
 *
 * @author andres
 */
class toba_ei_grafico_conf_torta extends toba_ei_grafico_conf_especifico
{
	/************************************************************************/
	// METODOS INTERNOS
	/************************************************************************/
	protected function init_canvas()
	{
		$this->canvas = new PieGraph($this->ancho, $this->alto);
	}

	protected function  set_up_jpgraph()
	{
		parent::set_up_jpgraph();
		require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph_pie.php');
	}

	protected function get_plot($data)
	{
		return new PiePlot($data);
	}
	
	/************************************************************************/
	// METODOS DE API CANVAS
	/************************************************************************/

	/************************************************************************/
	// METODOS DE API SERIES
	/************************************************************************/
	/**
	 * @param string $titulo
	 * @return toba_ei_grafico_conf_torta
	 */
	function serie__set_titulo($titulo)
	{
		$this->serie__get_activa()->title->Set($titulo);

		return $this;
	}
	
	/**
	 * @param array $leyendas
	 * @return toba_ei_grafico_conf_torta 
	 */
	function serie__set_leyendas($leyendas)
	{
		$this->serie__get_activa()->SetLegends($leyendas);

		return $this;
	}

	/**
	 * Setea el tema de colores
	 * @param string $tema puede ser: earth | pastel | sand | water
	 */
	function serie__set_tema($tema)
	{
		$this->serie__get_activa()->SetTheme($tema);

		return $this;
	}

	/**
	 * @param double $x entre 0 y 1
	 * @param double $y entre 0 y 1
	 * @return toba_ei_grafico_conf_torta
	 */
	function serie__set_centro($x, $y = 0.5)
	{
		$this->serie__get_activa()->SetCenter($x, $y);

		return $this;
	}

	/**
	 * Separa las porciones con indice $indices[i] en el arreglo de datos del gráfico.
	 * @param array $indices
	 * @return toba_ei_grafico_conf_torta
	 */
	function serie__separar_porciones($indices)
	{
		foreach ($indices as $i) {
			$this->serie__get_activa()->ExplodeSlice($i);
		}
		
		return $this;
	}

}

?>
