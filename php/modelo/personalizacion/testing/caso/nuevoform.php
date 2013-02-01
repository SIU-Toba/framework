<?php
/**
 * item_original
 *     ci_item_original
 *         pant_inicial
 *               + form <- formulario con eventos básicos y una fila
 *     ci2_item_original
 *         pant_inicial
 *
 */
class tester_caso_nuevoform extends tester_caso
{
	protected $sql = array(

		// tabla: apex_objeto
		"INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES ('ejemplo', 12000059, NULL, NULL, NULL, 'toba', 'toba_ei_formulario', NULL, NULL, NULL, NULL, 'ci_item_original - form', NULL, 0, NULL, 'ejemplo', 'ejemplo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2010-05-19 15:47:44', 'abajo');",

		// tabla: apex_objeto_dependencias
		"INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES ('ejemplo', 12000051, 12000008, 12000059, 'form', NULL, NULL, NULL, NULL, NULL);",

		// tabla: apex_objeto_ei_formulario_ef
		"INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_fila, objeto_ei_formulario, objeto_ei_formulario_proyecto, identificador, elemento_formulario, columnas, obligatorio, oculto_relaja_obligatorio, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total, inicializacion, permitir_html, deshabilitar_rest_func, estado_defecto, solo_lectura, solo_lectura_inteligente, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_sql, carga_fuente, carga_lista, carga_col_clave, carga_col_desc, carga_maestros, carga_cascada_relaj, cascada_mantiene_estado, carga_permite_no_seteado, carga_no_seteado, carga_no_seteado_ocultar, edit_tamano, edit_maximo, edit_mascara, edit_unidad, edit_rango, edit_filas, edit_columnas, edit_wrap, edit_resaltar, edit_ajustable, edit_confirmar_clave, edit_expreg, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include, popup_puede_borrar_estado, fieldset_fin, check_valor_si, check_valor_no, check_desc_si, check_desc_no, check_ml_toggle, fijo_sin_estado, editor_ancho, editor_alto, editor_botonera, selec_cant_minima, selec_cant_maxima, selec_utilidades, selec_tamano, selec_ancho, selec_serializar, selec_cant_columnas, upload_extensiones) VALUES (12000052, 12000059, 'ejemplo', 'id', 'ef_editable', 'id', 0, 0, 1, 'Id', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);",

		// tabla: apex_objeto_eventos
		"INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES ('ejemplo', 12000056, 12000059, 'modificacion', '&Modificar', 1, NULL, NULL, NULL, 'apex', NULL, 0, NULL, 1, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);",

		// tabla: apex_objeto_ut_formulario
		"INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, expandir_descripcion, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_agregar_abajo, filas_agregar_texto, filas_borrar_en_linea, filas_undo, filas_ordenar, filas_ordenar_en_linea, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios, no_imprimir_efs_sin_estado, resaltar_efs_con_estado, template) VALUES ('ejemplo', 12000059, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', 0, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL);",

		// tabla: apex_objetos_pantalla
		"INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES ('ejemplo', 12000004, 12000008, NULL, 12000051);",
	);


	function  __construct($db)
	{
		parent::__construct($db);		
		// tabla: apex_log_objeto
		$this->sql[] = "INSERT INTO {$this->get_schema_log_toba()}.apex_log_objeto (log_objeto, momento, usuario, objeto_proyecto, objeto, item, observacion) VALUES (12000072, '2010-05-19 15:47:44', 'toba', 'ejemplo', 12000059, NULL, NULL);";
	}		

	/**
	 * Descripción del caso de test
	 */
	function get_descripcion()
	{
		return '';
	}

}
?>