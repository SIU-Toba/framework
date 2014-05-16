<?php
class catalogo_tablas
{
	protected $_proyecto;
	protected $_fuente;
	protected $_datos_tabla = array();
	protected $_tablas = array();
	protected $_dt_bloqueados = array();
	protected $_tablas_actualizables = array();
	protected $_tablas_nuevas = array();
	protected $_tabla_x_schema = array();
	protected $_schemas_disp;

	function __construct($proyecto=null, $fuente=null)
	{
		$this->_proyecto = $proyecto;
		$this->_fuente = $fuente;
	}

	function cargar()
	{
		//Aca tengo que obtener los schemas de los datos de la fuente.
		$this->_schemas_disp = $this->buscar_schemas_fuente();
		$this->buscar_lista_tablas();								//Obtengo todas las tablas
		$this->buscar_dt_lockeados();							//Obtengo aquellos no actualizables
		$this->_tablas_nuevas = $this->_tablas;
		//Ahora tengo que sacar los actualizados
		foreach ($this->_tablas as $clave => $tabla) {
			$columnas_dt = $this->get_columnas_dt($tabla['tabla']);
			if (! empty($columnas_dt)) {																			//Si existe un DT
				$columnas_base = $this->get_columnas_base($tabla['tabla']);
				$modificaciones = $this->cambios_a_realizar($columnas_dt, $columnas_base);
				unset($this->_tablas_nuevas[$clave]);													//La quito de entre las tablas nuevas
				if (! empty($modificaciones)) {
					$this->_tablas_actualizables[$tabla['tabla']] = $modificaciones;
				}
			}
		}
	}

	function get_tablas_nuevas()
	{
		return $this->_tablas_nuevas;
	}

	function get_tablas_actualizables()
	{
		return $this->_tablas_actualizables;
	}

	function get_tablas_no_activas()
	{
		return $this->_dt_bloqueados;
	}

	function get_tablas_preseleccionadas()
	{
		return $this->quitar_desactivados();
	}
	
	function resetear()
	{
		toba_admin_fuentes::instancia()->get_fuente($this->_fuente)->resetear_mapeo_tablas();			//Fuerzo la relectura de los metadatos de datos_tabla
		$this->_tablas = array();
		$this->_tablas_nuevas = array();
		$this->_tablas_actualizables = array();
		$this->_dt_bloqueados = array();
		$this->_tabla_x_schema = array();
	}

	///-----------------------------------------------------------------------------------------------------------------
	function buscar_dt_lockeados()
	{
		$this->_dt_bloqueados = $this->get_dt_bloqueados();
	}

	function quitar_desactivados()
	{
		//Aca hago el diff entre las tablas
		return array_diff($this->_tablas, $this->_dt_bloqueados);
	}

	//-------------------------------------------------------------------------------------------------------------------
	function buscar_schemas_fuente()
	{
		$datos = array();
		$disponibles = toba_proyecto_db::get_info_fuente_schemas($this->_proyecto, $this->_fuente);
		if (empty($disponibles)) {			//Obtengo el schema directamente desde la conexion
			$datos[] = toba::db($this->_fuente, $this->_proyecto)->get_schema();			
		} else {
			foreach ($disponibles as $esquema) {
				$datos[] = $esquema['nombre'];
			}
		}
		return $datos;
	}
	
	function buscar_lista_tablas()
	{
		$this->_tablas = array();		
		$lista_inicial = toba::db($this->_fuente, $this->_proyecto)->get_lista_tablas_bd(false, $this->_schemas_disp);
		foreach ($lista_inicial as $tabla) {
			$this->_tablas[] = array('tabla' => $tabla['nombre']);
			$this->_tabla_x_schema[$tabla['nombre']] = $tabla['esquema'];
		}		
	}
	
	function get_dt_bloqueados()
	{
		if (! is_null($this->_proyecto)) {
			$proyecto = $this->_proyecto;
		} else {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		
		$sql = '
			SELECT
				dt.tabla
			FROM
				apex_objeto_db_registros as dt,
				apex_objeto as comp
			WHERE
					dt.objeto_proyecto = '. toba_contexto_info::get_db()->quote($proyecto) .
				'AND dt.objeto = comp.objeto
				AND dt.objeto_proyecto = comp.proyecto
				AND dt.permite_actualizacion_automatica = 0
			ORDER BY tabla;';
		toba::logger()->debug('DT bloqueados: '. $sql);
		return toba_contexto_info::get_db()->consultar($sql);
	}

	function get_columnas_base($tabla)
	{
		$resultado = array();
		try {
			$resultado = toba::db($this->_fuente, $this->_proyecto)->get_definicion_columnas($tabla);
		}catch (toba_error_db $e) {
			toba::logger()->error("Búsqueda Propiedades tabla ' $tabla': " . $e->getMessage());
		}
		return $resultado;
	}
	
	function get_columnas_dt($tabla)
	{
		$resultado = array();
		try {
			//Obtengo el id del datos_tabla para la tabla X
			$componente = toba_admin_fuentes::instancia()->get_fuente($this->_fuente)->get_id_datos_tabla($tabla);
			$db = toba_contexto_info::get_db();								//Obtengo la base del contexto para usar la instancia de toba
			toba_datos_tabla_def::set_db($db);
			
			//Busco la definicion de columnas del datos_tabla en cuestion
			$proyecto = toba_contexto_info::get_proyecto();
			$dt_info = toba_datos_tabla_def::get_vista_extendida($proyecto, $componente);
			$rs = $db->consultar($dt_info['_info_columnas']['sql']);		
			//Convierto el arreglo devuelto a un formato indexado por nombre de columna
			for ($a = 0; $a < count($rs); $a++) { 
				$resultado[$rs[$a]['columna']] =& $rs[$a];
			}	
		} catch(toba_error $e) {
			toba::logger()->error(" Búsqueda DT '$tabla': " .$e->getMessage());
		}
		return $resultado;
	}

	//----------------------------------------------------------------------------------------------------------------------
	function cambios_a_realizar($cols_dt, $cols_base)
	{		
		$cambios = array();
		//Genero 2 arreglos base para realizar la comparacion normalizando la estructura
		$aux_base = array();
		foreach ($cols_base as $col) {
			$temp = array('columna' => $col['nombre'], 'tipo' => $col['tipo']);
			if ($col['tipo'] == 'C') {$temp['largo'] = $col['longitud'];}
			if (isset($col['not_null'])) {$temp['no_nulo_db'] = (int) $col['not_null'];}
			if (isset($col['pk'])) {$temp['pk'] = (int) $col['pk'];}
			if (isset($col['secuencia']) && trim($col['secuencia']) != '') { $temp['secuencia'] = $col['secuencia'];}
			$aux_base[$col['nombre']] = $temp;
		}

		$aux_dt = array();
		foreach ($cols_dt as $clave => $valor) {
			if ($valor['externa'] != '1') {
				$aux_dt[$clave] = array('columna' => $valor['columna'], 'tipo' => $valor['tipo']);
				if ($valor['tipo'] == 'C') {$aux_dt[$clave]['largo'] = $valor['largo'];}
				$aux_dt[$clave]['no_nulo_db'] = (int) $valor['no_nulo_db'];
				$aux_dt[$clave]['pk'] = (int) $valor['pk'];
				if (isset($valor['secuencia']) && trim($valor['secuencia']) != '') { $aux_dt[$clave]['secuencia'] = $valor['secuencia'];}
			}
		}

		//Veo si hay diferencias entre la cantidad de columnas
		$cols_nuevas = array_diff_key($aux_base, $aux_dt);		// ! empty  ===> Base mas columnas
		if (! empty($cols_nuevas)) {$cambios['A'] = $cols_nuevas;}

		$cols_faltantes = array_diff_key($aux_dt, $aux_base);		// ! empty ===> Base menos columnas
		if (! empty($cols_faltantes)) {$cambios['B'] = $cols_faltantes;}

		//Veo si hay diferencias entre las propiedades de las columnas
		foreach ($aux_base as $klave => $col) {
			if (isset($aux_dt[$klave])) {										//Si existe la columna en el DT
				$resultado_base = array_diff_assoc($col, $aux_dt[$klave]);			//Calculo la diferencia entre los arreglos de propiedades
				if (! empty($resultado_base)) {									//Si hay resultado distinto de vacio ==> hay diferencia entre las columnas
					$cambios['M'][$klave] = $resultado_base;
				}
			}
		}
		return $cambios;
	}

	//----------------------------------------------------------------------------------------------------------------------
	function desactivar_no_procesadas($procesadas=array())
	{
		$total_tablas = array_keys($this->_tablas_actualizables);
		foreach ($this->_tablas_nuevas as $tabla) {
			$total_tablas[] = $tabla['tabla'];
		}
		$resultado = array_diff($total_tablas, $procesadas);
		foreach ($resultado as $tb) {
			$id = $this->get_id_objeto($tb);
			if (! is_null($id)) {
				$sql = 'UPDATE apex_objeto_db_registros SET permite_actualizacion_automatica = 0
							WHERE	objeto_proyecto  = ' . quote($this->_proyecto) . ' AND objeto = ' . quote($id);
				toba::db()->ejecutar($sql);
			}
		}
	}

	//----------------------------------------------------------------------------------------------------------------------
	function confirmar_acciones($tabla_nombre)
	{
		//Creo el datos relacion para editar la tabla.
		$id = toba_info_editores::get_dr_de_clase('toba_datos_tabla');
		$componente = array('proyecto' => $id[0], 'componente' => $id[1]);
		$dr_sincro = toba_constructor::get_runtime($componente);
		$dr_sincro->inicializar();
		
		//Veo donde esta la tabla
		if (isset($this->_tablas_actualizables[$tabla_nombre])) {		//Tengo que actualizar el DT
			 $id_dt = $this->get_id_objeto($tabla_nombre);
			 $dr_sincro->cargar(array('proyecto' => $this->_proyecto, 'objeto' => $id_dt));
			 $dr_sincro->actualizar_campos();
			 $dr_sincro->tabla('prop_basicas')->set_fila_columna_valor(0, 'esquema', $this->_tabla_x_schema[$tabla_nombre]);
			 //Aca aun falta quitar las columnas
			if (isset($this->_tablas_actualizables[$tabla_nombre]['B'])) {
				foreach (array_keys($this->_tablas_actualizables[$tabla_nombre]['B']) as $borrable) {
					$id_interno = $dr_sincro->tabla('columnas')->get_id_fila_condicion(array('columna' => $borrable));
					$dr_sincro->tabla('columnas')->eliminar_fila(current($id_interno));
				}
			}
		} else {		//Es un DT nuevo			
			
			$pms_proyecto = toba_info_editores::get_pms(toba_editor::get_proyecto_cargado());		
			$pm_default = current($pms_proyecto);
			$datos = array('nombre' => $tabla_nombre, 'proyecto' => $this->_proyecto, 
										'clase_proyecto' => 'toba', 'clase' => 'toba_datos_tabla',
										'fuente_datos_proyecto' => $this->_proyecto, 'fuente_datos' => $this->_fuente,
										'punto_montaje' => $pm_default['id']);
			
			$dr_sincro->tabla('base')->set($datos);
			$dr_sincro->tabla('prop_basicas')->set(array('ap'=>1, 'permite_actualizacion_automatica' => '1', 'punto_montaje' => $pm_default['id']));	
			$dr_sincro->tabla('prop_basicas')->set_fila_columna_valor(0, 'tabla', $tabla_nombre);
			$dr_sincro->tabla('prop_basicas')->set_fila_columna_valor(0, 'esquema', $this->_tabla_x_schema[$tabla_nombre]);

			$columnas = $this->get_columnas_base($tabla_nombre);
			foreach ($columnas as $col) {
				$datos_col = array();
				$datos_col['columna'] = $col['nombre'];
				$datos_col['no_nulo_db'] = (int) $col['not_null'];
				$datos_col['pk'] = (int) $col['pk'];
				$datos_col['tipo'] = $col['tipo'];
				if ($col['secuencia'] != '') {
					$datos_col['secuencia'] = $col['secuencia'];
				}
				if ($col['tipo'] == 'C' && $col['longitud'] > 0) {
					$datos_col['largo'] = $col['longitud'];
				}
				$dr_sincro->tabla('columnas')->nueva_fila($datos_col);
			}
		}
		
		$dr_sincro->sincronizar();
		unset($dr_sincro);
	}

	function get_id_objeto($tabla)
	{
		try {
			 $id = toba_admin_fuentes::instancia()->get_fuente($this->_fuente, $this->_proyecto)->get_id_datos_tabla($tabla);
		} catch (toba_error $e) {
			$id  = null;
		}
		return $id;
	}


}
?>

