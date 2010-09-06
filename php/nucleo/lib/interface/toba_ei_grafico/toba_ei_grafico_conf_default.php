<?php
/**
 * Clase de configuración de toba_ei_grafico default para todos los proyectos
 * toba. Los proyectos tienen la opción de utilizar otra configuración a través
 * de toba_configuración
 *
 * @author andres
 */
class toba_ei_grafico_conf_default implements toba_ei_grafico_conf_global
{
	protected $color_titulo;
	protected $colores;
	protected $color_fondo;

	function __construct()
	{
		$this->color_titulo = 'blue';
		$this->colores = array('red','blue','yellow','green');
		$this->color_fondo = 'white';
	}

	public function  get_color_titulo()
	{
		return $this->color_titulo;
	}

	public function get_color_fondo()
	{
		return $this->color_fondo;
	}

	public function get_colores_grafico()
	{
		return $this->colores;
	}
}
?>
