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
		$estructura[] = array( 	'tabla' => 'apex_objeto_dependencias',
								'registros' => 'n',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_objeto_mt_me',
								'registros' => '1',
								'obligatorio' => true );
		$estructura[] = array( 	'tabla' => 'apex_objeto_ci_pantalla',
								'registros' => 'n',
								'obligatorio' => true );
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
													con_toc					as  con_toc
											FROM	apex_objeto_mt_me
											WHERE	objeto_mt_me_proyecto=$quote_proyecto";
		if ( isset($componente) ) {
			$sql['_info_ci']['sql'] .= "	AND		objeto_mt_me=$quote_componente ";	
		}
		$sql['_info_ci']['sql'] .= ";";
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
													subclase				as subclase,
													subclase_archivo		as subclase_archivo
									 	FROM	apex_objeto_ci_pantalla
										WHERE	objeto_ci_proyecto=$quote_proyecto";
		if ( isset($componente) ) {
			$sql['_info_ci_me_pantalla']['sql'] .= "	AND		objeto_ci=$quote_componente ";	
		}
		$sql['_info_ci_me_pantalla']['sql'] .= "ORDER	BY	orden;";
		$sql['_info_ci_me_pantalla']['registros']='n';
		$sql['_info_ci_me_pantalla']['obligatorio']=true;
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