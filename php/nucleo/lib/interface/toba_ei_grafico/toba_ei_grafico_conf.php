<?php
/**
 * Esta clase almacena la informaci�n particular de cada gr�fico. Como as�
 * tambi�n provee m�todos para obtener el objeto jgraph ya configurado
 * @author andres
 */

abstract class toba_ei_grafico_conf
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
	 * Agrega una serie de datos al gr�fico
	 */
	abstract function agregar_serie($id, $datos);
	
	abstract function aplicar_conf_global();

	function generar_imagen()
	{
		// Se genera el path para la ubicaci�n temporal de la im�gen
		$this->generar_path();

		// escribimos la imagen a un archivo
		$this->canvas->Stroke($this->path);
	}
	
	/**
	 * Devuelve el contenedor de gr�ficos de jpgraph
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
	 * Devuelve la serie que est� siendo editada actualmente
	 */
	protected function get_serie()
	{
		if (!isset($this->serie_actual)) {
			throw new toba_error("Antes de comenzar a configurar el "
								 ."objeto debe invocar al m�todo agregar_serie");
		}
		
		return $this->series[$this->serie_actual];
	}
}
?>
