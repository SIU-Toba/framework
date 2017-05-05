<?php
/*
	ATENCION: 
		El controlador tiene que implementar "get_dbr_efs()" 
		para que este CI puede obtener el DBR que utiliza para trabajar
*/
class ci_efs extends toba_ci
{
	protected $tabla;
	protected $s__seleccion_efs;
	protected $s__seleccion_efs_anterior;
	protected $s__importacion_efs;
	protected $disparar_importacion_efs = false;
	private $id_intermedio_efs;
	protected $mecanismos_carga = array('carga_metodo', 'carga_sql', 'carga_lista');
	protected $modificado = false;
	protected $campo_clave = 'identificador';
	
	function get_tabla()
	{
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_dbr_efs();
		}
		return $this->tabla;
	}

	function mostrar_efs_detalle()
	{
		if (isset($this->s__seleccion_efs)) {
			return true;	
		}
		return false;
	}

	/**
	*	El contenedor selecciona un ef por su identificador real
	*/	
	function seleccionar_ef($id)
	{
		$id_interno = $this->get_tabla()->get_id_fila_condicion(array($this->campo_clave=>$id));
		if (count($id_interno) == 1) {
			$this->evt__efs_lista__seleccion(current($id_interno));
		} else {
			throw new toba_error("No se encontro el ef $id.");
		}
	}
	
	function conf()
	{
		$this->pantalla()->agregar_dep('efs_lista');		
		if ($this->mostrar_efs_detalle()) {
			$this->dependencia('efs_lista')->set_fila_protegida($this->s__seleccion_efs);
			$this->dependencia('efs_lista')->seleccionar($this->s__seleccion_efs);
			$this->pantalla()->agregar_dep('efs');
			$param_carga = $this->get_definicion_parametros(true);			
			$param_varios = $this->get_definicion_parametros(false);
			if (! empty($param_varios)) {			
				$this->pantalla()->agregar_dep('param_varios');
			}
			if (! empty($param_carga)) {
				$this->pantalla()->agregar_dep('param_carga');
			}			
			//Protejo la efs seleccionada de la eliminacion
		} else {
			$this->pantalla()->eliminar_evento('cancelar');
			$this->pantalla()->eliminar_evento('aceptar');
			if ($this->existe_dependencia('efs_importar')) {
				$this->pantalla()->agregar_dep('efs_importar');
				$this->dependencia('efs_importar')->colapsar();
			}
		}
		if (! $this->hay_cascadas()) {
			$this->dep('efs_lista')->eliminar_evento('mostrar_esquema');
		}			
		
	}
	
	function evt__cancelar()
	{
		$this->limpiar_seleccion();	
	}

	function evt__aceptar()
	{
		$this->limpiar_seleccion();	
	}

	function limpiar_seleccion()
	{
		unset($this->s__seleccion_efs);
		unset($this->s__seleccion_efs_anterior);
	}

	//-------------------------------
	//---- EI: Lista de efss ----
	//-------------------------------
	
	function evt__efs_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una efs de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las efss NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
			*/
		$orden = 1;
		foreach(array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			$registros[$id]['orden'] = $orden;
			$orden++;
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case 'A':
					//Por defecto el campo 'columnas' es igual a $this->campo_clave
					$registros[$id]['columnas'] = $registros[$id][$this->campo_clave];
					$this->id_intermedio_efs[$id] = $this->get_tabla()->nueva_fila($registros[$id]);
					break;	
				case 'B':
					$this->get_tabla()->eliminar_fila($id);
					break;	
				case 'M':
					//---Si se cambia un identificador que estaba ligado con us columna se cambia tambien el valor de la columna
					$anterior_id = $this->get_tabla()->get_fila_columna($id, $this->campo_clave);
					$anterior_col = $this->get_tabla()->get_fila_columna($id, 'columnas');
					if ($anterior_id != $registros[$id][$this->campo_clave]) {
						if ($anterior_id == $anterior_col) {
							$registros[$id]['columnas'] = $registros[$id][$this->campo_clave];
						}
					}
					$this->get_tabla()->modificar_fila($id, $registros[$id]);
					break;	
			}
		}
	}
	
	function conf__efs_lista()
	{
		if ($datos_dbr = $this->get_tabla()->get_filas()) {
			//Ordeno los registros segun la 'posicion'
			//ei_arbol($datos_dbr,"Datos para el ML: PRE proceso");
			for ($a = 0; $a < count($datos_dbr); $a++) {
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC, $datos_dbr);
			//EL formulario_ml necesita necesita que el ID sea la clave del array
			//No se solicita asi del DBR porque array_multisort no conserva claves numericas
			// y las claves internas del DBR lo son
			for ($a = 0; $a < count($datos_dbr); $a++) {
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset($datos_dbr[$a][apex_db_registros_clave]);
				$datos[$id_dbr] = $datos_dbr[$a];
			}
			//ei_arbol($datos,"Datos para el ML: POST proceso");
			return $datos;
		}
	}

	function evt__efs_lista__seleccion($id)
	{
		if (isset($this->id_intermedio_efs[$id])) {
			$id = $this->id_intermedio_efs[$id];
		}
		$this->s__seleccion_efs = $id;
	}

	function conf_fila__efs_lista($fila)
	{
		if ($this->dep('efs_lista')->existe_ef('elemento_formulario')) {
			$desactivar = (isset($this->s__seleccion_efs) && ($fila == $this->s__seleccion_efs));
			$this->dep('efs_lista')->ef('elemento_formulario')->set_solo_lectura($desactivar);
		}
	}
	
	//-----------------------------------------
	//---- EI: Info detalla de un EF ----------
	//-----------------------------------------

	function evt__efs__modificacion($datos)
	{
		$this->get_tabla()->modificar_fila($this->s__seleccion_efs_anterior, $datos);
	}
	
	function conf__efs($form)
	{
		//--- Solo el ML tiene la propiedad totalizar
		if ($this->controlador->get_clase_actual() == 'toba_ei_formulario') {
			$form->desactivar_efs(array('total'));
		}
		$this->s__seleccion_efs_anterior = $this->s__seleccion_efs;
		$fila = $this->get_tabla()->get_fila($this->s__seleccion_efs_anterior);
		$form->set_titulo('Propiedades del ef <em>'.$fila[$this->campo_clave].'</em>');		
		return $fila;
		
	}

	function evt__efs__cancelar()
	{
		unset($this->s__seleccion_efs);
		unset($this->s__seleccion_efs_anterior);
	}

	//-----------------------------------------
	//---- EI: Inicializacion del EF ----------
	//-----------------------------------------
	
	function get_mecanismos_carga()
	{
		$param = $this->get_definicion_parametros(true);		
		$tipos = array();
		if (in_array('carga_metodo', $param)) {
			$tipos[] = array('carga_metodo', 'Método de consulta PHP');
		}
		if (in_array('carga_lista', $param)) {
			$tipos[] = array('carga_lista', 'Lista fija de Opciones');
		}		
		if (in_array('carga_sql', $param)) {		
			$tipos[] = array('carga_sql', 'Consulta SQL');
		}
		return $tipos;
	}
	
	function get_tipos_clase()
	{
		return array(
			array('tipo' => 'datos_tabla', 	'descripcion' => toba_recurso::imagen_toba('objetos/datos_tabla.gif', true).' Tabla (datos_tabla)'),
			array('tipo' => 'consulta_php', 'descripcion' => toba_recurso::imagen_toba('editar.gif', true).' Clase de Consulta PHP'),
			array('tipo' => 'ci', 			'descripcion' => toba_recurso::imagen_toba('objetos/multi_etapa.gif', true).' CI controlador'),
			array('tipo' => 'estatica', 	'descripcion' => toba_recurso::imagen_toba('nucleo/php.gif', true).' Clase estática específica'),
		);
	}
	
	function get_posibles_maestros()
	{
		$filas = $this->get_tabla()->get_filas(null, true);
		$posibles = array();
		foreach ($filas as $clave => $datos) {
			if ($clave != $this->s__seleccion_efs) {
				$posibles[] = array($datos[$this->campo_clave], $datos[$this->campo_clave], $datos['orden']);
			}
		}
		$posibles = rs_ordenar_por_columna($posibles, 2, SORT_ASC); //Ordena posteriormente, sino rompe las claves
		return $posibles;
	}
	
	function get_definicion_parametros($carga=false)
	{
		$ef = $this->get_tipo_ef();
		$metodo = ($carga) ? 'get_lista_parametros_carga' : 'get_lista_parametros';
		$parametros = call_user_func(array('toba_'.$ef, $metodo));
		return $parametros;
	}
	
	function get_tipo_ef()
	{
		return $this->get_tabla()->get_fila_columna($this->s__seleccion_efs, 'elemento_formulario');
	}
	
	function set_parametros($parametros)
	{
		$this->get_tabla()->modificar_fila($this->s__seleccion_efs_anterior, $parametros);
	}

	//---------------------------------
	//---- PARAMETROS VARIOS
	//---------------------------------
	
	function conf__param_varios(toba_ei_formulario $form)
	{
		$tipo_ef = $this->get_tipo_ef();
		if (in_array($tipo_ef, array('ef_editable_numero','ef_editable_moneda', 'ef_editable_numero_porcentaje'))) {
			$form->set_descripcion('Definir los [wiki:Referencia/efs/numero parámetros del número]');
			$form->set_modo_descripcion(false);
		}
		$fila = $this->get_tabla()->get_fila($this->s__seleccion_efs_anterior);
				
		//--- Se desactivan los efs que no forman parte de la definicion
		$param = $this->get_definicion_parametros(false);
		$todos = $this->dependencia('param_varios')->get_nombres_ef();
		$efs_a_desactivar = array();
		foreach ($todos as $disponible) {
			if (! in_array($disponible, $param) ) {
				$efs_a_desactivar[] = $disponible;
				if (isset($this->parametros[$disponible])) {
					unset($this->parametros[$disponible]);	
				}
			}
		}
		
		//-- Si es un popup no eliminar la carpeta (es cosmetico)
		if (! in_array('popup_item', $efs_a_desactivar)) {
			array_borrar_valor($efs_a_desactivar, 'popup_carpeta');
			array_borrar_valor($efs_a_desactivar, 'popup_carga_desc_estatico');
			//-- Si esta seteado el item, buscar la carpeta asociada
			if (isset($fila['popup_item']) && isset($fila['popup_proyecto'])) {
				$fila['popup_carpeta'] = toba_info_editores::get_carpeta_de_item($fila['popup_item'], $fila['popup_proyecto']);
			}
		}
		$this->dependencia('param_varios')->desactivar_efs($efs_a_desactivar);

		//--- Si es un popup y tiene carga estatica chequear el checkbox
		$tiene_clase = (isset($fila['popup_carga_desc_clase']) && $fila['popup_carga_desc_clase'] != '');
		$tiene_include = (isset($fila['popup_carga_desc_include']) && $fila['popup_carga_desc_include'] != '');
		if ($tiene_clase || $tiene_include) {
			$fila['popup_carga_desc_estatico'] = 1;
		}		
		return $fila;
	}
	
	function evt__param_varios__modificacion($datos)
	{
		$this->modificado = true;
		$this->set_parametros($datos);
	}
	
	function ajax__get_regexp($tipo, toba_ajax_respuesta $respuesta)
	{
		$exp = null;
		switch($tipo) {
			case 'mail':
				$exp = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';
				break;
			case 'cuit':
				$exp = '/^[0-9]{2}-[0-9]{8}-[0-9]$/';
				break;
			case 'hora':
				$exp = '/^([0-1][0-9]|[2][0-3]):([0-5][0-9])$/';
				break;
			case 'id_valido':
				$exp = '/^[a-z0-9_]+$/';
				break;				
			default:
				throw new toba_error("El tipo $tipo no es válido");
		}
		$respuesta->set($exp);
	}
	
	//---------------------------------
	//---- PARAMETROS de CARGA
	//---------------------------------

	function conf__param_carga(toba_ei_formulario $form)
	{
		$form->ef('tipo_clase')->set_permitir_html(true);
		$lista_param = $this->get_definicion_parametros(true);
		$fila = $this->get_tabla()->get_fila($this->s__seleccion_efs_anterior);
		
		//---Desactiva los efs que no pertenecen a los parametros
		$todos = $this->dependencia('param_carga')->get_nombres_ef();
		foreach ($todos as $disponible) {
			if (! in_array($disponible, $lista_param) &&
					$disponible != 'mecanismo' &&
					$disponible != 'tipo_clase' &&
					$disponible != 'carga_metodo_lista' &&
					$disponible != 'sep_carga' &&
					$disponible != 'sep' &&
					$disponible != 'punto_montaje') {
				if (isset($fila[$disponible])) {
					unset($fila[$disponible]);
				}
				$this->dependencia('param_carga')->desactivar_efs($disponible);
			}
		}
		
		//---Determina el mecanismo
		foreach ($this->mecanismos_carga as $mec) {
			if (isset($fila[$mec])) {
				$fila['mecanismo'] = $mec;
			}
		}
		//--- Setear el combo de tipo_clase
		if (isset($fila['mecanismo']) && $fila['mecanismo'] == 'carga_metodo') {
			if (isset($fila['carga_clase']) && $fila['carga_clase'] != '') {
				$fila['tipo_clase'] = 'estatica';
			} elseif (isset($fila['carga_dt']) && $fila['carga_dt'] != '') {
				 $fila['tipo_clase'] = 'datos_tabla';
			} elseif (isset($fila['carga_consulta_php']) && $fila['carga_consulta_php'] != '') {
				 $fila['tipo_clase'] = 'consulta_php';
				 $fila['carga_metodo_lista'] = $fila['carga_metodo'];
			} else {
				$fila['tipo_clase'] = 'ci';
			}
		}
		return $fila;
	}
	
	function evt__param_carga__modificacion($datos)
	{
		$this->modificado = true;		
		$actual = $datos['mecanismo'];
		foreach ($this->mecanismos_carga as $valor_mec) {
			//-- Se quitan los valores de los otros mecanismo por si la interface no lo hizo
			if ($valor_mec != $actual && isset($datos[$valor_mec])) {
				//-- Caso particular a la carga php y dt que comparten un parametro
				if ($actual != 'carga_dt' && $valor_mec == 'carga_metodo') {
					unset($datos[$valor_mec]);
				}
			}
		}
		if ($datos['mecanismo'] != null) {
			unset($datos['mecanismo']);
			unset($datos['estatico']);
		} else {
			//--- Limpia los valores
			$datos = array('carga_maestros' => null);
			foreach ($this->mecanismos_carga as $mec) {
				if ($mec != 'punto_montaje') {				//No blanqueo el punto de montaje
					$datos[$mec] = null;					//generalmente es el mismo para el EF, pero hay que separar este campo en 2 (proxima version)
				}
			}
		}
		//-- Si selecciona otro mecanismo o tipo de clase de carga php, blanquear el datos tabla
		if ($actual != 'carga_metodo' || $datos['tipo_clase'] != 'datos_tabla') {
			$datos['carga_dt'] = null;
		}
		$this->set_parametros($datos);
	}
			
	function ajax__existe_metodo_dt($dt, toba_ajax_respuesta $respuesta)
	{
		$subclase = toba_info_editores::get_subclase_componente($dt);
		if (isset($subclase) && !empty($subclase)) {
			$archivo = toba::instancia()->get_path_proyecto(toba_contexto_info::get_proyecto()).'/php/'.$subclase['subclase_archivo'];
			$php = new toba_archivo_php($archivo);
			if ($php->existe() && $php->contiene_metodo('get_descripciones')) {
				$sql = $this->get_sql_carga_tabla($dt);
				$respuesta->set($sql);
			} else {
				$respuesta->set(false);
			}
		} else {
			$respuesta->set(false);
		}
	}
	
	function ajax__crear_metodo_get_descripciones($dt, toba_ajax_respuesta $respuesta)
	{
		$sql = $this->get_sql_carga_tabla($dt);
		if (isset($sql)) {
			$datos = toba_info_editores::get_tabla_fuente_de_dt($dt);			
			$asistente = new toba_asistente_adhoc();
			$asistente->asumir_confirmaciones();
			$molde = $asistente->get_molde_datos_tabla($datos['tabla'], $datos['fuente_datos']);
			$molde->crear_metodo_consulta('get_descripciones', $sql[0]);
			$molde->generar();
			$respuesta->set($sql);
		} else {
			$respuesta->set(false);			
		}
	}	
	
	protected function get_sql_carga_tabla($dt)
	{
		$datos = toba_info_editores::get_tabla_fuente_de_dt($dt);		
		if (! empty($datos)) {		
			$db = toba::db($datos['fuente_datos'], toba_editor::get_proyecto_cargado());
			$sql = $db->get_sql_carga_descripciones($datos['tabla']);
			return $sql;
		}
	}
	
	//---------------------------------
	//---- EI: IMPORTAR definicion ----
	//---------------------------------

	function evt__efs_importar__importar($datos)
	{
		$this->s__importacion_efs = $datos;
		$this->disparar_importacion_efs = true;
	}

	/**
	*	La importacion se ejecuta al final asi se procesa despues de manipular el ML de Efs
	*/
	function post_eventos()
	{
		if ($this->disparar_importacion_efs ) {
			if (isset($this->s__importacion_efs['datos_tabla'])) {
				$clave = array('proyecto' => toba_editor::get_proyecto_cargado(),
											'componente' => $this->s__importacion_efs['datos_tabla']);
				$dt = toba_constructor::get_info($clave, 'toba_datos_tabla');
				$this->s__importacion_efs = $dt->exportar_datos_efs($this->s__importacion_efs['pk']);
				foreach ($this->s__importacion_efs as $ef) {
					try {
						if (! $this->get_tabla()->existe_fila_condicion(array($this->campo_clave => $ef[$this->campo_clave]))) {
							$this->get_tabla()->nueva_fila($ef);						
						}
					} catch(toba_error $e) {
						toba::notificacion()->agregar("Error agregando el EF '{$ef[$this->campo_clave]}'. " . $e->getMessage());
					}
				}
			}
			$this->disparar_importacion_efs = false;
		}
	}

	function conf__efs_importar()
	{
		if (isset($this->s__importacion_efs)) {
			return $this->s__importacion_efs;
		}
	}

	
	//---------------------------------
	//---- EI: Cascadas		 ----
	//---------------------------------	
	function conf__esquema_cascadas()
	{
		$diagrama = "digraph G {\nsize=\"7,7\";rankdir=LR;\n";		
		$diagrama .= "node [shape=record];\n";
		foreach ($this->get_tabla()->get_filas() as $ef) {
			$maestros = isset($ef['carga_maestros']) ? trim($ef['carga_maestros']) : '';
			if ($maestros != '') {
				foreach (explode(',', $maestros) as $dep) {
					$diagrama .= $dep.'->'.$ef[$this->campo_clave].";\n";
				}
			}
		}
		$diagrama .= ' }';
		return $diagrama;
	}
	
	function hay_cascadas()
	{
		foreach ($this->get_tabla()->get_filas() as $ef) {
			if (isset($ef['carga_maestros']) && trim($ef['carga_maestros']) != '') {
				return true;
			}
		}
		return false;
	}	
	
	
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__efs_lista__mostrar_esquema = function() {
				this.ajax_html('esquema_cascadas', null, this.nodo_pie());
				return false;	
			}
		";
	}

	
	function ajax__esquema_cascadas($parametros, toba_ajax_respuesta $respuesta)
	{
		$this->pantalla()->agregar_dep('esquema_cascadas');
		$this->dep('esquema_cascadas')->generar_html();
	}
	
}
?>