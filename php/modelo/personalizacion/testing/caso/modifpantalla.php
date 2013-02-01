<?php
class tester_caso_modifpantalla extends tester_caso
{
	protected $sql = array(
		// tabla: apex_eventos_pantalla
		"INSERT INTO apex_eventos_pantalla (pantalla, objeto_ci, evento_id, proyecto) VALUES (12000004, 12000008, 12000059, 'ejemplo');",
		"INSERT INTO apex_eventos_pantalla (pantalla, objeto_ci, evento_id, proyecto) VALUES (12000004, 12000008, 12000060, 'ejemplo');",


		// tabla: apex_objeto_ci_pantalla
		"UPDATE apex_objeto_ci_pantalla SET orden = 1, etiqueta = 'Pantalla Uno', imagen_recurso_origen = 'apex' WHERE objeto_ci_proyecto = 'ejemplo' AND objeto_ci = 12000008 AND pantalla = 12000004 AND identificador = 'pant_inicial' AND etiqueta = 'Pantalla Inicial';",

		// tabla: apex_objeto_eventos
		"INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES ('ejemplo', 12000059, 12000008, 'procesar', '&Guardar', 1, NULL, NULL, NULL, 'apex', 'guardar.gif', 1, NULL, 1, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);",
		"INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES ('ejemplo', 12000060, 12000008, 'cancelar', '&Cancelar', 0, NULL, NULL, NULL, 'apex', NULL, 1, NULL, 2, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);",
	);

	function  __construct($db)
	{
		parent::__construct($db);		
		// tabla: apex_log_objeto
		$this->sql[] = "INSERT INTO {$this->get_schema_log_toba()}.apex_log_objeto (log_objeto, momento, usuario, objeto_proyecto, objeto, item, observacion) VALUES (12000075, '2010-05-19 16:00:29', 'toba', 'ejemplo', 12000008, NULL, NULL);";
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