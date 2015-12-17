<?php
require_once('objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{
	//Columnas
	protected $s__seleccion_columna;
	protected $s__seleccion_columna_anterior;
	private $id_intermedio_columna;
	protected $s__cortes_control;
	protected $s__importacion_cols;
	protected $disparar_importacion_columnas;
	protected $clase_actual = 'toba_ei_cuadro';
	
	function ini()
	{
		parent::ini();
		$col = toba::memoria()->get_parametro('columna');
		//¿Se selecciono un ef desde afuera?
		if (isset($col)) {
			$this->set_pantalla(2);
			$id_interno = $this->get_entidad()->tabla('columnas')->get_id_fila_condicion(array('clave'=>$col));
			if (count($id_interno) == 1) {
				$this->evt__columnas_lista__seleccion($id_interno[0]);			
			} else {
				throw new toba_error("No se encontro la columna $col.");
			}
		}
	}
	
	function evt__procesar()
	{
		$datos = $this->get_entidad()->tabla('prop_basicas')->get();
		$tiene_clave = ($datos['clave_dbr'] == 1 || $datos['columnas_clave'] != '');
		if (!$tiene_clave && $this->get_dbr_eventos()->hay_evento_de_fila()) {
			toba::notificacion()->agregar('El cuadro no tiene definido cuales de sus columnas
				forman la <strong>clave de los registros</strong>. Esto hace que los eventos asociados a las filas no
				puedan propagar el valor que las representa.<br><br>
				Estas columnas claves se pueden indicar en la solapa de Prop.Básicas.', 'info');
		}
		$this->get_entidad()->get_persistidor()->retrasar_constraints();
		parent::evt__procesar();		
	}	
	
	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas()
	{
		$datos = $this->get_entidad()->tabla('prop_basicas')->get();
		$datos['posicion_botonera'] = $this->get_entidad()->tabla('base')->get_columna('posicion_botonera');
		return $datos;
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla('base')->set_columna_valor('posicion_botonera', $datos['posicion_botonera']);
		unset($datos['posicion_botonera']);
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	//*******************************************************************
	//*******************  COLUMNAS  *************************************
	//*******************************************************************

	function mostrar_columna_detalle()
	{
		if (isset($this->s__seleccion_columna)) {
			return true;	
		}
		return false;
	}
	
	function conf__2($pantalla)
	{
		//--- Armo la lista de DEPENDENCIAS disponibles
		$this->s__cortes_control = array();
		if ($registros = $this->get_entidad()->tabla('cortes')->get_filas()) {
			foreach ($registros as $reg) {
				$this->s__cortes_control[$reg['identificador']] = $reg['identificador'];
			}
		}
		//--- Configuro la pantalla
		if ($this->mostrar_columna_detalle()) {
			$pantalla->eliminar_dep('columnas_importar');
			$existen_cortes = count($this->s__cortes_control) > 0;
			if (! $existen_cortes) {
				$pantalla->eliminar_dep('columna_corte');
			}
			$this->dependencia('columnas_lista')->set_fila_protegida($this->s__seleccion_columna);
			$this->dependencia('columnas_lista')->seleccionar($this->s__seleccion_columna);
		} else {
			$pantalla->eliminar_dep('columnas');
			$pantalla->eliminar_dep('columna_corte');
			$this->dependencia('columnas_importar')->colapsar();
		}
	}
	
	function evt__2__salida()
	{
		unset($this->s__seleccion_columna);
		unset($this->s__seleccion_columna_anterior);
	}

	//-------------------------------
	//---- EI: Lista de columnas ----
	//-------------------------------
	
	function evt__columnas_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una columna de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las columnas NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		$dbr = $this->get_entidad()->tabla('columnas');
		$orden = 1;
		foreach (array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			$registros[$id]['orden'] = $orden;
			$orden++;
			$accion = $registros[$id][apex_ei_analisis_fila];
			if (isset($registros[$id]['formateo'])) {
				$estilo = toba_info_editores::get_estilo_defecto_formateo_columna($registros[$id]['formateo']);
			} else {
				$estilo = 4;
			}
			$fila = $dbr->get_fila($id);
			if (! isset($fila) || !isset($fila['estilo'])) {
				$registros[$id]['estilo'] = $estilo;
			}
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case 'A':
					$this->id_intermedio_columna[$id] = $dbr->nueva_fila($registros[$id]);
					break;	
				case 'B':
					$dbr->eliminar_fila($id);
					break;	
				case 'M':
					$dbr->modificar_fila($id, $registros[$id]);
					break;	
			}
		}
	}
	
	function conf__columnas_lista()
	{
		if ($datos_dbr = $this->get_entidad()->tabla('columnas')->get_filas()) {
			//Ordeno los registros segun la 'posicion'
			//ei_arbol($datos_dbr,"Datos para el ML: PRE proceso");
			for ($a = 0;$a < count($datos_dbr); $a++) {
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC, $datos_dbr);
			//EL formulario_ml necesita necesita que el ID sea la clave del array
			//No se solicita asi del DBR porque array_multisort no conserva claves numericas
			// y las claves internas del DBR lo son
			for ($a = 0; $a < count($datos_dbr); $a++) {
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset( $datos_dbr[$a][apex_db_registros_clave]);
				$datos[$id_dbr] = $datos_dbr[$a];
			}
			//ei_arbol($datos,"Datos para el ML: POST proceso");
			return $datos;
		}
	}

	function evt__columnas_lista__seleccion($id)
	{
		if (isset($this->id_intermedio_columna[$id])) {
			$id = $this->id_intermedio_columna[$id];
		}
		$this->s__seleccion_columna = $id;
	}

	//-----------------------------------------
	//---- EI: Info detalla de una COLUMNA ----
	//-----------------------------------------

	function evt__columnas__modificacion($datos)
	{
		if (isset($datos['estilo_precarga'])) {
			if (! isset($datos['estilo'])) {
				$datos['estilo'] = toba_info_editores::get_nombre_clase_css($datos['estilo_precarga']);
			}
			unset($datos['estilo_precarga']);
		}
		
		if (! isset($datos['evento_asociado'])) {				//Si no hay evento asociado a la columna, hay que blanquear las columnas del vinculo para evitar problemas con viejos datos.
			$datos['vinculo_carpeta'] = null;
			$datos['vinculo_item'] = null;
		}				
		$this->get_entidad()->tabla('columnas')->modificar_fila($this->s__seleccion_columna_anterior, $datos);
	}
	
	function evt__columnas__aceptar($datos)
	{
		if (isset($datos['estilo_precarga'])) {
			if (! isset($datos['estilo'])) {
				$datos['estilo'] = toba_info_editores::get_nombre_clase_css($datos['estilo_precarga']);
			}
			unset($datos['estilo_precarga']);
		}
		
		if (! isset($datos['evento_asociado'])) {				//Si no hay evento asociado a la columna, hay que blanquear las columnas del vinculo para evitar problemas con viejos datos.
			$datos['vinculo_carpeta'] = null;
			$datos['vinculo_item'] = null;
		}
		$this->get_entidad()->tabla('columnas')->modificar_fila($this->s__seleccion_columna_anterior, $datos);
		$this->evt__columnas__cancelar();
	}

	function conf__columnas()
	{
		$this->s__seleccion_columna_anterior = $this->s__seleccion_columna;
		$datos = $this->get_entidad()->tabla('columnas')->get_fila($this->s__seleccion_columna_anterior);

		if (isset($datos['estilo'])) {
			$datos['estilo_precarga'] = apex_ef_no_seteado;
			$en_base = toba_info_editores::get_lista_estilos_columnas();		//Busco la inversa del texto para setear el combo si existe
			foreach($en_base as $estilo) {
				if ($estilo['css'] == $datos['estilo']) {
					$datos['estilo_precarga'] = $estilo['columna_estilo'];
				}
			}			
		}

		//Aqui comienza el engendro malefico
		$posibles = $this->get_eventos_vinculo_cargados();
		if (is_array($posibles)) {
			foreach ($posibles as $evento) {	//Si encuentro match con el evento
				if (isset($evento['evento_id']) && isset($datos['evento_asociado']) &&($datos['evento_asociado'] == $evento['evento_id'])) {
					$datos['evento_asociado'] = $evento['identificador']; //Uso el nombre del evento
				}
			}
		}
				
		return $datos;
	}

	function evt__columnas__cancelar()
	{
		unset($this->s__seleccion_columna);
	}

	//-----------------------------------------
	//---- EI: Participacion en los CORTES de CONTROL del cuadro
	//-----------------------------------------

	function conf__columna_corte($form)
	{
		$indice = 0;
		$ids_visitados = array();
		$filas = array();
		$busqueda = $this->get_entidad()->tabla('columna_total_cc')->nueva_busqueda();
		$busqueda->set_padre('columnas', $this->s__seleccion_columna_anterior);
		//Inicializo con los cortes de control existentes para cuando no existe carga previa o cuando se agregan cortes.
		foreach ($this->s__cortes_control as $corte) {
			$filas[$indice] = array('identificador' => $corte, 'total' => 0);
			$ids_visitados[$corte] = $indice;
			$indice++;
		}
		//Obtengo las asociaciones entre columnas y cortes de control
		$resultado_busqueda = $busqueda->buscar_ids();
		foreach ($resultado_busqueda  as $id_fila) {
			$col_asoc = $this->get_entidad()->tabla('columna_total_cc')->get_fila($id_fila);
			if (isset($ids_visitados[$col_asoc['identificador']])) {
				$id_tmp = $ids_visitados[$col_asoc['identificador']];
			} else {
				$id_tmp = $indice;
				$ids_visitados[$col_asoc['identificador']] = $indice;
				$indice++;
			}
			$filas[$id_tmp] = array('identificador' => $col_asoc['identificador'], 'total' => $col_asoc['total']);
		}
		$form->set_datos($filas);
	}

	function evt__columna_corte__modificacion($datos)
	{
		//Borro los datos anteriores de la tabla para solo colocar los nuevos
		if (! empty($datos)) {
			$busqueda = $this->get_entidad()->tabla('columna_total_cc')->nueva_busqueda();
			$busqueda->set_padre('columnas', $this->s__seleccion_columna_anterior);
			foreach ($busqueda->buscar_ids() as $id_fila) {
				$this->get_entidad()->tabla('columna_total_cc')->eliminar_fila($id_fila);
			}
		}

		//Setear cursor en tabla columnas con $this->s__seleccion_columna_anterior
		$this->get_entidad()->tabla('columnas')->set_cursor($this->s__seleccion_columna_anterior);
		// Buscar id para setear cursor en tabla cortes mediante el identificador
		foreach ($datos as $valores) {
			$id = $this->get_entidad()->tabla('cortes')->get_id_fila_condicion(array('identificador' => $valores['identificador']));
			if ($valores['total'] == 1) {
				$this->get_entidad()->tabla('cortes')->set_cursor(current($id));
				$this->get_entidad()->tabla('columna_total_cc')->nueva_fila($valores);				//Guardo los valores en la tabla
			}
		}

		//Resetear el cursor para que no se quede pensando
		$this->get_entidad()->tabla('cortes')->resetear_cursor();
	}

	//---------------------------------
	//---- EI: IMPORTAR definicion ----
	//---------------------------------

	function evt__columnas_importar__importar($datos)
	{
		$this->s__importacion_cols = $datos;
		$this->disparar_importacion_columnas = true;
	}

	function post_eventos()
	{
		if ($this->disparar_importacion_columnas) {
			if (isset($this->s__importacion_cols['datos_tabla'])) {
				$clave = array('proyecto' => toba_editor::get_proyecto_cargado(),
								'componente' => $this->s__importacion_cols['datos_tabla']);
				$dt = toba_constructor::get_info($clave, 'toba_datos_tabla');
				$this->s__importacion_cols = $dt->exportar_datos_columnas($this->s__importacion_cols['pk']);
				//ei_arbol($this->s__importacion_cols);
				$cols = $this->get_entidad()->tabla('columnas');
				foreach ($this->s__importacion_cols as $col) {
					try{
						$cols->nueva_fila($col);
					}catch(toba_error $e){
						toba::notificacion()->agregar("Error agregando la columna '{$col['clave']}'. " . $e->getMessage());
					}
				}
			}			
			$this->disparar_importacion_columnas = false;
		}		
	}

	function conf__columnas_importar()
	{
		if (isset($this->s__importacion_cols)) {
			return $this->s__importacion_cols;
		}
	}

	/**
	 * Recibe la notificacion sobre un evento eliminado
	 * @param array $evt Fila que representa el evento
	 * @private
	 */
	function notificar_eliminacion_evento($evt)
	{
		//Chequeo contra identificador y contra id_real
		$id_real = (isset($evt['evento_id'])) ? $evt['evento_id'] : null;
		$identificador = $evt['identificador'];

		$columnas = $this->get_entidad()->tabla('columnas')->get_filas(null, false);
		if (is_array($columnas)) {
			foreach ($columnas as $col) {
				if (isset($col['evento_asociado']) && ($col['evento_asociado'] == $identificador || $col['evento_asociado'] == $id_real)) {
					throw new toba_error_def("No se puede eliminar el evento '$identificador' aún esta asociado a la columna '{$col['clave']}'");
				}
			}
		}
	}

	//*******************************************************************
	//*******************  EVENTOS  ************************************
	//*******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/

	function get_eventos_estandar($modelo)
	{
		return toba_ei_cuadro_info::get_lista_eventos_estandar($modelo);
	}

	function evt__3__salida()
	{
		$this->dependencia('eventos')->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}

	function get_eventos_vinculo_cargados()
	{
		$condicion = null;
		$datos = $this->get_dbr_eventos()->get_filas($condicion, false, false);
		return $datos;
	}

	//*******************************************************************
	//*****************  CORTES de CONTROL  *****************************
	//*******************************************************************
	
	function conf__prop_cortes()
	{
		return $this->get_entidad()->tabla('prop_basicas')->get();
	}

	function evt__prop_cortes__modificacion($datos)
	{
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	function evt__cortes__modificacion($datos)
	{
		$this->get_entidad()->tabla('cortes')->procesar_filas($datos);
	}
	
	function conf__cortes()
	{
		if($datos_dbr = $this->get_entidad()->tabla('cortes')->get_filas()) {
			for ($a = 0; $a < count($datos_dbr); $a++) {
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC, $datos_dbr);
			for ($a = 0; $a < count($datos_dbr); $a++) {
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset($datos_dbr[$a][apex_db_registros_clave]);
				$datos[$id_dbr] = $datos_dbr[$a];
			}
			return $datos;
		}
	}

}
?>
