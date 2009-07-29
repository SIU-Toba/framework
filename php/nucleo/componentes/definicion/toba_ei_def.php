<?php

class toba_ei_def extends toba_componente_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_eventos',
								'registros' => 'n',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_ptos_control_x_evento',
								'registros' => 'n',
								'obligatorio' => false );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}				
		$sql['_info_eventos']['sql'] = "SELECT	evento_id				as evento_id,
												identificador			as identificador,
												etiqueta				as etiqueta,
												maneja_datos			as maneja_datos,
												sobre_fila				as sobre_fila,
												confirmacion			as confirmacion,
												estilo					as estilo,
												imagen_recurso_origen	as imagen_recurso_origen,
												imagen					as imagen,
												en_botonera				as en_botonera,
												ayuda					as ayuda,
												ci_predep				as ci_predep,				
												implicito				as implicito,	
												defecto					as defecto,				
												grupo					as grupo,
												accion					as accion,
												accion_imphtml_debug	as accion_imphtml_debug,
												accion_vinculo_carpeta		,
												accion_vinculo_item			,
												accion_vinculo_objeto		,
												accion_vinculo_popup		,
												accion_vinculo_popup_param	,
												accion_vinculo_celda		,
												accion_vinculo_target		,
												accion_vinculo_servicio   ,
												es_seleccion_multiple  ,
												es_autovinculo
									FROM	apex_objeto_eventos
									WHERE	proyecto=$proyecto ";
		if ( isset($componente) ) {
			$sql['_info_eventos']['sql'] .= "	AND		objeto=$componente ";	
		}
		$sql['_info_eventos']['sql'] .= " ORDER BY orden;";
		$sql['_info_eventos']['registros']='n';
		$sql['_info_eventos']['obligatorio']=false;
		// Puntos de control
	    $sql['_info_puntos_control']['sql'] = "SELECT pe.pto_control, 
                                            oe.identificador as evento
                                       FROM apex_ptos_control_x_evento pe,
                                            apex_objeto_eventos oe
                                      WHERE pe.proyecto = oe.proyecto
                                        AND pe.evento_id = oe.evento_id
                                        AND pe.proyecto = $proyecto";
		if ( isset($componente) ) {
			$sql['_info_puntos_control']['sql'] .= "	AND		oe.objeto=$componente ";
		}
	    $sql['_info_puntos_control']['sql'] .= " ORDER BY pto_control;";
	    $sql['_info_puntos_control']['registros']='n';
	    $sql['_info_puntos_control']['obligatorio']=false;
		return $sql;
	}
}
?>