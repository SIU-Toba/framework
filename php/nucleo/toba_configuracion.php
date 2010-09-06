<?php
/**
 * Esta es una clase transversal de toba que se ocupa de mantener referencias
 * a las clases de toba que pueden ser extendidas. Agregando esta indirección
 * la elección de la clase correspondiente es transparente. Cada clase que puede
 * ser extendida en un proyecto debe agregar dos métodos a esta clase:
 *		- static function clase() <-- devuelve la instancia de la clase
 *		- static function extender_clase() <--agrega una clase al mapeo
 *
 * @author sp14ab
 */
class toba_configuracion
{
	// -------------------------------------------------------------------
	// INSTANCIA
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

	// -------------------------------------------------------------------
	// ESTÁTICOS
	// -------------------------------------------------------------------
	static private $instancia;

	/**
	 * Devuelve una instancia de la configuración de toba
	 * @return toba_configuracion
	 */
	protected static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_configuracion();
		}
		return self::$instancia;
	}

	/**
	 * Si existe un mapeo a un objeto para $nombre lo devuelve. Sino, agrega
	 * $objeto_default al mapeo y devuelve el mismo.
	 * @param string $nombre
	 * @param mixed $objeto_default
	 */
	private static function get($nombre, $objeto_default)
	{
		$instancia = self::instancia();

		if (!$instancia->existe_mapeo($nombre)) {
			$instancia->add_mapeo($nombre, $objeto_default);
		}

		return $instancia->get_mapeo($nombre);
	}

	/**
	 * Extiende la configuración por defecto del ei grafico
	 * @param toba_ei_grafico_conf_global $objeto
	 */
	static function extender_ei_grafico_conf(toba_ei_grafico_conf_global $objeto)
	{
		self::instancia()->add_mapeo('ei_grafico_conf', $objeto);
	}

	/**
	 * Devuelve la clase de configuración del ei grafico
	 * @return toba_ei_grafico_conf
	 */
	static function ei_grafico_conf()
	{
		$nombre = 'ei_grafico_conf';
		$objeto = new toba_ei_grafico_conf_default();

		return self::get($nombre, $objeto);
	}

}
?>
