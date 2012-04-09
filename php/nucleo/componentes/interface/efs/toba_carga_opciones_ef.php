<?php

/**
 * Clase encargada de coordinar la carga de opciones de los distintos efs de un formulario o filtro
 * @package Componentes
 * @subpackage Efs
 */
class toba_carga_opciones_ef
{
	protected $_efs;
	protected $_parametros_carga_efs;
	protected $_cascadas_maestros = array();		//Arreglo de maestros indexados por esclavo
	protected $_cascadas_esclavos = array();		//Arreglo de esclavos indexados por maestro	
	protected $_controlador;
	
	/**
	 * @param toba_componente $componente
	 * @param array $efs Lista de objetos efs
	 * @param array $parametros_carga
	 */
	function __construct($componente, $efs, $parametros_carga) 
	{
		if (! isset($efs)) {
			$efs = array();
		}
		$this->_controlador = $componente;		
		$this->_efs = $efs;
		$this->_parametros_carga_efs = $parametros_carga;
	}
	
	/**
	 * Analiza los efs buscando maestros y esclavos y notificandolos entre si
	 * @ignore 
	 */
	function registrar_cascadas()
	{
		$this->_cascadas_maestros = array();
		$this->_cascadas_esclavos = array();
		
		foreach (array_keys($this->_efs) as $esclavo) {
			$this->_cascadas_maestros[$esclavo] = $this->_efs[$esclavo]->get_maestros();
			foreach ($this->_cascadas_maestros[$esclavo] as $maestro) {
				if (! isset($this->_efs[$maestro])) {
					throw new toba_error_def("Cascadas: El ef '$maestro' no esta definido");
				}
				$this->_cascadas_esclavos[$maestro][] = $esclavo;

				$id_form_dep = $this->_efs[$esclavo]->get_id_form();
				$js = $this->_controlador->get_objeto_js().".cascadas_cambio_maestro('$maestro')";
				$this->_efs[$maestro]->set_cuando_cambia_valor($js);
			}
		}
	}	
	
	function get_cascadas_esclavos()
	{
		return $this->_cascadas_esclavos;
	}
	
	function get_cascadas_maestros()
	{
		return $this->_cascadas_maestros;
	}	
	
	/**
	 * Determina si todos los maestros de un ef esclavo poseen datos
	 * @return boolean
	 */
	function ef_tiene_maestros_seteados($id_ef)
	{
		foreach ($this->_cascadas_maestros[$id_ef] as $maestro) {
			if (! $this->_efs[$maestro]->tiene_estado()) {
				return false;
			}
		}
		return true;			
	}	

	/**
	 * Carga los efs que permiten seleccionar su valor a partir de opciones
	 * @ignore 
	 */
	function cargar()
	{
		foreach ($this->_efs as $id_ef => $ef) {
			if ($this->ef_requiere_carga($id_ef)) {
				$param = array();
				//-- Tiene maestros el ef? Todos tienen estado?
				$cargar = true;
				$tiene_maestros = false;
				if (isset($this->_cascadas_maestros[$id_ef]) && !empty($this->_cascadas_maestros[$id_ef])) {
					$tiene_maestros = true;
					foreach ($this->_cascadas_maestros[$id_ef] as $maestro) {
						if ($this->_efs[$maestro]->tiene_estado()) {
							$estado = $this->_efs[$maestro]->get_estado();
							$param[$maestro] = $estado;
						} else {
							$cargar = false;
						}
					}
				}
				//--- Existe la posibilidad que no tenga maestros y ya ha sido cargado anteriormente
				//--- En este caso se evita una re-carga porque se asume que no hay condiciones que puedan variar las opciones
				$cargado = false;
				if (! $tiene_maestros && $cargar) {
					if ($this->_efs[$id_ef]->tiene_opciones_cargadas()) {
						$cargado = true;
					}
				}
				if (! $cargado) {
					$datos = null;
					if ($cargar) {
						if ($this->_efs[$id_ef]->carga_depende_de_estado()) {	
							//--- Caso del popup
							$estado = $this->_efs[$id_ef]->get_estado();
							if (isset($estado)) {
								$datos = $this->ejecutar_metodo_carga_descripcion_ef($id_ef, $estado);
							}
						} else {
							//--- Caso general
							$datos = $this->ejecutar_metodo_carga_ef($id_ef, $param);
						}
					}
					$this->_efs[$id_ef]->set_opciones($datos, $cargar, $tiene_maestros);
				}
			}
		}
	}
	
	/**
	 * @ignore 
	 */
	protected function ef_requiere_carga($id_ef)
	{
		return 
			isset($this->_parametros_carga_efs[$id_ef]['carga_metodo'])
			|| isset($this->_parametros_carga_efs[$id_ef]['carga_lista'])
			|| isset($this->_parametros_carga_efs[$id_ef]['carga_sql'])
			|| isset($this->_parametros_carga_efs[$id_ef]['popup_carga_desc_metodo']);
	}
	
	/**
	 * @ignore 
	 */
	function ejecutar_metodo_carga_ef($id_ef, $maestros = array())
	{
		$parametros = $this->_parametros_carga_efs[$id_ef];
		$seleccionable = $this->_efs[$id_ef]->es_seleccionable();
		
		$es_posicional = true;
		if ($seleccionable) {
			//--- Se determinan cuales son los campos claves y el campo de valor
			$campos_clave = $this->_efs[$id_ef]->get_campos_clave();
			$campo_valor = $this->_efs[$id_ef]->get_campo_valor();
			$es_posicional = $this->_efs[$id_ef]->son_campos_posicionales();
	
		}
		if (isset($parametros['carga_lista'])) {
			//--- Carga a partir de una lista de valores
			$salida = $this->ef_metodo_carga_lista($id_ef, $parametros, $maestros);
		} elseif (isset($parametros['carga_sql'])) {
			//--- Carga a partir de un SQL
			$nuevos = $this->ef_metodo_carga_sql($id_ef, $parametros, $maestros, $es_posicional);
			if ($seleccionable) {
				$salida = rs_convertir_asociativo($nuevos, $campos_clave, $campo_valor);
			} else {
				if (! empty($nuevos)) {
					return $nuevos[0][0];					
				}
			}
		} elseif (isset($parametros['carga_metodo'])) {
			if (isset($parametros['carga_dt'])) {
				//--- Carga a partir de un Método datos_tabla
				$nuevos = $this->ef_metodo_carga_dt($id_ef, $parametros, $maestros);
			} elseif (isset($parametros['carga_consulta_php'])) {
				//--- Carga a partir de una Consulta PHP				
				$nuevos = $this->ef_metodo_carga_consulta_php($id_ef, $parametros, $maestros);
			} else {
				//--- Carga a partir de un PHP
				$nuevos = $this->ef_metodo_carga_php($id_ef, $parametros, $maestros);
			}
			if ($seleccionable) {
				$salida = rs_convertir_asociativo($nuevos, $campos_clave, $campo_valor);
			} else {
				return $nuevos;	
			}
		} else {
			throw new toba_error_def('No está definido un método de carga. Parámetros: '.var_export($parametros, true));
		}
		if (! $this->_efs[$id_ef]->permite_seleccion_multiple()) {
			$salida = $this->ajustar_descripciones($id_ef, $salida);
		}
		if (! isset($parametros['carga_no_seteado_ocultar'])) {
			$parametros['carga_no_seteado_ocultar'] = false;
		}
		//--- Agrega el no-seteado en caso que existan elementos
		if ($parametros['carga_permite_no_seteado'] == '1' && !isset($salida[apex_ef_no_seteado])
			&& (! empty($salida) || !$parametros['carga_no_seteado_ocultar'])) {
			$lista = array();
			$lista[apex_ef_no_seteado] = (isset($parametros['carga_no_seteado']))? $parametros['carga_no_seteado']: '';
			return $lista + $salida;
		} else {
			return $salida;	
		}
	}

	/**
	 * Si se seteó una longitud máxima para la descripción de un EF seleccionable,
	 * se cortan los valores a ese máximo y se agrega al final '...'.
	 * @ignore 
	 */
	function ajustar_descripciones($id_ef, $datos)
	{
		$maximo_descripcion = $this->_efs[$id_ef]->get_maximo_descripcion();
		if (isset($maximo_descripcion) && is_array($datos)) {
			foreach ($datos as $clave => $opcion) {
				if ($opcion && strlen($opcion) > $maximo_descripcion) {
					$datos[$clave] = substr($opcion, 0, $maximo_descripcion) . '...';
				}
			}
		}
		return $datos;
	}

	/**
	 * @ignore 
	 */
	function ejecutar_metodo_carga_descripcion_ef($id_ef, $maestros = array())
	{
		$parametros = $this->_parametros_carga_efs[$id_ef];
		$parametros['carga_metodo'] = $parametros['popup_carga_desc_metodo'];		
		$parametros['carga_clase'] = $parametros['popup_carga_desc_clase'];
		$parametros['carga_include'] = $parametros['popup_carga_desc_include'];
		return $this->ef_metodo_carga_php($id_ef, $parametros, array($maestros));
	}
	

	/**
	 * @ignore 
	 */
	protected function ef_metodo_carga_lista($id_ef, $parametros, $maestros)
	{
		$elementos = explode(",", $parametros['carga_lista']);
		$valores = array();
		foreach ($elementos as $elemento) {
			$campos = explode("/", $elemento);
			if (count($campos) == 1) {
				$valores[trim($campos[0])] = trim($campos[0]);
			} elseif (count($campos) == 2) {
				$valores[trim($campos[0])] = trim($campos[1]);
			} else {
				throw new toba_error_def("La lista de opciones del ef '$id_ef' es incorrecta.");
			}
		}		
		return $valores;
	}
	
	/**
	 * @ignore 
	 */
	protected function ef_metodo_carga_sql($id_ef, $parametros, $maestros, $es_posicional)
	{
		//Armo la sentencia que limita al proyecto
		$sql_where = "";
		if (isset($parametros['columna_proyecto'])) {
			$sql_where .= $parametros["columna_proyecto"] . " = '".toba::proyecto()->get_id()."' ";
			if (isset($parametros["incluir_toba"]) && $parametros["incluir_toba"]) {
				$sql_where .= " OR ".$parametros["columna_proyecto"]." = 'toba'";
			}
		}
		if ($sql_where != '') {
			$where[] = "(" . $sql_where .")";
			$parametros['carga_sql'] =  sql_agregar_clausulas_where($parametros['carga_sql'],$where);
		}
		foreach ($maestros as $id_maestro => $valor_maestro) {
			if (is_array($valor_maestro)) {
				$valor_maestro = '{'.implode(',' , $valor_maestro) . '}';		//Si es un arreglo lo tomo como array
			}
			$parametros['carga_sql'] = str_replace(apex_ef_cascada.$id_maestro.apex_ef_cascada, $valor_maestro, $parametros['carga_sql']);
		}
		$modo = ($es_posicional) ? toba_db_fetch_num : toba_db_fetch_asoc;
		return toba::db($parametros['carga_fuente'])->consultar($parametros['carga_sql'], $modo);
	}
	
	/**
	 * @ignore 
	 */
	protected function ef_metodo_carga_php($id_ef, $parametros, $maestros)
	{
		// Fix. En la descripci?n de un popup $maestros llega con el valor directamente,
		// no como un arreglo con el valor como lo espera call_user_func
		$maestros = (! is_array($maestros)) ? array($maestros) : $maestros;
        
		if (isset($parametros['carga_include']) || isset($parametros['carga_clase'])) {
			if(!class_exists($parametros['carga_clase']) && isset($parametros['carga_include']) && $parametros['carga_include'] != '') {
				$pm = toba::puntos_montaje()->get_por_id($parametros['punto_montaje']);
				$path = $pm->get_path_absoluto().'/'.$parametros['carga_include'];
				require_once($path);
			}
			$instanciable = (isset($parametros['instanciable']) && $parametros['instanciable']=='1');
			if ($instanciable) {
				$obj = new $parametros['carga_clase']();
				$clase = $obj;
			} else {
				$clase = $parametros['carga_clase'];
			}
			if (! method_exists($clase, $parametros['carga_metodo'])) {
				throw new toba_error_def("ERROR en la carga del ef $id_ef. No existe el método '{$parametros['carga_metodo']}' de la clase '{$parametros['carga_clase']}'");			
			}			
			$metodo = array($clase, $parametros['carga_metodo']);	
			return call_user_func_array($metodo, $maestros);
		} else {
			//--- Es un metodo del CI contenedor
			if (! method_exists($this->_controlador->controlador(), $parametros['carga_metodo'])) {
				$clase = get_class($this->_controlador->controlador());
				throw new toba_error_def("ERROR en la carga del ef $id_ef. No existe el método '{$parametros['carga_metodo']}' en la clase '$clase'");			
			}
			$dato = call_user_func_array(array($this->_controlador->controlador(), $parametros['carga_metodo']), $maestros);
			return $dato;
		}
	}
	
	/**
	 * @ignore 
	 */
	protected function ef_metodo_carga_consulta_php($id_ef, $parametros, $maestros)
	{
		if (isset($parametros['carga_consulta_php_clase'])) {
			$objeto = toba::consulta_php($parametros['carga_consulta_php_clase']);
			$metodo = $parametros['carga_metodo'];
			if (! method_exists($objeto, $metodo)) {
				throw new toba_error_def("ERROR en la carga del ef $id_ef. No existe el método '{$parametros['carga_metodo']}' de la consulta php '{$parametros['carga_consulta_php_clase']}'");
			}
			return call_user_func_array(array($objeto, $metodo), $maestros);
		} 
	}
		
	
	/**
	 * @ignore 
	 */
	protected function ef_metodo_carga_dt($id_ef, $parametros, $maestros)
	{
		$id = $this->_controlador->get_id();
		$dt = toba_constructor::get_runtime(array('proyecto' => $id[0],'componente' => $parametros['carga_dt']), 'toba_datos_tabla');
		if (! method_exists($dt, $parametros['carga_metodo'])) {
			$clase = get_class($dt);
			throw new toba_error_def("ERROR en la carga del ef $id_ef. No existe el método '{$parametros['carga_metodo']}' de la clase '$clase'");			
		}				
		return call_user_func_array(array($dt, $parametros['carga_metodo']), $maestros);
	}	

	/*
	 * Quita un ef de la relacion de cascadas 
	 * @ignore
	 */
	function quitar_ef($ef)
	{
		$esclavos = (isset($this->_cascadas_esclavos[$ef])) ? $this->_cascadas_esclavos[$ef]: array();
		foreach($esclavos as $ef_esclavo){
			if (isset($this->_efs[$ef_esclavo])){
				$this->_efs[$ef_esclavo]->quitar_maestro($ef);
			}			
		}
		unset($this->_efs[$ef]);
	}
}

?>