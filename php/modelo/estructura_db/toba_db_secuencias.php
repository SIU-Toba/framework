<?php
/*
*	ATENCION: Esto no se esta generando automaticamente.
*/
class toba_db_secuencias
{
	static function get_lista()
	{
		$datos['apex_clase_tipo_seq'] = array('campo' => 'clase_tipo', 'tabla'=> 'apex_clase_tipo');
		$datos['apex_columna_estilo_seq'] = array('campo' => 'columna_estilo', 'tabla'=> 'apex_columna_estilo');
		$datos['apex_columna_formato_seq'] = array('campo' => 'columna_formato', 'tabla'=> 'apex_columna_formato');
		$datos['apex_nota_seq'] = array('campo' => 'nota', 'tabla'=> 'apex_nota');
		$datos['apex_item_nota_seq'] = array('campo' => 'item_nota', 'tabla'=> 'apex_item_nota');
		$datos['apex_objeto_nota_seq'] = array('campo' => 'objeto_nota', 'tabla'=> 'apex_objeto_nota');
		$datos['apex_item_seq'] = array('campo' => 'item', 'tabla' => 'apex_item');
		$datos['apex_objeto_seq'] = array('campo' => 'objeto', 'tabla'=> 'apex_objeto');
		$datos['apex_clase_relacion_seq'] = array('campo' => 'clase_relacion', 'tabla'=> 'apex_clase_relacion');
		$datos['apex_item_msg_seq'] = array('campo' => 'item_msg', 'tabla'=> 'apex_item_msg');
		$datos['apex_objeto_msg_seq'] = array('campo' => 'objeto_msg', 'tabla'=> 'apex_objeto_msg');
		$datos['apex_msg_seq'] = array('campo' => 'msg', 'tabla'=> 'apex_msg');
		$datos['apex_objeto_dbr_columna_seq'] = array('campo' => 'col_id', 'tabla'=> 'apex_objeto_db_registros_col');
		$datos['apex_objeto_dbr_ext_seq'] = array('campo' => 'externa_id', 'tabla' => 'apex_objeto_db_registros_ext');		
		$datos['apex_objeto_dbr_uniq_seq'] = array('campo' => 'uniq_id', 'tabla' => 'apex_objeto_db_registros_uniq');		
		$datos['apex_obj_ei_cuadro_col_seq'] = array('campo' => 'objeto_cuadro_col', 'tabla'=> 'apex_objeto_ei_cuadro_columna');	
		$datos['apex_obj_ci_pantalla_seq'] = array('campo' => 'pantalla', 'tabla'=> 'apex_objeto_ci_pantalla');
		$datos['apex_obj_ei_form_fila_seq'] = array('campo' => 'objeto_ei_formulario_fila', 'tabla'=> 'apex_objeto_ei_formulario_ef');
		$datos['apex_obj_ei_cuadro_cc_seq'] = array('campo' => 'objeto_cuadro_cc', 'tabla'=> 'apex_objeto_cuadro_cc');	
		$datos['apex_objeto_dep_seq'] = array('campo' => 'dep_id', 'tabla'=> 'apex_objeto_dependencias');
		$datos['apex_objeto_eventos_seq'] = array('campo' => 'evento_id', 'tabla'=> 'apex_objeto_eventos');
		$datos['apex_objeto_datos_rel_asoc_seq'] = array('campo' => 'asoc_id', 'tabla'=> 'apex_objeto_datos_rel_asoc');	
		$datos['apex_admin_persistencia_seq'] = array('campo' => 'ap', 'tabla'=> 'apex_admin_persistencia');
		$datos['apex_permiso_seq'] = array('campo' => 'permiso', 'tabla' => 'apex_permiso');
		$datos['apex_molde_operacion_tipo_seq'] = array('campo' => 'operacion_tipo', 'tabla' => 'apex_molde_operacion_tipo');
		$datos['apex_molde_operacion_tipo_dato_seq'] = array('campo' => 'tipo_dato', 'tabla' => 'apex_molde_operacion_tipo_dato');
		$datos['apex_molde_operacion_seq'] = array('campo' => 'molde', 'tabla' => 'apex_molde_operacion');
		$datos['apex_molde_operacion_abms_fila_seq'] = array('campo' => 'fila', 'tabla' => 'apex_molde_operacion_abms_fila');
		$datos['apex_molde_operacion_log_seq'] = array('campo' => 'generacion', 'tabla' => 'apex_molde_operacion_log');
		$datos['apex_molde_operacion_log_elementos_seq'] = array('campo' => 'id', 'tabla' => 'apex_molde_operacion_log_elementos');
		$datos['apex_consulta_php_seq'] = array('campo' => 'consulta_php', 'tabla' => 'apex_consulta_php');
		$datos['apex_relacion_tablas_seq'] = array('campo' => 'relacion_tablas', 'tabla' => 'apex_relacion_tablas');
		$datos['apex_dimension_seq'] = array('campo' => 'dimension', 'tabla' => 'apex_dimension');
		$datos['apex_dimension_gatillo_seq'] = array('campo' => 'gatillo', 'tabla' => 'apex_dimension_gatillo');
		$datos['apex_usuario_perfil_datos_seq'] = array('campo' => 'usuario_perfil_datos', 'tabla' => 'apex_usuario_perfil_datos');
		$datos['apex_usuario_perfil_datos_dims_seq'] = array('campo' => 'elemento', 'tabla' => 'apex_usuario_perfil_datos_dims');
		$datos['apex_restriccion_funcional_seq'] = array('campo' => 'restriccion_funcional', 'tabla' => 'apex_restriccion_funcional');
		$datos['apex_objeto_ei_filtro_col_seq'] = array('campo' => 'objeto_ei_filtro_col', 'tabla' => 'apex_objeto_ei_filtro_col');
		$datos['apex_tarea_seq'] = array('campo' => 'tarea', 'tabla' => 'apex_tarea');
		$datos['apex_objeto_dep_consumo_seq'] = array('campo' => 'consumo_id', 'tabla'=> 'apex_objeto_dep_consumo');
		$datos['apex_gadgets_seq'] = array('campo' => 'gadget', 'tabla'=> 'apex_gadgets');
		$datos['apex_puntos_montaje_seq'] = array('campo' => 'id', 'tabla'=> 'apex_puntos_montaje');
		$datos['apex_objeto_db_columna_fks_seq'] = array('campo' => 'id', 'tabla'=> 'apex_objeto_db_columna_fks');
		return $datos;
	}

	static function get_lista_secuencias_tablas_log()
	{		
		$datos['apex_sesion_browser_seq'] = array('campo' => 'sesion_browser', 'tabla'=> 'apex_sesion_browser');		
		$datos['apex_solicitud_seq'] = array( 'campo' => 'solicitud', 'tabla' => 'apex_solicitud' );
		$datos['apex_solicitud_observacion_seq'] = array('campo' => 'solicitud_observacion', 'tabla'=> 'apex_solicitud_observacion');
		$datos['apex_log_error_login_seq'] = array('campo' => 'log_error_login', 'tabla'=> 'apex_log_error_login');
		$datos['apex_log_sistema_seq'] = array('campo' => 'log_sistema', 'tabla'=> 'apex_log_sistema');				
		$datos['apex_log_tarea_seq'] = array('campo' => 'log_tarea', 'tabla' => 'apex_log_tarea');	
		$datos['apex_log_objeto_seq'] = array('campo' => 'log_objeto', 'tabla'=> 'apex_log_objeto');		
		return $datos;
	}
}
?>
