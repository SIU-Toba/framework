<?php
/**
 * Esta clase representa los datos de una tarea. Provee
 * funcionalidades para que su contenido sea alterado externamente y también
 * funciona como iterador de acciones (importador_tarea_accion)
 */
class toba_tarea_datos implements Iterator {
	/**
	 * Los registros que se van a incluir en la bd. Pueden ser registros de insert
	 * o update
	 * @var ArrayIterator
	 */
	protected $it_registros;


	/**
	 * Los registros organizados por tablas. Se mantienen dos estructuras diferentes
	 * debido a los distintos usos que se le va a dar a esta clase
	 * @var array
	 */
	protected $tablas;

	function  __construct()
	{
		$this->it_registros =  new ArrayIterator(array());
		$this->tablas = array();
	}

	/**
	 * Agrega un registro al final del plan
	 * @param toba_registro $registro
	 */
	function add_registro($registro)
	{
		$this->it_registros->append($registro);
		$this->tablas[$registro->get_tabla()] = $registro;
	}

	/**
	 * Devuelve todos los registros de una tabla
	 * @param string $tabla
	 * @return array
	 */
	function get_registros($tabla)
	{
		return (isset($this->tablas[$tabla])) ? $this->tablas[$tabla] : array();
	}

	/**
	 * @return toba_registro
	 */
	public function current()
	{
		return $this->it_registros->current();
	}

	public function key() 
	{
		return $this->it_registros->key();
	}
	
	public function next() 
	{
		$this->it_registros->next();
	}

	public function rewind()
	{
		$this->it_registros->rewind();
	}

	public function valid()
	{
		return $this->it_registros->valid();
	}
}
?>
