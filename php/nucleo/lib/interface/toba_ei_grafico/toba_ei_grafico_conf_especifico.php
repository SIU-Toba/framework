<?php
/**
 * Esta clase almacena la informaci�n particular de cada gr�fico. El objetivo
 * es proveer una interfaz coherente para generar gr�ficos simples.
 * @author andres
 */

abstract class toba_ei_grafico_conf_especifico extends toba_ei_grafico_conf
{
	/**
	 * Arreglo donde se guardan las series de datos del gr�fico
	 * @var array
	 */
	protected $series;

	/**
	 * La serie que se est� configurando actualmente
	 * @var string
	 */
	protected $serie_actual;
	
	protected $ancho, $alto;

	/************************************************************************/
	// METODOS INTERNOS
	/************************************************************************/

	function  __construct($ancho, $alto)
	{
		$this->series	= array();
		$this->ancho	= $ancho;
		$this->alto		= $alto;
		$this->set_up_jpgraph();
		$this->init_canvas();
	}

	/**
	 * Este m�todo existe debido a que la definici�n de la constante TTF_DIR debe
	 * ser hecha antes de incluir la librer�a jpgraph. Por tanto todas las clases
	 * que extiendan de esta deben redefinir este m�todo con una llamada
	 * parent::set_up_jpgraph() y luego incluir las l�brer�as utilizadas en esa
	 * clase espec�fica
	 */
	protected function set_up_jpgraph()
	{
		if (!defined("TTF_DIR")) {
            $path = toba::instalacion()->get_fonts_path();
            if ($path !== false) {
                define("TTF_DIR", $path);
            }
		}
		
		require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph.php');
	}

	protected function init_canvas()
	{
		$this->canvas__set(new Graph($this->ancho, $this->alto));
		$this->canvas()->SetScale("textlin");
	}

	/**
	 * Devuelve un plot espec�fico a partir de un set de datos
	 * @param array $datos
	 */
	abstract protected function get_plot($datos);
	
	/************************************************************************/
	// METODOS DE API CANVAS
	/************************************************************************/

	function canvas__set_titulo($titulo)
	{
		$this->canvas->title->Set($titulo);
	}

	/************************************************************************/
	// METODOS DE API SERIES
	/************************************************************************/

	/**
	 * Setea la serie a editar a la que tiene id $id_serie
	 * @param string $id_serie
	 */
	function serie__set_activa($id_serie)
	{
		if (!isset($this->series[$id_serie])) {
			throw new toba_error("La serie con id $id_serie no existe.");
		}
		
		$this->serie_actual = $id_serie;
	}

	/**
	 * @param string $id
	 * @param array $datos
	 * @return toba_ei_grafico_conf
	 */
	function serie__agregar($id, $datos)
	{
		$plot = $this->get_plot($datos);
		$this->series[$id] = $plot;
		$this->canvas->Add($plot);

		$this->serie__set_activa($id);
		return $this;
	}

	/**
	 * Devuelve la serie que est� siendo editada actualmente
	 */
	function serie__get_activa()
	{
		if (!isset($this->serie_actual)) {
			throw new toba_error("Antes de comenzar a configurar el "
								 ."objeto debe invocar al m�todo agregar_serie");
		}
		
		return $this->serie($this->serie_actual);
	}

	/**
	 * Devuelve la serie con id $id_serie
	 * @param string $id_serie
	 */
	function serie($id_serie)
	{
		if (!isset($this->series[$id_serie])) {
			throw new toba_error("No existe la serie $id_serie");
		}

		return $this->series[$id_serie];
	}
}
?>
