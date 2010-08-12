<?php
/**
 * Esta es una clase transversal de toba que se ocupa de mantener referencias
 * a las clases de toba que pueden ser extendidas. Agregando esta indirecci�n
 * la elecci�n de la clase correspondiente es transparente. Cada clase que puede
 * ser extendida en un proyecto debe agregar un m�todo a esta clase.
 * Se deja a modo ejemplificativo de uso la configuraci�n para impresi�n html aunque
 * no se utiliza por compatibilidad hacia atr�s
 *
 * @author sp14ab
 */
class toba_configuracion
{
	// -------------------------------------------------------------------
	// EST�TICOS
	// -------------------------------------------------------------------
	static private $instancia;

	/**
	 * @return toba_configuracion
	 */
	protected static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_configuracion();
		}
		return self::$instancia;
	}

	
	static function extender_impresion_html(toba_impr_html $objeto)
	{
		self::instancia()->add_mapeo('impresion_html', $objeto);
	}

	static function impresion_html()
	{
		$instancia = self::instancia();
		$nombre = 'impresion_html';

		if (!$instancia->existe_mapeo($nombre)) {
			$instancia->add_mapeo($nombre, new toba_impr_html());
		}

		return $instancia->get_mapeo($nombre);
	}

	// -------------------------------------------------------------------
	// DIN�MICOS
	// -------------------------------------------------------------------
	protected $mapeo;
	
	protected function __construct()
	{
		$this->mapeo = array();

	}

	function add_mapeo($nombre, $objeto)
	{
		$this->mapeo[$nombre] = $objeto;
	}

	function get_mapeo($nombre)
	{
		return $this->mapeo[$nombre];
	}

	function existe_mapeo($nombre)
	{
		return isset($this->mapeo[$nombre]);
	}
}
?>
