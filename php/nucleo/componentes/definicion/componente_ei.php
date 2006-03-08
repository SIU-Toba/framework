<?
require_once("componente.php");

class componente_ei extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_eventos';
		$estructura[2]['registros'] = 'n';
		$estructura[2]['obligatorio'] = false;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		$sql["info_eventos"]['sql'] = "SELECT	identificador			as identificador,
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
												grupo					as grupo,
												accion					as accion,
												accion_imphtml_debug	as accion_imphtml_debug
									FROM	apex_objeto_eventos
									WHERE	proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_eventos"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_eventos"]['sql'] .= " ORDER BY orden;";
		$sql["info_eventos"]['registros']='n';
		$sql["info_eventos"]['obligatorio']=false;
		return $sql;
	}

	static function get_nombre_clase_info()
	{
		return 'info_ei';
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/runtime/interface';
	}
}
?>