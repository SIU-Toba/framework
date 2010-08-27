<?php
/**
 * Clase ayudante para manejar los datos de los gráficos de toba
 */
abstract class toba_grafico_data
{
	/**
	 * @var toba_grafico_conf
	 */
	protected $conf;


	function  __construct()
	{

	}

	function set_conf(toba_grafico_conf $conf)
	{
		$this->conf = $conf;
	}
}
?>
