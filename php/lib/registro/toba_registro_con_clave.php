<?php

abstract class toba_registro_con_clave extends toba_registro
{
    protected $clave;
	protected $reg_orig;

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

	function analizar_conflictos() 
	{
		$reg_original	= $this->get_registro_a_actualizar();
		$reg_count	= count($reg_original);
		
		//Ejecuto la SQL para analizar conflictos de indices
		parent::analizar_conflictos();
		if (count($this->conflictos) > 0) {
			return;	// Si el padre devolvió algún conflicto este impide que se chequeen el resto de los toba_registro_conflictos
		}

		//Analizo si el registro no fue encontrado o si no se pudo determinar cual se debia modificar
		if ($reg_count < 1) {	
			$this->conflictos[] =  new toba_registro_conflicto_inexistente($this);
		} else if ($reg_count > 1) {	
			$this->conflictos[] =  new toba_registro_conflicto_univoco($this);
		}
	}
	
	/**
	 * Hace un select con la condición del update y devuelve la cantidad el
	 * registro sobre el que se tiene que hacer el update
	 */
	protected function get_registro_a_actualizar()
	{
		if (!isset($this->reg_orig)) {
			$where	= $this->armar_where();
			$sql	= "SELECT * FROM $this->tabla WHERE $where";
			$this->reg_orig = $this->db->consultar($sql);	
		}
		
		return $this->reg_orig;
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
