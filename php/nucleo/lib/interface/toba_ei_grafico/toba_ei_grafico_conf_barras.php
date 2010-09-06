<?php
require_once ('3ros/jpgraph/jpgraph.php');
require_once ('3ros/jpgraph/jpgraph_bar.php');

class toba_ei_grafico_conf_barras extends toba_ei_grafico_conf
{
	public function agregar_serie($id, $datos)
	{
		$bar = new BarPlot($datos);
		$this->series[$id] = $bar;
		$this->canvas->Add($bar);

		$this->set_id_serie($id);
		return $this;
	}

	public function set_titulo($titulo)
	{
		return $this;
	}

	public function aplicar_conf_global()
	{
		$conf_global = toba_configuracion::ei_grafico_conf();
		$this->get_serie()->SetFillGradient("navy","steelblue",GRAD_MIDVER);
		$this->get_serie()->SetColor("navy");
	}

}
?>
