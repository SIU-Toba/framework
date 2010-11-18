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
	protected $_info_orden_col = array();		//Mantiene las columnas por las que se debe ordenar.
	
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
		if ($this->hay_ordenamiento()) {
			$salida = $this->ordenar_datos_x_columnas($salida);
		}
		return $salida;
	}

	/**
	 * Verifica si existe ordenamiento de los datos o no
	 * @return boolean
	 */
	function hay_ordenamiento()
	{
		return (! empty($this->_info_orden_col));
	}

	/**
	 * Retorna un arreglo con las columnas definidas para el ordenamiento
	 * @return array
	 */
	function get_columnas_orden()
	{
		return $this->_info_orden_col;
	}

	/**
	 * Fija que columnas se utilizaran para realizar el ordenamiento de los datos,
	 * el orden posicional en el arreglo define que columna se utiliza antes.
	 * @param array $cols Arreglo asociativo de columnas => sentido ej: array('id_persona' => SORT_ASC, 'nombre' => SORT_DESC, 'descripcion_trabajo' => SORT_ASC)
	 */
	function set_columnas_orden($cols = array())
	{
		//Tengo que verificar que las columnas pertenecen a la tabla.
		$cols_existentes = array_keys($this->tabla->get_columnas());		
		foreach($cols as $columna => $sentido) {
			if (! in_array($columna, $cols_existentes)) {
				throw new toba_error_def('La columna por la que intenta ordenar no existe: '. $columna);
			}
		}
		$this->_info_orden_col = $cols;
	}

	/**
	 * Ordena el conjunto de datos recibido segun lo especificado en $this->_info_orden_col
	 * @param recordset $datos_orig		Conjunto de datos a ordenar
	 * @return recordset $datos_destino Conjunto de datos ordenados
	 * @ignore
	 */
	function ordenar_datos_x_columnas($datos_orig)
	{
		if (! empty($this->_info_orden_col) && ! empty($datos_orig)) {
			$columnas = array_keys($this->_info_orden_col);
			$datos_destino = rs_ordenar_por_columnas($datos_orig, $columnas, $this->_info_orden_col);
		} else {
			$datos_destino = $datos_orig;
		}
		return $datos_destino;
	}
}
?>