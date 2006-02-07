<?
require_once("componente.php");

class componente_ut_formulario extends componente_toba
{

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
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
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND		objeto_ut_formulario='".$this->id[1]."';";
		$sql["info_formulario"]["tipo"]="1";
		$sql["info_formulario"]["estricto"]="1";
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
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND	objeto_ut_formulario='".$this->id[1]."'
								AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql["info_formulario_ef"]["tipo"]="x";
		$sql["info_formulario_ef"]["estricto"]="1";
		return $sql;
	}
}
?>