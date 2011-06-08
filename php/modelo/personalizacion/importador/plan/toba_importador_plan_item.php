<?php

class toba_importador_plan_item {
	protected $path_metadatos;
	protected $id;
	protected $tipo;
	protected $path;

	function __construct($path_metadatos, $tipo, $id, $path = null)
	{
		$this->path_metadatos = $path_metadatos;
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

	function get_path_absoluto()
	{
		return $this->path_metadatos .'/'. $this->path;
	}
}
?>
