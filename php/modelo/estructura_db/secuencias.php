<?php
/*
*	ATENCION: Esto no se esta generando automaticamente.
*/
class secuencias
{
	static function get_lista()
	{
		$datos['apex_sesion_browser_seq'] = array( 'campo' => 'sesion_browser', 'tabla' => 'apex_sesion_browser' );
		$datos['apex_solicitud_seq'] = array( 'campo' => 'solicitud', 'tabla' => 'apex_solicitud' );
		$datos['apex_solicitud_observacion_seq'] = array( 'campo' => 'solicitud_observacion', 'tabla' => 'apex_solicitud_observacion' );
		$datos['apex_solicitud_obj_obs_seq'] = array( 'campo' => 'solicitud_obj_observacion', 'tabla' => 'apex_solicitud_obj_observacion' );
		$datos['apex_clase_tipo_seq'] = array('campo' => 'clase_tipo', 'tabla'=> 'apex_clase_tipo');
		$datos['apex_columna_estilo_seq'] = array('campo' => 'columna_estilo', 'tabla'=> 'apex_columna_estilo');
		$datos['apex_columna_formato_seq'] = array('campo' => 'columna_formato', 'tabla'=> 'apex_columna_formato');
		$datos['apex_nota_seq'] = array('campo' => 'nota', 'tabla'=> 'apex_nota');
		$datos['apex_nucleo_nota_seq'] = array('campo' => 'nucleo_nota', 'tabla'=> 'apex_nucleo_nota');
		$datos['apex_item_nota_seq'] = array('campo' => 'item_nota', 'tabla'=> 'apex_item_nota');
		$datos['apex_objeto_nota_seq'] = array('campo' => 'objeto_nota', 'tabla'=> 'apex_objeto_nota');
		$datos['apex_clase_nota_seq'] = array('campo' => 'clase_nota', 'tabla'=> 'apex_clase_nota');
		$datos['apex_patron_nota_seq'] = array('campo' => 'patron_nota', 'tabla'=> 'apex_patron_nota');
		$datos['apex_buffer_seq'] = array('campo' => 'buffer', 'tabla'=> 'apex_buffer');
		$datos['apex_item_seq'] = array('campo' => 'item_id', 'tabla' => 'apex_item');
		$datos['apex_objeto_seq'] = array('campo' => 'objeto', 'tabla'=> 'apex_objeto');
		$datos['apex_log_sistema_seq'] = array('campo' => 'log_sistema', 'tabla'=> 'apex_log_sistema');
		$datos['apex_sesion_browser_seq'] = array('campo' => 'sesion_browser', 'tabla'=> 'apex_sesion_browser');
		$datos['apex_solicitud_seq'] = array('campo' => 'solicitud', 'tabla'=> 'apex_solicitud');
		$datos['apex_solicitud_observacion_seq'] = array('campo' => 'solicitud_observacion', 'tabla'=> 'apex_solicitud_observacion');
		$datos['apex_solicitud_obj_obs_seq'] = array('campo' => 'solicitud_obj_observacion', 'tabla'=> 'apex_solicitud_obj_observacion');
		$datos['apex_log_error_login_seq'] = array('campo' => 'log_error_login', 'tabla'=> 'apex_log_error_login');
		$datos['apex_ap_tarea_tipo_seq'] = array('campo' => 'tarea_tipo', 'tabla'=> 'apex_ap_tarea_tipo');
		$datos['apex_ap_tarea_estado_seq'] = array('campo' => 'tarea_estado', 'tabla'=> 'apex_ap_tarea_estado');
		$datos['apex_ap_tarea_tema_seq'] = array('campo' => 'tarea_tema', 'tabla'=> 'apex_ap_tarea_tema');
		$datos['apex_ap_tarea_seq'] = array('campo' => 'tarea', 'tabla'=> 'apex_ap_tarea');
		$datos['apex_objeto_plan_linea_seq'] = array('campo' => 'linea', 'tabla'=> 'apex_objeto_plan_linea');
		$datos['apex_patron_msg_seq'] = array('campo' => 'patron_msg', 'tabla'=> 'apex_patron_msg');
		$datos['apex_item_msg_seq'] = array('campo' => 'item_msg', 'tabla'=> 'apex_item_msg');
		$datos['apex_clase_msg_seq'] = array('campo' => 'clase_msg', 'tabla'=> 'apex_clase_msg');
		$datos['apex_objeto_msg_seq'] = array('campo' => 'objeto_msg', 'tabla'=> 'apex_objeto_msg');
		$datos['apex_msg_seq'] = array('campo' => 'msg', 'tabla'=> 'apex_msg');
		$datos['apex_objeto_dbr_columna_seq'] = array('campo' => 'col_id', 'tabla'=> 'apex_objeto_db_registros_col');
		$datos['apex_obj_ei_cuadro_col_seq'] = array('campo' => 'objeto_cuadro_col', 'tabla'=> 'apex_objeto_ei_cuadro_columna');	
		$datos['apex_obj_ci_pantalla_seq'] = array('campo' => 'pantalla', 'tabla'=> 'apex_objeto_ci_pantalla');
		$datos['apex_obj_ei_form_fila_seq'] = array('campo' => 'objeto_ei_formulario_fila', 'tabla'=> 'apex_objeto_ei_formulario_ef');
		$datos['apex_log_objeto_seq'] = array('campo' => 'log_objeto', 'tabla'=> 'apex_log_objeto');
		$datos['apex_obj_ei_cuadro_cc_seq'] = array('campo' => 'objeto_cuadro_cc', 'tabla'=> 'apex_objeto_cuadro_cc');	
		$datos['apex_objeto_dep_seq'] = array('campo' => 'dep_id', 'tabla'=> 'apex_objeto_dependencias');
		$datos['apex_objeto_eventos_seq'] = array('campo' => 'evento_id', 'tabla'=> 'apex_objeto_eventos');
		$datos['apex_objeto_datos_rel_asoc_seq'] = array('campo' => 'asoc_id', 'tabla'=> 'apex_objeto_datos_rel_asoc');	
		$datos['apex_admin_persistencia_seq'] = array('campo' => 'ap', 'tabla'=> 'apex_admin_persistencia');
		$datos['apex_columna_proceso_seq'] = array('campo' => 'columna_proceso', 'tabla'=> 'apex_columna_proceso');
		$datos['apex_elemento_infra_input_seq'] = array('campo' => 'entrada', 'tabla'=> 'apex_elemento_infra_input');
		$datos['apex_nucleo_tipo_seq'] = array('campo' => 'nucleo_tipo', 'tabla'=> 'apex_nucleo_tipo');
		$datos['apex_tp_tarea_seq'] = array('campo' => 'tarea', 'tabla'=> 'apex_tp_tarea');
		$datos['apex_tp_tarea_tipo_seq'] = array('campo' => 'tarea_tipo', 'tabla'=> 'apex_tp_tarea_tipo');
		$datos['apex_permiso_seq'] = array('campo' => 'permiso', 'tabla' => 'apex_permiso');
		return $datos;
	}
}
?>