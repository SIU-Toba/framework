<?php

abstract class toba_registro_con_clave extends toba_registro
{
    protected $clave;

	function  __construct($db, $nombre_tabla)
	{
		parent::__construct($db, $nombre_tabla);
	}


	function add_clave($columna, $valor)
	{
		$this->clave[$columna] = $valor;
	}

	/**
	 * Cada clave es una dupla campo:valor. Si hay varias claves se devuelven
	 * las duplas separadas por coma (Como vienen especificadas en el xml).
	 */
	function get_clave()
	{
		$res = array();

		foreach ($this->clave as $key => $valor) {
			$res[] = "$key:$valor";
		}

		return implode(',', $res);
	}

	function get_conflictos()
	{
		$conflictos	= parent::get_conflictos();
		if (count($conflictos) > 0) {
			return $conflictos;	// Si el padre devolvió algún conflicto este impide que se chequeen el resto de los toba_registro_conflictos
		}

		$reg_original	= $this->get_registro_a_actualizar();
		$reg_count		= count($reg_original);

		if ($reg_count < 1) {	// No existe el registro a actualizar
			$conflictos[] =  new toba_registro_conflicto_inexistente($this);
		} else if ($reg_count > 1) {	// No se puede identificar unívocamente el registro a actualizar
			$conflictos[] =  new toba_registro_conflicto_univoco($this);
		}

		return $conflictos;
	}

	/**
	 * Hace un select con la condición del update y devuelve la cantidad el
	 * registro sobre el que se tiene que hacer el update
	 */
	protected function get_registro_a_actualizar()
	{
		$where	= $this->armar_where();
		$sql	= "SELECT * FROM $this->tabla WHERE $where";
		$regs	= $this->db->consultar($sql);
		return $regs;
	}


	protected function armar_where()
	{
		$keys = array();

		foreach ($this->clave as $columna => $valor) {
			$keys[] = $columna.' = '.$this->db->quote($valor);
		}

		return implode(' AND ', $keys);
	}
}
?>
