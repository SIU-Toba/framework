<?php
require_once(toba_dir() . '/php/3ros/JSON.php');

/**
 * Clase que representa una respuesta AJAX (php => js)
 * Tiene dos metodos, uno pensado para comportamientos de alto nivel (set) en donde el framework interpreta el tipo de salida
 * y otro para bajo nivel (agregar) en donde el manejo de la información queda a cargo del consumidor
 */
class toba_ajax_respuesta
{
	protected $modo;
	protected $contenido;
	protected $secciones = array();
	
	/**
	 * @ignore 
	 */	
	function __construct($modo)
	{
		$this->modo = $modo;
	}
	
	/**
	 * Método de alto nivel, construye la respuesta en base al contenido pasado.
	 * Cuando se comunica este contenido a JS se adecua el formato según como fue el pedido inicialmente (datos o html)
	 * @param mixed $contenido
	 */
	function set($contenido)
	{
		$this->contenido = $contenido;
	}
	
	/**
	 * Método de bajo nivel: Permite armar la respuesta a js basado en un conjunto de secciones identificadas por un nombre
	 * Este método puede ser llamado cuantas veces se necesite acumulando la respuesta
	 * La serializacion/deserialización de los datos queda a cargo del consumidor
	 * @param string $nombre
	 * @param mixed $contenido
	 */
	function agregar($nombre, $contenido)
	{
		$this->secciones[$nombre] = $contenido;
	}
	
	/**
	 * @ignore 
	 */
	function comunicar()
	{
		if (isset($this->contenido) && isset($this->modo)) {
			switch ($this->modo) {
				case 'D':
					$json = new Services_JSON();
					toba::logger()->debug("Respuesta AJAX: ".var_export($this->contenido, true));	
					echo $json->encode($this->contenido);
					break;
				case 'H':
					echo $this->contenido;
					break;
			}
		} elseif (!empty($this->secciones)) {
			foreach ($this->secciones as $seccion => $contenido) {
				echo "<--toba: $seccion-->";
				echo $contenido;
			}
		}
	}

	
	
}