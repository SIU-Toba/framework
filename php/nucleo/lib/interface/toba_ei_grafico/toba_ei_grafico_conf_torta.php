<?php

/**
 * Description of toba_ei_grafico_torta
 *
 * @author andres
 */
require_once ('3ros/jpgraph/jpgraph.php');
require_once ('3ros/jpgraph/jpgraph_pie.php');

class toba_ei_grafico_conf_torta extends toba_ei_grafico_conf
{
	function init_canvas()
	{
		$this->canvas = new PieGraph($this->ancho, $this->alto);
	}

	/**
	 * @param string $id
	 * @param array $datos
	 * @return toba_ei_grafico_conf_torta
	 */
	function agregar_serie($id, $datos)
	{
		$pie = new PiePlot($datos);
		$this->series[$id] = $pie;
		$this->canvas->Add($pie);

		$this->set_id_serie($id);
		return $this;
	}

	/**
	 * @param array $leyendas
	 * @return toba_ei_grafico_conf_torta 
	 */
	function set_leyendas($leyendas)
	{
		$this->get_serie()->SetLegends($leyendas);

		return $this;
	}

	/**
	 * @param double $x entre 0 y 1
	 * @param double $y entre 0 y 1
	 * @return toba_ei_grafico_conf_torta
	 */
	function set_centro($x, $y = 0.5)
	{
		$this->get_serie()->SetCenter($x, $y);

		return $this;
	}

	/**
	 * @param string $titulo
	 * @return toba_ei_grafico_conf_torta
	 */
	function set_titulo($titulo)
	{
		$this->get_serie()->title->Set($titulo);

		return $this;
	}

	/**
	 * Separa las porciones con indice $indices[i] en el arreglo de datos del gráfico.
	 * @param array $indices
	 * @return toba_ei_grafico_conf_torta
	 */
	function separar_porciones($indices)
	{
		foreach ($indices as $i) {
			$this->get_serie()->ExplodeSlice($i);
		}
		
		return $this;
	}

	function aplicar_conf_global()
	{
		// Se consume la configuración global del gráfico
		$conf_global = toba_configuracion::ei_grafico_conf();
		$this->canvas->title->SetColor($conf_global->get_color_titulo());

		$this->get_serie()->SetSliceColors($conf_global->get_colores_grafico());
	}

}

?>
