<?php

abstract class toba_importador {
	protected $tareas	= array();
	protected $iterador = 0;
	protected $db;
	/**
	 * @var toba_plan el plan a seguir por el importador
	 */
	protected $plan;

	function  __construct($path_plan, $db)
	{
		$this->plan =  new  toba_importador_plan($path_plan);
		$this->db = $db;
		$this->cargar_tareas();
	}

	function rewind()
	{
		$this->iterador = 0;
	}
	
	/**
	 *
	 * @return importador_tarea
	 */
	function get_siguiente_tarea()
	{
		if (!isset($this->tareas[$this->iterador])) {
			return null;
		}
	
		return $this->tareas[$this->iterador++];	// Devolvemos la siguiente tarea y aumentamos el iterador
	}

	/**
	 * Carga las tareas a realizar por el importador
	 */
	abstract protected function cargar_tareas();

}

?>
