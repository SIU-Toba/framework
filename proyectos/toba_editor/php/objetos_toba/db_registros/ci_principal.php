<?php
require_once('objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $db_tablas;
	protected $clase_actual = 'toba_datos_tabla';	
	protected $s__ap_php_db;							// La base posee registro de la existencia de una extension??
	protected $s__ap_php_archivo;						// El archivo de la extension existe en el sistema de archivos??
	
	function conf()
	{
		parent::conf();
		//Mecanismo para saber si la extension PHP de un AP ya exite en la DB y posee archivo
		if ( !isset($this->s__ap_php_db) ) {
			$this->s__ap_php_db = false;
			$this->s__ap_php_archivo = false;
			if ( $this->componente_existe_en_db() ) {
				$datos_ap = $this->get_entidad()->tabla('prop_basicas')->get();
				if( ( $datos_ap['ap'] == 0 ) && $datos_ap['ap_clase'] && $datos_ap['ap_archivo'] ) {
					$this->s__ap_php_db = true;	//El AP esta extendido
				}
				if( admin_util::existe_archivo_subclase($datos_ap['ap_archivo']) ) {
					$this->s__ap_php_archivo = true; //La extension existe
				}
			}
		}
		// Se configura el FORM para que dispare el evento de recarga de tablas.
		$cols = $this->dep('datos')->tabla('columnas')->get_cantidad_filas();
		$this->pantalla()->tab('2')->set_etiqueta("Columnas [$cols]");
		if( ($this->get_id_pantalla() == '1') ){
			if($cols > 0) {
				$uniq = $this->dep('datos')->tabla('valores_unicos')->get_cantidad_filas();
				$exts = $this->dep('datos')->tabla('externas')->get_cantidad_filas();
				$txt_uniq = ($uniq > 0)? " - Valores únicos: $uniq" : '';
				$txt_exts = ($exts > 0)? " - Cargas externas: $exts": '';
				$this->dep('prop_basicas')->set_modo_recarga("¿Desea recargar las columnas de la tabla?" .
																" Se eliminaran los elementos definidos anteriormente. ".
																" (Columnas: $cols $txt_exts $txt_uniq)." .
																"Los cambios no seran actualizados hasta presionar el boton \'Guardar\'.".
																" ATENCION: Si no recarga los valores automaticamente, hágalo a mano para ".
																" que la definicion de la tabla y las columnas coincida.");
			}else{
				$this->dep('prop_basicas')->set_modo_recarga('');
			}
		}
		//En este editor se setea la fuente de datos en un form inferior
		$this->dep('base')->desactivar_efs('fuente_datos');
	}

	function get_entidad()
	{
		$this->dependencia('datos')->tabla('externas')->set_es_unico_registro(false);
		return parent::get_entidad();	
	}
	
	function validar()
	{
		$datos = $this->get_entidad()->tabla('columnas')->get_filas();
		if (! $this->verificar_existencia_columna_clave($datos)){
			throw new toba_error('La tabla debe tener una columna como Clave Primaria');
		}
	}
	
	function evt__procesar()
	{
		$this->validar();
		try {			
			parent::evt__procesar();
			unset($this->s__ap_php_db);
			unset($this->s__ap_php_archivo);
			admin_util::refrescar_barra_lateral();
		} catch (toba_error_db $e) {
			if( $e->get_sqlstate()==23505 ) {
				$datos = $this->get_entidad()->tabla('prop_basicas')->get();
				throw new toba_error("Ya existe un datos_tabla referenciado a la tabla: '".$datos['tabla']."'. No es posible guardar.");
			}else{
				throw $e;
			}
		}
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas($form)
	{
		// Hay extension
		$param_editor = toba_componente_info::get_utileria_editor_parametros(array('proyecto'=>$this->id_objeto['proyecto'],
																			'componente'=> $this->id_objeto['objeto']),
																		'ap');

		$eliminar_extension = !isset($this->id_objeto); //Si es alta no se puede extender
		if ( $this->s__ap_php_db ) {
			$form->evento('ver_php')->vinculo()->set_parametros($param_editor);
			if ( $this->s__ap_php_archivo ) {
				// El archivo de la extension existe
				$abrir = toba_componente_info::get_utileria_editor_abrir_php(array('proyecto'=>$this->id_objeto['proyecto'],
																				'componente'=> $this->id_objeto['objeto']),
																			'ap');
				$form->set_js_abrir( $abrir['js'] );
				$eliminar_extension = true;
			} else {
				$form->evento('ver_php')->set_imagen('nucleo/php_ap_inexistente.gif');
				$form->eliminar_evento('abrir_php');
				$form->evento('extender_ap')->vinculo()->set_parametros($param_editor);
			}
		} else {
			$form->eliminar_evento('ver_php');	
			$form->eliminar_evento('abrir_php');
			$form->evento('extender_ap')->vinculo()->set_parametros($param_editor);			
		}
		$datos = $this->get_entidad()->tabla("prop_basicas")->get();
		if (! isset($datos['fuente_datos'])) {
			$datos['fuente_datos_proyecto'] = toba_editor::get_proyecto_cargado();
			$datos['fuente_datos'] = toba_info_editores::get_fuente_datos_defecto(toba_editor::get_proyecto_cargado());
		}
		if ($eliminar_extension) {
			$form->eliminar_evento('extender_ap');
		}		
		$form->set_datos($datos);
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla('base')->set_columna_valor('fuente_datos',$datos['fuente_datos']);
		$this->get_entidad()->tabla('base')->set_columna_valor('fuente_datos_proyecto',$datos['fuente_datos_proyecto']);
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	function get_tablas($fuente)
	{
		return toba::db($fuente['fuente_datos'], toba_editor::get_proyecto_cargado())->get_lista_tablas();
	}
	
	/* Evento de generacion de columnas en base a la tabla seleccionada */
	function evt__prop_basicas__cargar_tablas($datos)
	{
		$this->evt__prop_basicas__modificacion($datos);
		//Borro la informacion previa. Ya avise en JS que se iba a hacer
		$this->dep('datos')->tabla('valores_unicos')->eliminar_filas();
		$this->dep('datos')->tabla('externas_col')->eliminar_filas();
		$this->dep('datos')->tabla('externas')->eliminar_filas();
		$this->dep('datos')->tabla('columnas')->eliminar_filas();
		$this->get_entidad()->actualizar_campos();
		$this->actualizar_nombre_objeto_dt($datos);
	}

	/* Actualizo el nombre del objeto en el datos tabla basico para que se refleje en pantalla*/
	function actualizar_nombre_objeto_dt($datos)
	{
		if (isset($datos['tabla']) && !is_null($datos['tabla'])) {
			$this->get_entidad()->tabla('base')->set_columna_valor('nombre', 'DT - ' . $datos['tabla']);
		}
	}

	/* Esta funcion devuelve un nombre defecto si no esta seteado en el datos_tabla basico*/
	function get_abreviacion_clase_actual()
	{
		$datos = $this->get_entidad()->tabla('base')->get();
		if (isset($datos['nombre'])){
			return $datos['nombre'];
		}else{
			return call_user_func(array($this->get_clase_info_actual(), 'get_tipo_abreviado'));
		}
	}
	//*******************************************************************
	//**  COLUMNAS  *****************************************************
	//*******************************************************************
	
	
	function conf__columnas()
	{
		return $this->get_entidad()->tabla('columnas')->get_filas(null,true);	
	}

	function evt__columnas__modificacion($datos)
	{
		$this->get_entidad()->tabla('columnas')->procesar_filas($datos);
		if (! $this->verificar_existencia_columna_clave($datos)){
			toba::notificacion()->agregar('No existe una Clave Primaria asociada a esta tabla','error');
		}
	}

	//-- Generacion automatica de columnas!!
	
	function evt__columnas__leer_db($datos)
	{
		$this->evt__columnas__modificacion($datos);
		$this->get_entidad()->actualizar_campos();
	}

	function verificar_existencia_columna_clave($datos)
	{
		$hay_pk = false;
		foreach($datos as $columnas){
			$hay_pk = $hay_pk || ($columnas['pk'] == '1');
		}				
		return $hay_pk;
	}
	
	//*******************************************************************
	//**  EXTERNAS  *****************************************************
	//*******************************************************************	
	
	/**
	 * Configuración pantalla carga externa
	 */
	function conf__3()
	{
		if (count($this->get_lista_columnas_ext()) == 0) {
			$this->pantalla()->eliminar_dep('detalle_carga');
			$this->pantalla()->eliminar_dep('externas');
			$this->pantalla()->set_descripcion('La carga externa sólo es necesaria cuando se han definido'.
								' columnas EXTERNAS');
		} else {
			if (! $this->get_entidad()->tabla('externas')->hay_cursor()) {
				$this->pantalla()->eliminar_dep('detalle_carga');
			}
		}
	}

	function get_lista_columnas_ext()
	{
		return $this->get_entidad()->tabla('columnas')->get_filas(array('externa' => 1));
	}
	
	function get_lista_columnas_no_ext()
	{
		return $this->get_entidad()->tabla('columnas')->get_filas(array('externa' => 0));
	}
	
	
	function conf__externas(toba_ei_formulario_ml $ml)
	{
		$ml->set_proximo_id($this->get_entidad()->tabla('externas')->get_proximo_id());
		$datos = $this->get_entidad()->tabla('externas')->get_filas(null,true);
		foreach (array_keys($datos) as $id) {
			$buscador = $this->get_entidad()->tabla('externas_col')->nueva_busqueda();
			$buscador->set_padre('externas', $id);
			
			//--- De esta carga, se filtran las filas que son parametros y se buscan sus columnas padres
			$datos[$id]['col_parametro'] = $this->get_columnas_externas($buscador, 0);

			//--- De esta carga, se filtran las filas que son resultado y se buscan sus columnas padres
			$datos[$id]['col_resultado'] = $this->get_columnas_externas($buscador, 1);
			
		}
		$ml->set_datos($datos);
	}
	
	protected function get_columnas_externas($buscador, $es_resultado)
	{
		$buscador->limpiar_condiciones();
		$buscador->set_condicion('es_resultado', '==', $es_resultado);
		$col_exts = $buscador->buscar_ids();
		return $this->get_entidad()->tabla('externas_col')->get_id_padres($col_exts, 'columnas');
	}

	
	function evt__externas__seleccion($id_ext)
	{
		$this->get_entidad()->tabla('externas')->set_cursor($id_ext);
	}
	
	function evt__externas__modificacion($datos)
	{
		foreach ($datos as $id => $fila) {
			if (isset($fila['col_parametro'])) {
				$col_parametros = $fila['col_parametro'];
				unset($fila['col_parametro']);
			}
			if (isset($fila['col_resultado'])) {			
				$col_resultados = $fila['col_resultado'];
				unset($fila['col_resultado']);
			}
			$accion = $fila[apex_ei_analisis_fila];
			unset($fila[apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->get_entidad()->tabla('externas')->nueva_fila($fila, null, $id);
					$this->reasociar_columnas_externas($id, $col_parametros, $col_resultados);
					break;	
				case "B":
					$this->get_entidad()->tabla('externas')->eliminar_fila($id);
					break;	
				case "M":
					$this->get_entidad()->tabla('externas')->modificar_fila($id, $fila);
					$this->reasociar_columnas_externas($id, $col_parametros, $col_resultados);					
					break;	
			}
		}
	}

	/**
	 * Asocia la carga externa con un conjunto de columnas
	 * Como la asociacion es embebida, es seguro borrar todo primero y agregarlas nuevamente
	 */
	protected function reasociar_columnas_externas($id_ext, $col_parametros, $col_resultados)
	{
		$this->get_entidad()->tabla('externas')->set_cursor($id_ext);
		$buscador = $this->get_entidad()->tabla('externas_col')->nueva_busqueda();
		$buscador->set_padre('externas', $id_ext);		
		
		//---Se busca si el set actual es distinto al anterior
		$col_parametro_actuales = $this->get_columnas_externas($buscador, 0);
		$col_resultado_actuales = $this->get_columnas_externas($buscador, 1);
		$buscador->limpiar_condiciones();
		
		//--- Si hay alguna diferencia, borra los actuales y agrega los nuevos
		if ($col_parametros != $col_parametro_actuales || $col_resultados != $col_resultado_actuales) {
			
			//--- Borra las columnas actuales
			foreach ($buscador->buscar_ids() as $id_hija) {
				$this->get_entidad()->tabla('externas_col')->eliminar_fila($id_hija);
			}
			//--- Columnas Parámetros
			foreach($col_parametros as $col_par) {
				$padre = array('externas' => $id_ext, 'columnas' => $col_par);
				$this->get_entidad()->tabla('externas_col')->nueva_fila(array('es_resultado' => 0),
																		$padre);
			}
			//--- Columnas Resultado
			foreach($col_resultados as $col_par) {
				$padre = array('externas' => $id_ext, 'columnas' => $col_par);
				$this->get_entidad()->tabla('externas_col')->nueva_fila(array('es_resultado' => 1),
																		$padre);
			}
		}
		$this->get_entidad()->tabla('externas')->restaurar_cursor();
	}

	function conf__detalle_carga(toba_ei_formulario $form)
	{
		$datos = $this->get_entidad()->tabla('externas')->get();
		if (isset($datos['tipo']) && $datos['tipo'] == 'sql') {
			$lista = array();
			foreach ($form->get_nombres_ef() as $id_ef) {
				if ($id_ef != 'sql') {
					$lista[] = $id_ef;	
				}
			}
			$form->desactivar_efs($lista);
		} else {
			if( isset($datos['clase']) && $datos['clase'] && 
					isset($datos['include']) && $datos['include'] ) {
				$datos['estatico'] = 1;
			}
			$form->desactivar_efs(array('sql'));
		}
		$form->set_datos($datos);
	}		
	
	function evt__detalle_carga__cancelar()
	{
		$this->get_entidad()->tabla('externas')->resetear_cursor();		
	}
	
	function evt__detalle_carga__modificacion($datos)
	{
		$this->get_entidad()->tabla('externas')->set($datos);
	}
	
	function evt__detalle_carga__aceptar($datos)
	{
		$this->evt__detalle_carga__modificacion($datos);		
		$this->get_entidad()->tabla('externas')->resetear_cursor();
	}
	
	//*******************************************************************
	//**  VALORES UNICOS  ***********************************************
	//*******************************************************************	

	function conf__4()
	{
		if (count($this->get_lista_columnas()) == 0) {
			$this->pantalla()->eliminar_dep('valores_unicos');
			$this->pantalla()->set_descripcion('No hay columnas definidas.');
		} else {
			$this->pantalla()->set_descripcion('Defina las combinaciones de columnas que deben ser unicas por fila.');
		}
	}
	
	function get_lista_columnas()
	{
		return $this->get_entidad()->tabla('columnas')->get_filas();
	}

	function evt__valores_unicos__modificacion($datos)
	{
		$this->get_entidad()->tabla('valores_unicos')->procesar_filas($datos);
	}

	function conf__valores_unicos($c)
	{
		$c->set_datos($this->get_entidad()->tabla('valores_unicos')->get_filas());
	}
}
?>