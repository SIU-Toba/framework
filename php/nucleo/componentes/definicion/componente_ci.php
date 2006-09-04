<?php
require_once("componente_ei.php");
/**
 * Controla un flujo de pantallas
 * @package Componentes
 * @subpackage Eis
 */
class componente_ci extends componente_ei
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[3]['tabla'] = 'apex_objeto_dependencias';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = false;
		$estructura[4]['tabla'] = 'apex_objeto_mt_me';
		$estructura[4]['registros'] = '1';
		$estructura[4]['obligatorio'] = true;
		$estructura[5]['tabla'] = 'apex_objeto_ci_pantalla';
		$estructura[5]['registros'] = 'n';
		$estructura[5]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//-- Info BASICA --------------
		$sql["info_ci"]['sql'] = "		SELECT		ev_procesar_etiq		as	ev_procesar_etiq,
													ev_cancelar_etiq		as	ev_cancelar_etiq,
													objetos					as	objetos,
													ancho					as	ancho,			
													alto					as	alto,
													posicion_botonera		as  posicion_botonera,
													tipo_navegacion			as	tipo_navegacion,
													con_toc					as  con_toc
											FROM	apex_objeto_mt_me
											WHERE	objeto_mt_me_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_ci']['sql'] .= "	AND		objeto_mt_me='$componente' ";	
		}
		$sql['info_ci']['sql'] .= ";";
		$sql['info_ci']['registros']='1';
		$sql['info_ci']['obligatorio']=true;
		//-- PANTALLAS --------------
		$sql["info_ci_me_pantalla"]['sql'] = "SELECT	
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
										WHERE	objeto_ci_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_ci_me_pantalla']['sql'] .= "	AND		objeto_ci='$componente' ";	
		}
		$sql['info_ci_me_pantalla']['sql'] .= "ORDER	BY	orden;";
		$sql['info_ci_me_pantalla']['registros']='n';
		$sql['info_ci_me_pantalla']['obligatorio']=true;
		//-- DEPENDENCIAS ------------
		$sql['info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_ci';
	}

	static function get_tipo_abreviado()
	{
		return "CI";		
	}
}
?>