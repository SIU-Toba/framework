<?php
/**
 * Esta clase representa el wrapper de m�s bajo nivel para la librer�a jpgraph.
 * La �nica utilidad que provee es la de graficar la im�gen, de esta manera
 * se quita la responsabilidad al usuario sobre el tiempo en el cu�l se tiene
 * que generar la imagen
 */
class toba_ei_grafico_conf
{
	/**
	 * @var Graph
	 */
	protected $canvas;


    function canvas__set($canvas)
	{
		$this->canvas = $canvas;
	}
	
	/**
	 * Devuelve el contenedor de gr�ficos de jpgraph
	 * @return Graph
	 */
	function canvas()
	{
		if (!isset($this->canvas)) {
			throw new toba_error("No hay ning�n canvas seteado");
		}

		return $this->canvas;
	}

	/**
	 * Genera la imagen
	 * @param string $path path completo de la imagen a generar
	 */
	function imagen__generar($path)
	{
		if (!isset($this->canvas)) {
			throw new toba_error("Generaci�n de im�gen: No hay ning�n canvas seteado");
		}
		// escribimos la imagen a un archivo
		$this->canvas->Stroke($path);
	}
}
?>
