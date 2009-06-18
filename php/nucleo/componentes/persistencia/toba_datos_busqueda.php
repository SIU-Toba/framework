<?php

/**
 * Permite construir busquedas complejas en torno a un datos_tabla específico y su relación con otras tablas
 * 
 * @package Componentes
 * @subpackage Persistencia
 */
class toba_datos_busqueda
{
	protected $relacion;
	protected $tabla;
	protected $padres = array();
	protected $condiciones = array();
	
	/**
	 * @ignore 
	 */
	function __construct($relacion, $tabla)
	{
		$this->relacion = $relacion;
		$this->tabla = $tabla;
	}

	/**
	 * Fuerza que en la búsqueda se tome en cuenta solo los registros hijos de un padre
	 *
	 * @param string $tabla_padre	Nombre del datos_tabla padre
	 * @param string $id_padre		Id. interno de la fila padre
	 */
	function set_padre($tabla_padre, $id_padre)
	{
		$this->padres[$tabla_padre] = $id_padre;
	}
	
	/**
	 * Fuerza que en la búsqueda se respete una condición sobre los registros de la tabla
	 *
	 * @param string $campo Columna de la condición
	 * @param string $operador Operador de la condición (==, ===, <, >, etc)
	 * @param mixed $valor Valor de la condición
	 */
	function set_condicion($campo, $operador, $valor)
	{
		$this->condiciones[] = array($campo, $operador, $valor);
	}
	
	/**
	 * Limpia las condiciones 
	 */
	function limpiar_condiciones()
	{
		$this->condiciones = array();	
	}

	/**
	 * Dado los criterios seteados busca los ids de los registros de la tabla
	 * @return array de Ids. de la tabla
	 */
	function buscar_ids()
	{
		$ids = $this->tabla->get_id_fila_condicion($this->condiciones, false);
		
		if (! empty($this->padres)) {
			foreach ($this->padres as $tabla_padre => $id_padre) {
				$hijas = $this->tabla->get_relacion_con_padre($tabla_padre)->get_id_filas_hijas_de($id_padre);
				$ids = array_intersect($ids, $hijas);
			}
		}
		return $ids;
	}
	
	/**
	 * Dado los criterios seteados, retorna las filas de los registros de la tabla
	 * @return array Recordset 
	 */
	function buscar_filas()
	{
		$ids = $this->buscar_ids();
		$salida = array();
		foreach ($ids as $id) {
			$salida[] = $this->tabla->get_fila($id);
		}
		return $salida;
	}
	
}


?>