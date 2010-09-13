<?php
/**
 * Esta clase representa el wrapper de más bajo nivel para la librería jpgraph.
 * La única utilidad que provee es la de graficar la imágen, de esta manera
 * se quita la responsabilidad al usuario sobre el tiempo en el cuál se tiene
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
	 * Devuelve el contenedor de gráficos de jpgraph
	 * @return Graph
	 */
	function canvas()
	{
		if (!isset($this->canvas)) {
			throw new toba_error("No hay ningún canvas seteado");
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
			throw new toba_error("Generación de imágen: No hay ningún canvas seteado");
		}
		// escribimos la imagen a un archivo
		$this->canvas->Stroke($path);
	}
}
?>
