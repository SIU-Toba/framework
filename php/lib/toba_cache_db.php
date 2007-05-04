<?php
/**
 * @ignore 
 * 	Levanta el contenido de las tablas a la memoria y crea indices
 * 	para porder solicitar subconjuntos de datos
 */
class toba_cache_db
{
	private $db;
	private $tablas = array();
	private $indices = array();

	function __construct( $db )
	{
		$this->db = $db;
	}

	/*
	*	Recuperacion de datos del CACHE
	*/
	function get_datos_tabla( $tabla, $clave )
	{
		if (isset($this->tablas[$tabla])) {
			if (isset($this->indices[$tabla][$clave])) {
				foreach ($this->indices[$tabla][$clave] as $fila ) {
					$datos[] = $this->tablas[$tabla][$fila];
				}
				return $datos;
			} else {
				//En el caso de algunas tablas, esto puede pasar 
				//throw new toba_error("CACHE: La clave '$clave' de la tabla '$tabla' no existe");
			}
		} else {
			throw new toba_error("CACHE: La tabla '$tabla' no existe");
		}
	}

	/*
	*	Levanta una tabla a la memoria y la indexa
	*/
	function agregar_tabla( $tabla, $sql, $columna_clave )
	{
		//Recupero el contenido
		$this->tablas[$tabla] = $this->db->consultar( $sql );

		//Genero indices
		$this->indices[$tabla] = array();
		for ($a=0; $a < count($this->tablas[$tabla]); $a++) {
			$id = $this->tablas[$tabla][$a][$columna_clave];
			$this->indices[$tabla][$id][] = $a;
		}
	}

	/*
	*	Muestra un resumen del contenido del CACHE
	*/
	function info()
	{
		foreach ($this->tablas as $tabla => $contenido) {
			$resumen[$tabla]['registros'] = count($contenido);
			$resumen[$tabla]['indices'] = count($this->indices[$tabla]);
		}	
		return $resumen;
	}
}
?>
