<?php
/**
 * Esta clase almacena la información particular de cada gráfico. Como así
 * también provee métodos para obtener el objeto jgraph ya configurado
 * @author andres
 */

abstract class toba_ei_grafico_conf
{
	/**
	 * Arreglo donde se guardan las series de datos del gráfico
	 * @var array
	 */
	protected $series;

	/**
	 * La serie que se está configurando actualmente
	 * @var string
	 */
	protected $serie_actual;

	/**
	 * @var Graph
	 */
	protected $canvas;
	
	protected $ancho, $alto, $path;


	function  __construct($ancho, $alto)
	{
		$this->series	= array();
		$this->ancho	= $ancho;
		$this->alto		= $alto;
		$this->init_canvas();
	}

	/**
	 * Prepara el objeto para que edite la serie con id $id_serie
	 * @param string $id_serie
	 */
	function set_id_serie($id_serie)
	{
		if (!isset($this->series[$id_serie])) {
			throw new toba_error("La serie con id $id_serie no existe.");
		}
		
		$this->serie_actual = $id_serie;
	}

	protected function init_canvas()
	{
		$this->canvas = new Graph($this->ancho, $this->alto);
		$this->canvas->SetScale("textlin");
	}

	/**
	 * Agrega una serie de datos al gráfico
	 */
	abstract function agregar_serie($id, $datos);
	
	abstract function aplicar_conf_global();

	function generar_imagen()
	{
		// Se genera el path para la ubicación temporal de la imágen
		$this->generar_path();

		// escribimos la imagen a un archivo
		$this->canvas->Stroke($this->path);
	}
	
	/**
	 * Devuelve el contenedor de gráficos de jpgraph
	 * @return Graph
	 */
	function get_canvas()
	{
		return $this->canvas;
	}

	function set_titulo_canvas($titulo)
	{
		$this->canvas->title->Set($titulo);
	}


	abstract function set_titulo($titulo);
	
	function get_path()
	{
		if (!isset($this->path)) {
			throw new toba_error("El path se setea luego de generar la imagen.");
		}

		return $this->path;
	}

	protected function generar_path()
	{
		$this->path = toba_dir().'/temp/'.uniqid().'.png';
	}

	/**
	 * Devuelve la serie que está siendo editada actualmente
	 */
	protected function get_serie()
	{
		if (!isset($this->serie_actual)) {
			throw new toba_error("Antes de comenzar a configurar el "
								 ."objeto debe invocar al método agregar_serie");
		}
		
		return $this->series[$this->serie_actual];
	}
}
?>
