<?php

/**
 * Representa la relacion entre dos tablas
 * Esta clase no tiene importancia para el consumo del framework, ya que es una estructura interna utilizada para relacionar datos_tabla
 *  - Las relaciones se arman macheando posicionalmente columnas
 *  - El comportamiento de esta clase varia segun la cantidad de registros  que maneja el padre... con N registros se suma el problema de recuperacion y seteo discrecional de HIJOS
 * @access private
 * @package Componentes
 * @subpackage Persistencia
 * @todo actualizacion dinamica del MAPEO de filas
 * @todo cuando sea necesario el mapeo de filas, esta clase va tener que mantener su estado en la sesion
 */
class toba_relacion_entre_tablas
{
	protected $tabla_padre;					// Referencia al toba_datos_tabla PADRE
	protected $tabla_padre_claves;
	protected $tabla_padre_id;
	protected $tabla_hijo;					// Referencia al toba_datos_tabla HIJO
	protected $tabla_hijo_claves;
	protected $tabla_hijo_id;
	protected $mapeo_campos = array();
	protected $mapeo_filas = array();
	protected $mapeo_filas_eliminadas = array();
	protected $borrado_en_cascada = true;
	protected $es_relacion_de_inclusion = true;

	function __construct($tabla_padre, $tabla_padre_clave, $tabla_padre_id, 
							$tabla_hijo, $tabla_hijo_clave, $tabla_hijo_id)
	{
		toba_asercion::arrays_igual_largo($tabla_padre_clave, $tabla_hijo_clave);
		$this->tabla_padre = $tabla_padre;
		$this->tabla_padre_claves = $tabla_padre_clave;
		$this->tabla_padre_id = $tabla_padre_id;
		$this->tabla_hijo = $tabla_hijo;	
		$this->tabla_hijo_claves = $tabla_hijo_clave;
		$this->tabla_hijo_id = $tabla_hijo_id;
		//Notifico la existencia de la relacion a las tablas
		$this->tabla_padre->agregar_relacion_con_hijo( $this, $this->tabla_hijo_id );
		$this->tabla_hijo->agregar_relacion_con_padre( $this, $this->tabla_padre_id );
		$this->mapear_campos();
	}
	
	function get_txt()
	{
		return "Relacion entre {$this->tabla_padre_id} y {$this->tabla_hijo_id}: ";	
	}
	
	//-------------------------------------------------------------------------------
	//-- CONFIGURACION
	//-------------------------------------------------------------------------------
	
	function set_mapeo_filas($mapeo)
	{
		$this->mapeo_filas = $mapeo;	
	}
	
	function set_mapeo_filas_eliminadas($mapeo)
	{
		$this->mapeo_filas_eliminadas = $mapeo;
	}

	function set_relacion_inclusion($inclusion)
	{
		$this->es_relacion_de_inclusion = $inclusion;
	}
	
	function get_mapeo_filas()
	{
		return $this->mapeo_filas;
	}	

	function get_mapeo_filas_eliminadas()
	{
		return $this->mapeo_filas_eliminadas;
	}

	function get_mapeo_campos()
	{
		return $this->mapeo_campos;	
	}
	
	/**
	 * Macheo de campos claves entre PADRE e HIJO
	 */
	function mapear_campos() 
	{
		for($a=0;$a<count($this->tabla_padre_claves);$a++){
			$this->mapeo_campos[ $this->tabla_padre_claves[$a] ] = $this->tabla_hijo_claves[$a];
		}
	}
	
	function tabla_padre()
	{
		return $this->tabla_padre;	
	}
	
	function tabla_hijo()
	{
		return $this->tabla_hijo;	
	}
	
	//-------------------------------------------------------------------------------
	//-- CARGA
	//-------------------------------------------------------------------------------	
	
	/**
	 * Retorna una clausula where restringiendo los campos relacionados según un select de una tabla padre
	 * @ignore 
	 */
	function generar_clausula_subselect($alias_hija)
	{
		if ($this->es_relacion_de_inclusion) {
			$persistidor_padre = $this->tabla_padre()->persistidor();
			$mapeo_campos = $this->get_mapeo_campos();
			
			//Campos a comparar con el subselect
			$where_subselect = '(';
			foreach ($mapeo_campos as $campo) {
				$where_subselect .= $alias_hija . '.' . $campo . ', ';
			}
			$where_subselect = substr($where_subselect, 0, -2);	//Elimina la ultima coma
					
			$subselect = $this->tabla_padre()->persistidor()->get_sql_de_carga(array_keys($mapeo_campos));
			$subselect = str_replace("\n", "\n\t\t", $subselect);
			$where_subselect .= ") IN (\n\t\t$subselect )";
			return $where_subselect;
		}
	}
	
	//-------------------------------------------------------------------------------
	//-- EVENTOS DISPARADOS EN LA TABLAS RELACIONADAS
	//-------------------------------------------------------------------------------
	
	/**
	 * El elemento HIJO de la relacion notifica que se CARGO.
	 * Se arman los mapeos de las filas
	 */
	function evt__carga_hijo($reg_hijos=null)
	{
		//Se mapean las filas de las tablas
		//Si la tabla padre tiene un cursor, son todos hijos de este
		if( $this->tabla_padre->hay_cursor() && ! isset($reg_hijos)) {
			//Los mapeos son simples, son todos hijos del cursor
			$id_padre = $this->tabla_padre->get_cursor();
			$this->mapeo_filas[$id_padre] = $this->tabla_hijo->get_id_filas(false);
		} else {
			//Se arman los mapeos de filas
			//Quizás se podría optimizar recorriendo en primer lugar los hijos
			if (! isset($reg_hijos)) {
				// Mapeo todos los padres a todos los hijos
				$ids_filas_padres = $this->tabla_padre->get_id_filas(false);
				foreach($ids_filas_padres as $id_padre) {
					$fila_padre = $this->tabla_padre->get_fila($id_padre);
					$claves = $this->mapear_fila_a_formato_hijo($fila_padre);
					$hijas = $this->tabla_hijo->get_id_fila_condicion($claves, false);
					$this->mapeo_filas[$id_padre] = $hijas;
				}
			} else {
				// Mapeo hijos a padre para un conjunto de filas hijas
				foreach($reg_hijos as $id_fila_hijo) {
					$fila_hijo = $this->tabla_hijo->get_fila($id_fila_hijo);
					$claves = $this->mapear_fila_a_formato_padre($fila_hijo);
					$padre = $this->tabla_padre->get_id_fila_condicion($claves, false);
					if (count($padre)!=1) {
						throw new toba_error("RELACION entre TABLAS: MAPEO de hijos a padres: se encontro mas de un padre para un hijo.");
					}
					$id_padre = $padre[0];
					$this->mapeo_filas[$id_padre][] = $id_fila_hijo;
				}
			}
		}
	}
	
	/**
	 * El elemento PADRE de la relacion notifica que se SINCRONIZO:
	 * Se propagan sus valores a los hijos
	 */
	function evt__sincronizacion_padre($filas=array())
	{
		//Si la tabla solo sincronizo filas (usando un cursor, solo hay que disparar
		//la modificacion de dichas filas
		if($filas) {
			$filas_padre = $filas;	
		} else {
			$filas_padre = 	$this->tabla_padre->get_id_filas(false);
		}
		//Se recorre cada fila 'viva' del padre 
		foreach ($filas_padre as $id_fila_padre) {
			$fila_padre = $this->tabla_padre->get_fila($id_fila_padre);
			//Se busca las filas hijas relacionadas con la fila padre
			if (isset($this->mapeo_filas[$id_fila_padre])) {
				foreach ($this->mapeo_filas[$id_fila_padre] as $id_fila_hijo) {
					//Se mapea cada campo relacionado a la fila hija
					foreach($this->mapeo_campos as $columna_padre => $columna_hijo){
						$this->tabla_hijo->set_fila_columna_valor($id_fila_hijo, $columna_hijo, 
																	$fila_padre[$columna_padre]);
					}
				}
			}
		}
	}

	/**
	 * El padre notifica que se elimina una fila
	 * Si esta activado el borrado en cascada se elimina recursivamente a los hijos
	 */
	function evt__eliminacion_fila_padre($id)
	{
		//¿Hay filas hijos?
		if (isset($this->mapeo_filas[$id]) && !empty($this->mapeo_filas[$id])) {
			if (!$this->borrado_en_cascada) {
				throw new toba_error_def($this->get_txt_error_base("No está permitido el borrado en cascada"));
			}
			//Borra las filas en cascada
			foreach ($this->mapeo_filas[$id] as $hijo) {
				$this->tabla_hijo->eliminar_fila($hijo);
			}
			//Borra el mapeo
			unset($this->mapeo_filas[$id]);			
		}
	}
	
	/**
	 * El hijo notifica que se elimina una fila, se saca del mapeo
	 */
	function evt__eliminacion_fila_hijo($id)
	{
		$pos_padre = $this->buscar_padre_de($id);
		if (is_array($pos_padre)) {
			$this->mapeo_filas_eliminadas[$pos_padre[0]][$pos_padre[1]] = 
				$this->mapeo_filas[$pos_padre[0]][$pos_padre[1]];
			unset($this->mapeo_filas[$pos_padre[0]][$pos_padre[1]]);
		}
	}	
	
	//--------------- ----------------------------------------------------------------
	//-- CONSULTAS EN MEMORIA
	//-------------------------------------------------------------------------------
	
	/**
	 * Retorna aquellas filas cuyo padre en la tabla relacionada es el paráemtro
	 *
	 * @param mixed $id_padre Id. interno de la fila padre
	 * @return array Arreglo de ids. internos de la filas hijas
	 */
	function get_id_filas_hijas_de($id_padre,$incluir_hijos_eliminados=false)
	{
		if (isset($this->mapeo_filas[$id_padre])) {
			$filas = $this->mapeo_filas[$id_padre];
			if ($incluir_hijos_eliminados) {
				if (isset($this->mapeo_filas_eliminadas[$id_padre])) {
					$filas = array_merge($filas, $this->mapeo_filas_eliminadas[$id_padre]);
				}
			}
			return $filas;
		} else { 
			return array();
		}
	}

	/**
	 * Filtra un conjunto de filas hijas de acuerdo al estado de sus padres
	 */
	function filtrar_filas_hijas($filas, $incluir_hijos_eliminados=false)
	{
		if ($this->hay_cursor_en_padre()) {
			return array_intersect($filas, $this->get_id_filas_hijas($incluir_hijos_eliminados));
		} else {
			if ($this->es_relacion_de_inclusion) {
				return array();
			} else {
				//--- Si no tiene un padre estricto, no se filtra nada
				return $filas;	
			}
		}
	}
	
	/**
	 * Determina si la tabla padre tiene una fila seteada como 'actual'
	 */
	function hay_cursor_en_padre()
	{
		return $this->tabla_padre->hay_cursor();
	}
	
	/**
	 * Dada la condicion del cursor de la tabla padre, retorna las filas hijas asociadas
	 */
	function get_id_filas_hijas($incluir_hijos_eliminados=false)
	{
		if ($this->hay_cursor_en_padre()) {
			return $this->get_id_filas_hijas_de($this->tabla_padre->get_cursor(), $incluir_hijos_eliminados);
		} else {
			throw new toba_error_def($this->get_txt_error_base("La tabla padre no tiene definido un cursor"));
		}
	}

	/**
	 * Dada una fila hija específica, retorna la fila padre asociada, si es que la tiene
	 */
	function get_id_padre($id_fila_hijo)
	{
		foreach (array_keys($this->mapeo_filas) as $padre) {
			$pos = array_search($id_fila_hijo, $this->mapeo_filas[$padre]);
			if ($pos !== false && $pos !== '') {
				return $padre;
			}
		}
	}

	/**
	 * Busca en la tabla padre el id que machea con la fila hijo
	 * @param array $fila_hijo Asociativo campo-valor, en la fila deben estar seteados aquellos campos que mantienen la asociación con la tabla padre
	 */
	protected function buscar_id_padre_fila($fila_hijo)
	{
		$condicion = $this->mapear_fila_a_formato_padre($fila_hijo);
		$id_padre = $this->tabla_padre->get_id_fila_condicion($condicion, false);
		if (count($id_padre) == 1) {
			return current($id_padre);
		} else {
			$desc_hijo = var_export($fila_hijo, true);
			if (empty($id_padre)) {
				throw new toba_error_def($this->get_txt_error_base("No se encuentra una fila padre. Fila hija: $desc_hijo"));
			} else {
				throw new toba_error_def($this->get_txt_error_base("Estructura corrupta. Se encuentra más de una fila padre. Fila hija: $desc_hijo"));
			}
		}		
	}
	

	/**
	 * Busca en el mapeo el padre que tiene un hijo dado.
	 * @param mixed $id_fila_hijo Id. interno de la fila hijo
	 * @return array [0] => id. interno del padre , [1] => posición del hijo dentro del mapeo
	 */
	function buscar_padre_de($id_fila_hijo)
	{
		foreach (array_keys($this->mapeo_filas) as $padre) {
			$pos = array_search($id_fila_hijo, $this->mapeo_filas[$padre]);
			if ($pos !== false) {
				return array($padre, $pos);
			}
		}
		return false;
	}

	protected function get_txt_error_base($error="Ha ocurrido un error")
	{
		$txt = "RELACION:\n TABLA padre: " . $this->tabla_padre->get_txt() 
				. " -- ". $this->tabla_padre_id . " -- [". $this->tabla_padre->get_nombre()  . "]\n";
		$txt .= "TABLA hijo: " . $this->tabla_hijo->get_txt() 
				. " -- ". $this->tabla_hijo_id . " -- [". $this->tabla_hijo->get_nombre() . "]\n";
		$txt .= $error;
		return $txt;
	}
	
	//-------------------------------------------------------------------------------
	//-- COMANDOS EN MEMORIA
	//-------------------------------------------------------------------------------
	
	/**
	 * Retorna la traducción de los campos de la fila padre a la hija
	 * @param array $fila_padre RecodSet de la fila en formato del padre
	 * @return array La misma fila en formato del hijo
	 */
	protected function mapear_fila_a_formato_hijo($fila_padre)
	{
		$fila_hija = array();
		foreach($this->mapeo_campos as $c_padre => $c_hijo) {
			$fila_hija[$c_hijo] = $fila_padre[$c_padre];
		}
		return $fila_hija;
	}
	
	/**
	 * Retorna la traducción de los campos de la fila hija a la padre
	 * @param array $fila_hijo RecodSet de la fila en formato del hijo
	 * @return array La misma fila en formato del padre
	 */
	protected function mapear_fila_a_formato_padre($fila_hijo)
	{
		$fila_padre = array();
		foreach($this->mapeo_campos as $c_padre => $c_hijo) {
			$fila_padre[$c_padre] = $fila_hijo[$c_hijo];
		}
		return $fila_padre;
	}
	
	protected function cargar_tabla_hijo()
	{
		if( $this->tabla_padre->get_cantidad_filas() == 1) {
			//Si la tabla padre tiene un solo registro, la carga se realiza utilizando la clave de este
			$fila = $this->tabla_padre->get_fila($this->tabla_padre->get_cursor());
			$claves = $this->mapear_fila_a_formato_hijo($fila);
			$this->tabla_hijo->persistidor()->cargar_por_clave($claves);
		} else {
			//Se presentan los persistidores entre sí para elaborar el mecanismo de carga
			$this->tabla_hijo->persistidor()->cargar_en_base_a_padre(
														$this->tabla_padre->persistidor(), 
														$this->mapeo_campos);
		}
	}


	/**
	 * Reemplaza o crea el mapeo de una fila hija por un nuevo padre
	 * @param mixed $id_fila_hijo Id. interno de la fila que cambia su padre
	 * @param mixed $id_nuevo_padre Id. interno de la nueva fila padre, si es NULL solo borra la asociacion
	 * @return boolean True si cambio de padre, false si no hubo cambio porque no se necesitaba
	 */
	function set_padre($id_fila, $id_padre) 
	{
		$actual = $this->buscar_padre_de($id_fila);
		if ($actual !== false && $actual != $id_padre) {
			//--Si tiene padre lo cambia
			$this->cambiar_padre($id_fila, $id_padre);
			return true;
		} elseif ($id_padre !== null) {
			//--Si no tenia y ahora si, lo crea
			$this->asociar_fila_con_padre($id_fila, $id_padre);
			return true;
		} 
		return false;
	}
	
	
	/**
	 * Reemplaza el mapeo de una fila hija por un nuevo padre
	 * @param mixed $id_fila_hijo Id. interno de la fila que cambia su padre
	 * @param mixed $id_nuevo_padre Id. interno de la nueva fila padre, si es NULL solo borra la asociacion
	 */
	protected function cambiar_padre($id_fila_hijo, $id_nuevo_padre)
	{
		$pos = $this->buscar_padre_de($id_fila_hijo);
		if ($pos === false) {
			throw new toba_error_def($this->get_txt_error_base("No fue posible encontrar el padre actual de la fila $id_fila_hijo"));
		}
		//Se borra la asociación actual con el padre
		unset($this->mapeo_filas[$pos[0]][$pos[1]]);
		//Se asocia con el nuevo padre
		if (isset($id_nuevo_padre)) {
			$this->mapeo_filas[$id_nuevo_padre][] = $id_fila_hijo;
		}
	}
	
	
	/**
	 * Asocia una fila hija con su padre
	 *
	 * @param mixed $id_hijo Id. interno de la fila hijo
	 * @param mixed $id_padre Id. interno de la fila padre, si no se explicita que es la actualmente seleccionada en esa tabla
	 * @param array $fila_hijo Asociativo campo-valor de la fila hijo
	 * @throws toba_error_def En caso de que no se pase id_padre y la tabla padre no tenga cursor asociado
	 */
	function asociar_fila_con_padre($id_hijo, $id_padre=null)
	{
		//Si no se paso el padre, hay que encontrarlo...
		if (!isset($id_padre)) {
			if (! $this->tabla_padre->hay_cursor() && $this->es_relacion_de_inclusion) {
				throw new toba_error_def($this->get_txt_error_base("Se intenta crear o actualizar una fila y su fila padre aún no existe"));
			}
			$id_padre = $this->tabla_padre->get_cursor();
		} 
		//--Si se encontro el padre se asocia
		if (isset($id_padre)) {
			$this->mapeo_filas[$id_padre][] = $id_hijo;
		}
	}
	
	function resetear()
	{
		$this->mapeo_filas = array();
		$this->mapeo_filas_eliminadas = array();
	}
}
?>
