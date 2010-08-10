<?php

class toba_registro_delete extends toba_registro_con_clave
{
	function  __construct($db, $nombre_tabla)
	{
		parent::__construct($db, $nombre_tabla);
		$this->tipo  = self::registro_delete;
	}

    function to_sql()
	{
		$where = $this->armar_where();

		$sql = "DELETE FROM $this->tabla WHERE $where";

		return $sql;
	}

}
?>
