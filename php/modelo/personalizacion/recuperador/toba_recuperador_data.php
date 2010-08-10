<?php

class toba_recuperador_data
{
    protected $data;

	function  __construct()
	{
		$this->data = array();
	}

	function get_unicos($schema)
	{
		if (!isset($this->data[$schema])) {
			throw  new toba_error("toba_bi_schema_data: El schema $schema no es válido");
		}
		return $this->data[$schema];
	}

	function get_diferentes()
	{
		return $this->data['diferentes'];
	}

	function set_unicos($schema, &$data)
	{
		$this->data[$schema] = $data;
	}

	function set_diferentes(&$data)
	{
		$this->data['diferentes'] = $data;
	}
}
?>
