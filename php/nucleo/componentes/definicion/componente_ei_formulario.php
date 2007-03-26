<?
require_once("componente_ei.php");

class componente_ei_formulario extends componente_ei
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[4]['tabla'] = 'apex_objeto_ut_formulario';
		$estructura[4]['registros'] = '1';
		$estructura[4]['obligatorio'] = false;
		$estructura[5]['tabla'] = 'apex_objeto_ei_formulario_ef';
		$estructura[5]['registros'] = 'n';
		$estructura[5]['obligatorio'] = false;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//Formulario
		$sql["info_formulario"]['sql'] = "SELECT	auto_reset as	auto_reset,						
										ancho 						as ancho,
										ancho_etiqueta				as ancho_etiqueta
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_formulario']['sql'] .= "	AND		objeto_ut_formulario='$componente' ";	
		}
		$sql['info_formulario']['sql'] .= ";";
		$sql['info_formulario']['registros']='1';
		$sql['info_formulario']['obligatorio']=true;
		//EF
		$sql["info_formulario_ef"]['sql'] = "SELECT	*
								FROM	apex_objeto_ei_formulario_ef
								WHERE	objeto_ei_formulario_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_formulario_ef']['sql'] .= "	AND		objeto_ei_formulario='$componente' ";	
		}
		$sql['info_formulario_ef']['sql'] .= " AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql['info_formulario_ef']['registros']='n';
		$sql['info_formulario_ef']['obligatorio']=true;
		return $sql;
	}

	static function get_nombre_clase_info()
	{
		return 'info_ei_formulario';
	}
	
	static function get_tipo_abreviado()
	{
		return "Form.";		
	}
}
?>