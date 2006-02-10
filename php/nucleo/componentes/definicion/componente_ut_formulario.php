<?
require_once("componente.php");

class componente_ut_formulario extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_ut_formulario';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_objeto_ut_formulario_ef';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = false;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//-- Formulario ----------------------
		$sql["info_formulario"]["sql"] = "SELECT				tabla	as	tabla,
										titulo as						titulo,
										ev_mod_eliminar as			ev_mod_eliminar,
										ev_mod_clave as				ev_mod_clave,
										ev_mod_limpiar	as				ev_mod_limpiar,
										auto_reset as					auto_reset,						
										campo_bl	as						campo_bl,
										ancho as							ancho
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto= '$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_formulario"]["sql"] .= " AND	objeto_ut_formulario= '$componente' ";
		}
		$sql["info_formulario"]["sql"] .= " ;";
		$sql["info_formulario"]["registros"]='1';
		$sql["info_formulario"]['obligatorio']=true;
		//-- Formulario EF --------------
		$sql["info_formulario_ef"]["sql"] = "SELECT	identificador as identificador,
										columnas	as						columnas,
										obligatorio	as					obligatorio,
										elemento_formulario as		elemento_formulario,
										inicializacion	as				inicializacion,
										etiqueta	as						etiqueta,
										descripcion	as					descripcion,
										clave_primaria	as				clave_primaria,
										orden	as							orden,
										-- Exclusivos del ML
										clave_primaria_padre as		clave_primaria_padre,
										listar as						listar,
										lista_cabecera as				lista_cabecera,
										lista_valor_sql as			lista_valor_sql,
										lista_orden as					lista_orden,
										colapsado as 					colapsado,
										no_sql as						no_sql
								FROM	apex_objeto_ut_formulario_ef
								WHERE	objeto_ut_formulario_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_formulario_ef"]["sql"] .= " AND	objeto_ut_formulario='$componente' ";
		}
		$sql["info_formulario_ef"]["sql"] .= " AND (desactivado=0 OR desactivado IS NULL)
											ORDER	BY	orden;";
		$sql["info_formulario_ef"]["registros"]='n';
		$sql["info_formulario_ef"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/runtime/transversales';
	}
}
?>