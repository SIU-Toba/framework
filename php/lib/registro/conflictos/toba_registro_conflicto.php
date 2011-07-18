<?php

abstract class toba_registro_conflicto
{
	/**
	 * Conflicto irresoluble
	 */
	const fatal = 'fatal';

	/**
	 * Conflicto resoluble
	 */
	const warning = 'warning';

	protected $tipo;

	protected $numero;
	/**
	 * @var toba_registro
	 */
	protected $registro;
	
	protected $descripcion_componente; 

	function __construct($registro)
	{
		$this->registro = $registro;
	}

	function get_tipo()
	{
		return $this->tipo;
	}

	function get_numero()
	{
		return $this->numero;
	}

	function set_descripcion_componente($desc)
	{
		$this->descripcion_componente = $desc;
	}
	
	abstract function get_descripcion();
}
?>
