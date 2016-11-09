<?php
require_once('seleccion_imagenes.php');

define('id_temporal', 'Automático');

class ci_principal extends toba_ci
{
	protected $s__id_item ;
	protected $s__inicializar_item_nuevo = true;
	protected $s__fuentes;
	protected $s__hay_con_permisos;
	protected $cambio_item = false;	
	private $elemento_eliminado = false;
	private $refrescar = false;
	
	function ini__operacion()
	{
		if (! isset($this->s__fuentes)) {
			$this->s__fuentes = toba_info_editores::get_fuentes_datos(toba_editor::get_proyecto_cargado());			
		}
		$this->s__hay_con_permisos = false;
		foreach ($this->s__fuentes as $fuente) {
			if (isset($fuente['permisos_por_tabla']) && $fuente['permisos_por_tabla'] == '1') {
				$this->s__hay_con_permisos = true;
			}
		}	
	}
		
	function ini()
	{
		//Se quita la secuencia para manejar el caso de alta de un ID alfanumerico a gusto
		$this->get_entidad()->tabla('base')->set_definicion_columna('item', 'secuencia', null);

		//Se quita el control de concurrencia porque permite modificar claves
		$this->get_entidad()->persistidor()->set_lock_optimista(false);		
		$zona = toba::zona();
		if ($zona->cargada()) {
			list($proyecto, $item) = $zona->get_editable();
			//Se notifica un item y un proyecto	
			if (isset($item) && isset($proyecto)) {
				//Se determina si es un nuevo item
				$es_nuevo = (!isset($this->s__id_item ) || 
							($this->s__id_item['proyecto'] != $proyecto || $this->s__id_item['item'] != $item));
				if ($es_nuevo) {
					$this->set_item(array('proyecto'=>$proyecto, 'item'=>$item));
					$this->cambio_item = true;
				}
			}
		} else {
			//Creacion de un item nuevo
			if ($this->s__inicializar_item_nuevo) {
				unset($this->s__id_item );
				$datos = $this->get_entidad();
				$datos->resetear();
				$this->inicializar_item($datos);
				$this->s__inicializar_item_nuevo = false;
			}
		}
	}
	
	function get_entidad()
	{	//Acceso al DATOS_RELACION
		if ($this->cambio_item) {
			toba::logger()->debug($this->get_txt() . '*** se cargo el item: ' . print_r($this->s__id_item,true));
			$this->dependencia('datos')->cargar($this->s__id_item);
		}
		return $this->dependencia('datos');
	}	

	function set_item($id)
	{
		$this->s__id_item  = $id;
	}
	
	function conf()
	{
		if (! $this->get_entidad()->esta_cargada()) {
			$this->pantalla()->eliminar_evento('eliminar');
		}		
		if (! $this->s__hay_con_permisos) {
			$this->pantalla()->eliminar_tab('pant_permisos');
		}
	}	
	
	/**
	*	Inicializacion de un ITEM nuevo, llega el DR vacio
	*/
	function inicializar_item($dr)
	{
		//Ver si el padre viene por post
		$datos = array();															//TODO: See this -.-
		$padre_i = toba::memoria()->get_parametro('padre_i');
		$padre_p = toba::memoria()->get_parametro('padre_p');
		if (isset($padre_p) && isset($padre_i)) {
			$datos = array('item' => id_temporal);
			$datos['padre'] = $padre_i;
			$datos['padre_proyecto'] = $padre_p;
		}
		$dr->tabla('base')->set($datos);
		//Le agrego el permiso del usuario actual
		foreach (toba::usuario()->get_grupos_acceso() as $grupo) {
			$permiso_usuario_actual = array('usuario_grupo_acc' => $grupo);
			$dr->tabla('permisos')->nueva_fila($permiso_usuario_actual);
		}
	}

	//-------------------------------------------------------------------
	//--- PROPIEDADES BASICAS
	//-------------------------------------------------------------------

	function conf__prop_basicas(toba_ei_formulario $form)
	{
		$datos = $this->get_entidad()->tabla('base')->get();
		if (!isset($datos['carpeta']) || $datos['carpeta'] != 1) {
			if (!$this->get_entidad()->esta_cargada()) {
				$form->ef('item')->set_iconos_utilerias(array(new utileria_identificador_nuevo()));
			} else {
				$form->ef('item')->set_iconos_utilerias(array(new utileria_identificador_actual()));
			}
		}
	
		//Transfiere los campos accion, buffer y patron a uno comportamiento
		if (isset($datos['actividad_accion']) && $datos['actividad_accion'] != '') {
			$datos['comportamiento'] = 'accion';
		}
		if (isset($datos['actividad_buffer']) && $datos['actividad_buffer'] != 0) {
			$datos['comportamiento'] = 'buffer';
		}
		if (isset($datos['actividad_patron']) && $datos['actividad_patron'] != 'especifico') {
			$datos['comportamiento'] = 'patron';
		}
		if (! isset($datos['pagina_tipo'])) {
			$pagina = toba_info_editores::get_tipo_pagina_defecto();
			$datos['pagina_tipo'] = $pagina['pagina_tipo'];
			$datos['pagina_tipo_proyecto'] = $pagina['proyecto']; 
		}
		if ($form->existe_ef('accion')) {
			$form->ef('accion')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
		}
		$form->set_datos($datos);
	}

	function evt__prop_basicas__modificacion($registro)
	{
		//El campo comportamiento incide en el buffer, patron y accion
		if ($registro['solicitud_tipo'] == 'browser') {		
			switch ($registro['comportamiento']) {
				case 'accion':
					$registro['actividad_buffer'] = 0;
					$registro['actividad_patron'] = 'especifico';
					break;
				case 'buffer':
					$registro['actividad_accion'] = '';
					$registro['actividad_patron'] = 'especifico';				
					break;
				case 'patron':
					$registro['actividad_buffer'] = 0;
					$registro['actividad_accion'] = '';
					break;								
			}
		}
		unset($registro['comportamiento']);
		$this->get_entidad()->tabla('base')->set($registro);
	}
	
	//----------------------------------------------------------
	//-- OBJETOS -----------------------------------------------
	//----------------------------------------------------------
	function conf__objetos()
	{
		$objetos = $this->get_entidad()->tabla('objetos')->get_filas(null, true);
		return $objetos;
	}
	
	function evt__objetos__modificacion($objetos)
	{
		$this->get_entidad()->tabla('objetos')->procesar_filas($objetos);
	}
	
	//----------------------------------------------------------
	//-- PERMISOS -------------------------------------------------
	//----------------------------------------------------------
	
	function conf__pant_permisos_tablas(toba_ei_pantalla $pant)
	{		
		if (! $this->s__hay_con_permisos && $this->existe_dependencia('form_tablas')) {
			$pant->eliminar_dep('form_tablas');
			$this->get_entidad()->tabla('permisos_tablas')->eliminar_filas();
		}
	}
		
	/*
	*	Toma los permisos actuales, les agrega los grupos faltantes y les pone descripcion
	*/
	function conf__permisos()
	{
		$asignados = $this->get_entidad()->tabla('permisos')->get_filas();
		$grupos = toba_info_permisos::get_grupos_acceso(toba_editor::get_proyecto_cargado());
		$datos = array();
		foreach ($grupos as $grupo) {
			//El grupo esta asignado al item?
			$esta_asignado = false;	
			foreach ($asignados as $asignado) {
				//Si esta asignado ponerle el nombre del grupo y chequear el checkbox
				if ($asignado['usuario_grupo_acc'] == $grupo['usuario_grupo_acc']) {
					$grupo['tiene_permiso'] = 1;
					$grupo['item'] = $this->s__id_item['item'];
					$esta_asignado = true;
				}
			}
			//Si no esta asignado poner el item y deschequear el checkbox
			if (!$esta_asignado) {
				$grupo['tiene_permiso'] = 0;
				$grupo['item'] = $this->s__id_item['item'];
			}
			$datos[] = $grupo;
		}
		return $datos;
	}
	
	function evt__permisos__modificacion($grupos)
	{
		$dbr = $this->get_entidad()->tabla('permisos');
		$asignados = $dbr->get_filas(array(), true);
		if (!$asignados) {
			$asignados = array();		
		}
		//ei_arbol($asignados, 'asignados');
		//ei_arbol($grupos, 'nuevos');
		foreach ($grupos as $grupo) {
			$estaba_asignado = false;
			foreach ($asignados as $id => $asignado) {
				//¿Estaba asignado anteriormente?
				if ($asignado['usuario_grupo_acc'] == $grupo['usuario_grupo_acc']) {
					$estaba_asignado = true;
					if (! $grupo['tiene_permiso']) {
						//Si estaba asignado, y fue deseleccionado entonces borrar
						$dbr->eliminar_fila($id);
					}
				}
			}
			//Si no estaba asignado y ahora se asigna, agregarlo
			if (!$estaba_asignado && $grupo['tiene_permiso']) {
				unset($grupo['tiene_permiso']);
				unset($grupo['nombre']);
				$dbr->nueva_fila($grupo);
			}
		}
	}
	
	function conf__form_tablas(toba_ei_formulario_ml $form) 
	{
		$configurados = $this->get_entidad()->tabla('permisos_tablas')->get_cantidad_filas();
		$permisos = array();
		
		foreach ($this->s__fuentes as $fuente) {
			$con_permisos = $fuente['permisos_por_tabla'];
			if (! $con_permisos) {
				continue;
			}
			
			if ($configurados === 0) {												//No hay permisos configurados
					$fuente['esquema'] = $fuente['schema'];
					$fuente['proyecto'] = toba_editor::get_proyecto_cargado();
					$fuente['tablas_modifica'] = array();
					$permisos[] = $fuente;
			} else {															//Hay permisos previos
				$aux = array();				
				$datos_fila = $this->get_entidad()->tabla('permisos_tablas')->get_filas(array('fuente_datos' => $fuente['fuente_datos']));
				foreach ($datos_fila as $fila) {										//Agrupo las tablas por schema
					$indx = $fila['esquema'];
					$aux[$indx][] = $fila['tabla'];
				}
				if (! empty($aux)) {
					foreach($aux as $schema => $tablas) {
						$permisos[] = array('fuente_datos' => $fuente['fuente_datos'], 'esquema' => $schema, 'proyecto' => toba_editor::get_proyecto_cargado(), 'tablas_modifica' => $tablas);
					}
					unset($aux);
				}				
			}
		}
		$form->set_datos($permisos);
	}
	
	function evt__form_tablas__modificacion($datos) 
	{
		$conj_actual = array();
		$datos_actuales = $this->get_entidad()->tabla('permisos_tablas')->get_filas();
		foreach($datos_actuales as $fila) {
			$indx = "{$fila['fuente_datos']}_{$fila['esquema']}_{$fila['tabla']}";
			$conj_actual[$indx] = $fila;
		}
		
		$conj_nuevo = array();
		foreach($datos as $fila) {
			foreach ($fila['tablas_modifica'] as $tabla) {
				$indx = "{$fila['fuente_datos']}_{$fila['esquema']}_$tabla";
				$conj_nuevo[$indx] = $fila;
				$conj_nuevo[$indx]['tabla'] = $tabla;
			}
		}
		
		$nuevos = array_diff_key($conj_nuevo, $conj_actual);
		$borrados = array_diff_key($conj_actual, $conj_nuevo);
		
		foreach($borrados as $fila) {
			$condicion = array('fuente_datos' => $fila['fuente_datos'], 'esquema' => $fila['esquema'], 'tabla' => $fila['tabla']);			
			$id = $this->get_entidad()->tabla('permisos_tablas')->get_id_fila_condicion($condicion);			
			$this->get_entidad()->tabla('permisos_tablas')->eliminar_fila(current($id));			
		}
		
		foreach($nuevos as $fila) {
			$id = $this->get_entidad()->tabla('permisos_tablas')->nueva_fila($fila);
			$this->set_permisos_basicos($id);
		}
	}
	
	function get_tablas_fuente($fuente, $schema = null) 
	{
		try {
			return toba::db($fuente, toba_editor::get_proyecto_cargado())->get_lista_tablas(false, $schema);
		} catch (toba_error $e) {
			toba::notificacion()->warning("La fuente '$fuente' no está definida en bases.ini. Si guarda los cambios es posible que borre información existente");
			//No esta definida en bases.ini
			return array();
		}
	}

	function get_schemas_fuente($fuente) 
	{
		try {
			return toba::db($fuente, toba_editor::get_proyecto_cargado())->get_lista_schemas_disponibles();
		} catch (toba_error $e) {
			toba::notificacion()->warning("La fuente '$fuente' no está definida en bases.ini. Si guarda los cambios es posible que borre información existente");
			//No esta definida en bases.ini
			return array();
		}
	}

	//----------------------------------------------------------------------------------------------------------------------------------------------------//
	//								PANTALLA PERMISOS									   //
	//----------------------------------------------------------------------------------------------------------------------------------------------------//
	function conf__form_acl(toba_ei_formulario_ml $form)
	{
		$datos = $this->get_entidad()->tabla('permisos_tablas')->get_filas();
		toba::logger()->var_dump($datos);
		foreach($datos as $klave => $fila) {
			$permisos = explode(',' , $fila['permisos']);
			if (in_array('select', $permisos)) {
				$datos[$klave]['chk_select'] = 1;
			}
			if (in_array('insert', $permisos)) {
				$datos[$klave]['chk_insert'] = 1;
			}
			if (in_array('update', $permisos)) {
				$datos[$klave]['chk_update'] = 1;
			}
			if (in_array('delete', $permisos)) {
				$datos[$klave]['chk_delete'] = 1;
			}			
		}
		$form->set_datos($datos);
	}
	
	function evt__form_acl__modificacion($datos)
	{
		foreach($datos as $klave => $fila) {
			$permisos = array();
			$datos[$klave]['permisos'] = array();
			if (isset($fila['chk_select']) && $fila['chk_select'] == 1) {
				$permisos[] = 'select';
			}
			if (isset($fila['chk_insert']) && $fila['chk_insert'] == 1) {
				$permisos[] = 'insert';
			}
			if (isset($fila['chk_update']) && $fila['chk_update'] == 1) {
				$permisos[] = 'update';
			}
			if (isset($fila['chk_delete']) && $fila['chk_delete'] == 1) {
				$permisos[] = 'delete';
			}
			if (! empty($permisos)) {
				$datos[$klave]['permisos'] = implode(',', $permisos);
			}
		}
		$this->get_entidad()->tabla('permisos_tablas')->procesar_filas($datos);
	}
	
	function set_permisos_basicos($id_fila)
	{
		$this->get_entidad()->tabla('permisos_tablas')->set_fila_columna_valor($id_fila, 'permisos', 'select,insert,update,delete');		
	}
	
	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$basicos = $this->get_entidad()->tabla('base');
		$basicos->set_fila_columna_valor(0, 'proyecto', toba_editor::get_proyecto_cargado());
		$es_temporal = $basicos->get_columna('item') == id_temporal;
		if ($es_temporal) {
			//Reemplazar el automático por la secuencia
			$basicos->set_columna_valor('item', toba::instancia()->get_db()->recuperar_nuevo_valor_secuencia('apex_item_seq'));
		}
		
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();	
		$datos = $this->get_entidad()->tabla('base')->get();		
		
		//Si el proyecto usa esquema de permisos por tabla
		$modelo_proyecto = toba_editor::get_modelo_proyecto();
		try {
			$modelo_proyecto->generar_roles_db($datos['item']);
		} catch (toba_error_db $e) {
			toba::notificacion()->error('Error al actualizar los roles postgres para esta operación', $e->get_mensaje_log());
		}
		
		if (! isset($this->s__id_item )) {		//Si el item es nuevo
			admin_util::refrescar_editor_item($datos['item']);						
			admin_util::redirecionar_a_editor_item($datos['proyecto'], $datos['item']);			
		}
	}

	function evt__eliminar()
	{
		$this->get_entidad()->eliminar();
		toba::notificacion()->agregar('La operación ha sido eliminada', 'info');
		toba::zona()->resetear();
		admin_util::refrescar_editor_item();
	}
	

	/**
	 * Servicio para mostrar la imagen
	 */
	function servicio__ejecutar()
	{
		seleccion_imagenes::generar_html_listado();
	}
}



?>
