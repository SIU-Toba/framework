<?php
/**
 * item_nuevo	[item]
 *		item_nuevo	[ci]
 *			pant_inicial
 *			pant_segunda
 *
 */
class tester_caso_nuevoitem extends tester_caso
{
	protected $sql = array(
		// tabla: apex_item
		"INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (NULL, 'ejemplo', '12000037', NULL, 'ejemplo', '1', 0, 0, 'web', 'toba', 'normal', NULL, NULL, NULL, NULL, 'item_nuevo', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'apex', NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '2010-05-19 14:53:13', 0);",

		// tabla: apex_item_objeto
		"INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (NULL, 'ejemplo', '12000037', 12000047, 0, NULL);",

		// tabla: apex_objeto
		"INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES ('ejemplo', 12000047, NULL, NULL, NULL, 'toba', 'toba_ci', NULL, NULL, NULL, NULL, 'item_nuevo', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2010-05-19 14:53:49', 'abajo');",

		// tabla: apex_objeto_ci_pantalla
		"INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template) VALUES ('ejemplo', 12000047, 12000036, 'pant_inicial', 1, 'Pantalla Inicial', NULL, NULL, 'apex', NULL, NULL, NULL, NULL, NULL, NULL);",
		"INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template) VALUES ('ejemplo', 12000047, 12000037, 'pant_segunda', 2, 'Pantalla Dos', NULL, NULL, 'apex', 'ayuda.gif', NULL, NULL, NULL, NULL, NULL);",

		// tabla: apex_objeto_mt_me
		"INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES ('ejemplo', 12000047, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);",

		// tabla: apex_usuario_grupo_acc_item
		"INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES ('ejemplo', 'admin', NULL, '12000037');",
	);

	function  __construct($db)
	{
		parent::__construct($db);		
		// tabla: apex_log_objeto
		$this->sql[] = "INSERT INTO {$this->get_schema_log_toba()}.apex_log_objeto (log_objeto, momento, usuario, objeto_proyecto, objeto, item, observacion) VALUES (12000060, '2010-05-19 14:53:13', 'toba', 'ejemplo', NULL, '12000037', NULL);";
		$this->sql[] = "INSERT INTO {$this->get_schema_log_toba()}.apex_log_objeto (log_objeto, momento, usuario, objeto_proyecto, objeto, item, observacion) VALUES (12000061, '2010-05-19 14:53:49', 'toba', 'ejemplo', 12000047, NULL, NULL);";		
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