<?php

/**
 * La fuente de datos encapsula un mecanismo de entrada/salida de datos, típicamente una base relacional
 * Esta clase contiene ventanas antes y despues de la conexión de la fuente y permite acceder al objeto db 
 * que es el que tiene el API de consultas/comandos
 * 
 * @package Fuentes
 */
class toba_fuente_datos
{
	protected $definicion;
	protected $db;
	
	function __construct($definicion)
	{
		$this->definicion = $definicion;
	}
	
	/**
	 * Accede al objeto db que tiene el API para consultas/comandos sobre la fuente
	 * @return toba_db
	 */
	function get_db($reusar = true)
	{
		if ($reusar) {
			if (!isset($this->db)) {
				$this->pre_conectar();
				$this->db = toba_dba::get_db_de_fuente(toba::instancia()->get_id(),
															$this->definicion['proyecto'],
															$this->definicion['fuente_datos'],
															$reusar);
				$this->post_conectar();
			}
			return $this->db;
		} else {
			return toba_dba::get_db_de_fuente(toba::instancia()->get_id(),
															$this->definicion['proyecto'],
															$this->definicion['fuente_datos'],
															$reusar);
		}
	}
	
	/**
	 * Dado el nombre de una tabla de la fuente, retorna el id de su datos_tabla asociado
	 * @param string $tabla
	 * @return int
	 */
	function get_id_datos_tabla($tabla)
	{
		if (! isset($this->definicion['mapeo_tablas_dt'])) {
			//-- Lazyload de la relacion entre tabla y dt por un tema de eficiencia
			$this->definicion['mapeo_tablas_dt'] = toba_proyecto_db::get_mapeo_tabla_dt($this->definicion['proyecto'], $this->definicion['fuente_datos']);
		}
		if (isset($this->definicion['mapeo_tablas_dt'][$tabla])) {
			return $this->definicion['mapeo_tablas_dt'][$tabla];
		} else {
			throw new toba_error("No se encuentra el datos_tabla asociado a la tabla $tabla en la fuente {$this->definicion['fuente_datos']}");
		}
	}
	
	/**
	*	Ventana para personalizar las acciones previas a la conexión
	* @ventana
	*/
	function pre_conectar() {}
	
	/**
	* Ventana para personalizar las acciones posteriores a la conexión
	* @ventana
	*/
	function post_conectar() {}
}
?>