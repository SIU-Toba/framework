<?php
/**
 * Controla un flujo de pantallas
 * @package Componentes
 * @subpackage Eis
 */
class toba_ci_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_mt_me',
								'registros' => '1',
								'obligatorio' => true );		
		$estructura[] = array( 	'tabla' => 'apex_objeto_dependencias',
								'registros' => 'n',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_objeto_ci_pantalla',
								'registros' => 'n',
								'obligatorio' => true,
								'diff_clave' => 'identificador',
								'diff_excluir' => array(
									'pantalla'
								));
		$estructura[] = array( 	'tabla' => 'apex_objetos_pantalla',
								'registros' => 'n',
								'obligatorio' => false );				
		$estructura[] = array( 	'tabla' => 'apex_eventos_pantalla',
								'registros' => 'n',
								'obligatorio' => false );				
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		
		$quote_proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$quote_componente = self::$db->quote($componente);
		}	
				
		//-- Info BASICA --------------
		$sql["_info_ci"]['sql'] = "		SELECT		ev_procesar_etiq		as	ev_procesar_etiq,
													ev_cancelar_etiq		as	ev_cancelar_etiq,
													objetos					as	objetos,
													ancho					as	ancho,			
													alto					as	alto,
													posicion_botonera		as  posicion_botonera,
													tipo_navegacion			as	tipo_navegacion,
													con_toc					as  con_toc,
													botonera_barra_item		as botonera_barra_item
											FROM	apex_objeto_mt_me
											WHERE	objeto_mt_me_proyecto=$quote_proyecto";
		if ( isset($componente) ) {
			$sql['_info_ci']['sql'] .= "	AND		objeto_mt_me=$quote_componente ";	
		}
		$sql['_info_ci']['sql'] .= " ORDER BY objeto_mt_me;";
		$sql['_info_ci']['registros']='1';
		$sql['_info_ci']['obligatorio']=true;
		//-- PANTALLAS --------------
		$sql["_info_ci_me_pantalla"]['sql'] = "SELECT	
													pantalla			as pantalla,	
													identificador			as identificador,
													etiqueta			  	as etiqueta,
													descripcion			  	as descripcion,
													tip						as tip,
													imagen_recurso_origen	as imagen_recurso_origen,
													imagen					as imagen,
													objetos				  	as objetos,
													eventos					as eventos,
													orden					as orden,
													punto_montaje			as punto_montaje,
													subclase				as subclase,
													subclase_archivo		as subclase_archivo,
													template				as template,
													template_impresion  as template_impresion

									 	FROM	apex_objeto_ci_pantalla
										WHERE	objeto_ci_proyecto=$quote_proyecto";
		if ( isset($componente) ) {
			$sql['_info_ci_me_pantalla']['sql'] .= "	AND		objeto_ci=$quote_componente ";	
		}
		$sql['_info_ci_me_pantalla']['sql'] .= "ORDER	BY	orden;";
		$sql['_info_ci_me_pantalla']['registros']='n';
		$sql['_info_ci_me_pantalla']['obligatorio']=true;
		//-- OBJETOS ASOCIADOS A LA PANTALLA -----------------
		$sql['_info_obj_pantalla']['sql'] = "SELECT
																					op.pantalla	as	pantalla,
																					op.proyecto	as	proyecto,
																					op.objeto_ci	as	objeto_ci,
																					op.dep_id		as	 dep_id,
																					op.orden		as	orden,
																					cp.identificador as identificador_pantalla,
																					od.identificador as identificador_dep
																	FROM	apex_objetos_pantalla op,
																					apex_objeto_ci_pantalla cp,
																					apex_objeto_dependencias od
																	WHERE	op.proyecto = $quote_proyecto
																	AND		op.proyecto = cp.objeto_ci_proyecto
																	AND		op.objeto_ci = cp.objeto_ci
																	AND		op.pantalla = cp.pantalla
																	AND		op.proyecto = od.proyecto
																	AND		op.objeto_ci = od.objeto_consumidor
																	AND		op.dep_id = od.dep_id";
		if ( isset($componente) ) {
			$sql['_info_obj_pantalla']['sql'] .= "	AND		op.objeto_ci=$quote_componente ";
		}
		$sql['_info_obj_pantalla']['sql'] .= "ORDER	BY	op.orden;";
		$sql['_info_obj_pantalla']['registros']='n';
		$sql['_info_obj_pantalla']['obligatorio']=false;
	//--EVENTOS ASOCIADOS A LA PANTALLA -------------------------
		$sql['_info_evt_pantalla']['sql'] = "SELECT		ep.pantalla as pantalla,
																						ep.proyecto as proyecto,
																						ep.objeto_ci as objeto_ci,
																						ep.evento_id as evento_id,
																						cp.identificador as identificador_pantalla,
																						oe.identificador as identificador_evento
																	FROM		 apex_eventos_pantalla ep,
																						apex_objeto_ci_pantalla cp,
																						apex_objeto_eventos oe
																	WHERE		ep.proyecto = $quote_proyecto
																	AND		ep.proyecto = cp.objeto_ci_proyecto
																	AND		ep.objeto_ci = cp.objeto_ci
																	AND		ep.pantalla = cp.pantalla
																	AND		ep.proyecto = oe.proyecto
																	AND		ep.evento_id = oe.evento_id
																	AND		ep.objeto_ci = oe.objeto";
		if ( isset($componente) ) {
			$sql['_info_evt_pantalla']['sql'] .= "	AND		ep.objeto_ci=$quote_componente ";
		}
		$sql['_info_evt_pantalla']['sql'] .= "ORDER	BY	cp.orden;";
		$sql['_info_evt_pantalla']['registros']='n';
		$sql['_info_evt_pantalla']['obligatorio']=false;

	//-- DEPENDENCIAS ------------
		$sql['_info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);
		return $sql;
	}

	static function get_vista_extendida_resumida($proyecto, $componente)
	{
		$estructura = self::get_vista_extendida($proyecto, $componente);
		unset($estructura['_info_ci']);
		unset($estructura['_info_eventos']);
		unset($estructura['_info_puntos_control']);
		return $estructura;
	}
}
?>