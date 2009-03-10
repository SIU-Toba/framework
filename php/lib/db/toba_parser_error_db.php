<?php

/**
 * @package Fuentes
 */
abstract class toba_parser_error_db
{
	function __construct($id=null, $proyecto=null) {
		$this->id_db_original = $id;
		$this->proyecto_original = $proyecto;
	}
		
	/**
	 * Para poder leer los metadatos de postgres se necesita hacer una conexión extra a la base
	 * ya que la original tiene el error aguardando una resolución (por ejemplo en una transacción)
	 * @return toba_db
	 */
	protected function get_conexion_extra()
	{
		if (! isset($this->conexion_extra)) {
			$this->conexion_extra = toba_admin_fuentes::instancia()->get_fuente($this->id_db_original, $this->proyecto_original)->get_db(false);
		}
		return $this->conexion_extra;
	}
	
	/**
	 * Retorna un verbo asociado a la acción (actualizando, insertando, borrando)
	 */
	protected function get_accion($sql)
	{
		$sql = trim($sql);
		$pos = array();
		$pos['insertando'] 	= stripos($sql, 'INSERT');
		$pos['actualizando'] = stripos($sql, 'UPDATE');
		$pos['eliminando'] 	= stripos($sql, 'DELETE');
		$mejor = 100000;
		$mensaje = '';
		//-- Busca cual ocure primero en el sql
		foreach ($pos as $clave => $posicion) {
			if ($posicion !== false && $posicion < $mejor) {
				$mejor = $posicion;
				$mensaje = $clave;
			}
		}
		return $mensaje;
	}	
	
	abstract function parsear($sql, $sqlstate, $mensaje);
}

?>
