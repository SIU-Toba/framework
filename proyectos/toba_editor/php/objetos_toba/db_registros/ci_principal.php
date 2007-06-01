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
	}

	function get_entidad()
	{
		$this->dependencia('datos')->tabla('externas')->set_es_unico_registro(false);
		return parent::get_entidad();	
	}
	
	function evt__procesar()
	{
		parent::evt__procesar();
		unset($this->s__ap_php_db);
		unset($this->s__ap_php_archivo);
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas($form)
	{
		if ( $this->s__ap_php_db ) {
			// Hay extension
			$parametros = toba_componente_info::get_utileria_editor_parametros(array('proyecto'=>$this->id_objeto['proyecto'],
																				'componente'=> $this->id_objeto['objeto']),
																			'ap');
			$form->evento('ver_php')->vinculo()->set_parametros($parametros);
			if ( $this->s__ap_php_archivo ) {
				// El archivo de la extension existe
				$abrir = toba_componente_info::get_utileria_editor_abrir_php(array('proyecto'=>$this->id_objeto['proyecto'],
																				'componente'=> $this->id_objeto['objeto']),
																			'ap');
				$form->set_js_abrir( $abrir['js'] );
			} else {
				$form->evento('ver_php')->set_imagen('nucleo/php_ap_inexistente.gif');
				$form->eliminar_evento('abrir_php');
			}
		} else {
			$form->eliminar_evento('ver_php');	
			$form->eliminar_evento('abrir_php');
		}
		$form->set_datos($this->get_entidad()->tabla("prop_basicas")->get());
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
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
	}

	//-- Generacion automatica de columnas!!
	
	function evt__columnas__leer_db()
	{
		$columnas = $this->obtener_definicion_columnas();
		$dbr = $this->get_entidad()->tabla("columnas");
		$actuales = $dbr->get_filas(null, true);
		for($a=0;$a<count($columnas);$a++){
			try{
				//--- Evita incluir dos veces el mismo nombre
				$nueva = true;
				foreach ($actuales as $id => $actual) {
					if ($columnas[$a]['columna'] == $actual['columna']) {
						$nueva = false;
					}
				}
				if ($nueva) {
					$dbr->nueva_fila($columnas[$a]);
				}
			}catch(toba_error $e){
				toba::notificacion()->agregar("Error agregando la COLUMNA '{$columnas[$a]['columna']}'. " . $e->getMessage());
			}
		}
	}

	function obtener_definicion_columnas()
	//Utilizo ADODB para recuperar los metadatos
	{
		//-[ 1 ]- Obtengo datos
		$tabla = $this->get_entidad()->tabla("prop_basicas")->get_fila_columna(0,"tabla");
		$reg = $this->get_entidad()->tabla("base")->get();
		$proyecto = $reg['fuente_datos_proyecto'];
		$id_fuente = $reg['fuente_datos'];
		$fuente = toba::db($id_fuente, toba_editor::get_proyecto_cargado());
		try{
			$columnas = $fuente->get_definicion_columnas($tabla);
			foreach(array_keys($columnas) as $id){
				$columnas[$id]['columna'] = $columnas[$id]['nombre'];	
				$columnas[$id]['largo'] = $columnas[$id]['longitud'];	
				$columnas[$id]['no_nulo_db'] = $columnas[$id]['not_null'];	
			}
			return $columnas;
		}catch(toba_error $e){
			toba::notificacion()->agregar( $e->getMessage() );
		}
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