<?php

class toba_importador_plan_item {
	protected $id;
	protected $tipo;
	protected $path;

	function __construct($tipo, $id, $path = null)
	{
		$this->id	= $id;
		$this->tipo = $tipo;
		$this->path = $path;
	}

	function get_id()
	{
		return $this->id;
	}

	function get_tipo()
	{
		return $this->tipo;
	}

	function get_path()
	{
		return $this->path;
	}
}
?>
