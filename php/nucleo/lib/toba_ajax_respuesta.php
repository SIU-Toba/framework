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
	protected $contenido = null;
	
	/**
	 * @ignore 
	 */	
	function __construct($modo)
	{
		$this->modo = $modo;
	}
	
	/**
	 * Construye la respuesta en base al contenido pasado.
	 * Cuando se comunica este contenido a JS se adecua el formato según como fue el pedido inicialmente (datos o html)
	 * @param mixed $contenido
	 */
	function set($contenido)
	{
		$this->contenido = $contenido;
	}
	
	/**
	 * Construye la respuesta gradualmente a partir de pares (clave, valor) cada uno de estos valores no será codificado ni en php ni decodificado en js
	 * En caso de necesitar codificacion/decodificacion queda a cargo del consumidor
	 */
	function agregar_string($clave, $valor)
	{
		$this->contenido[$clave] = $valor;
	}
	
	/**
	 * @ignore 
	 */
	function comunicar()
	{
		if (isset($this->modo)) {
			switch ($this->modo) {
				case 'D':
					$json = new Services_JSON();
					toba::logger()->debug("Respuesta AJAX: ".var_export($this->contenido, true));	
					echo $json->encode($this->contenido);
					break;
				case 'H':
					echo $this->contenido;
					break;
				case 'P':
					if (is_array($this->contenido)) {
						foreach ($this->contenido as $clave => $valor) {
							echo "<--toba:$clave-->";
							echo $valor;
						}
					}
					break;
			}
		}
	}

	
	
}