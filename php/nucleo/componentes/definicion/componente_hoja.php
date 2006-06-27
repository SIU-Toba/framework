<?
require_once("componente.php");

class componente_hoja extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_hoja';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_objeto_hoja_directiva';
		$estructura[3]['registros'] = '2';
		$estructura[3]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$consumo_web = toba::get_solicitud()->get_tipo() == 'web';
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//-- Hoja ---
		$sql["info_hoja"]["sql"] = "SELECT h.sql as			sql,
							h.total_y as					total_y,
							h.total_x as					total_x,
							cf.funcion as					total_x_formato,
							h.ordenable as					ordenable,
		                    h.columna_entrada as     	  	columna_entrada,
							h.ancho as						ancho,
							h.grafico as 					grafico,
							h.graf_columnas as				graf_columnas,
							h.graf_filas as					graf_filas,
							h.graf_gen_invertir as			graf_gen_invertir,
							h.graf_gen_invertible as		graf_gen_invertible,
							h.graf_gen_ancho as				graf_gen_ancho,
							h.graf_gen_alto as				graf_gen_alto
					FROM	apex_objeto_hoja h
							LEFT OUTER JOIN apex_columna_formato cf 
								ON h.total_x_formato = cf.columna_formato
					WHERE	objeto_hoja_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_hoja"]["sql"] .= " AND	objeto_hoja='$componente' ";
		}
		$sql["info_hoja"]["sql"] .= " ;";
		$sql["info_hoja"]['registros']='1';
		$sql["info_hoja"]['obligatorio']='1';
		//-- Directivas ---
		$sql["info_hoja_dir"]["sql"] = "SELECT	d.objeto_hoja_directiva_tipo as tipo,
							d.nombre as 						nombre,
							cf.funcion as 						formato,
							ce.css as 							estilo,
							dim.dimension as					dimension,
							d.par_tabla as						dimension_tabla,
							d.par_columna as					dimension_columna ";
		if ( $consumo_web ) {
			$sql["info_hoja_dir"]["sql"] .= ", u.usuario_perfil_datos as dimension_control_perfil ";
		}
		$sql["info_hoja_dir"]["sql"] .= "	FROM	apex_objeto_hoja_directiva d 
							LEFT OUTER JOIN apex_columna_formato cf USING(columna_formato)
							LEFT OUTER JOIN apex_columna_estilo ce USING(columna_estilo)
							LEFT OUTER JOIN apex_dimension dim ON (d.par_dimension = dim.dimension)";
		if ( $consumo_web ) {
			// Filtrar dimensiones por perfil
			$perfil = toba::get_hilo()->obtener_usuario_perfil_datos();
			$sql["info_hoja_dir"]["sql"] .= " LEFT OUTER JOIN apex_dimension_perfil_datos u 
													ON (d.par_dimension = u.dimension) 
													AND (u.usuario_perfil_datos = '$perfil') ";
		}							
		$sql["info_hoja_dir"]["sql"] .= " WHERE	d.objeto_hoja_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_hoja_dir"]["sql"] .= " AND	d.objeto_hoja='$componente' ";
		}
    	$sql["info_hoja_dir"]["sql"] .=	" ORDER BY	d.columna;";
		$sql["info_hoja_dir"]["registros"]='n';
		$sql["info_hoja_dir"]['obligatorio']=true;
		return $sql;
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/transversales';
	}
}
?>