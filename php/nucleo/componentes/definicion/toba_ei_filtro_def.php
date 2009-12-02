<?php

class toba_ei_filtro_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_ei_filtro',
								'registros' => '1',
								'obligatorio' => true );
		$estructura[] = array( 	'tabla' => 'apex_objeto_ei_filtro_col',
								'registros' => 'n',
								'obligatorio' => true );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}		
				
		//Filtro
		$sql["_info_filtro"]['sql'] = "SELECT
												ancho 						as ancho
								FROM	apex_objeto_ei_filtro
								WHERE	objeto_ei_filtro_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_filtro']['sql'] .= "	AND		objeto_ei_filtro=$componente ";	
		}
		$sql['_info_filtro']['sql'] .= " ORDER BY objeto_ei_filtro;";
		$sql['_info_filtro']['registros']='1';
		$sql['_info_filtro']['obligatorio']=true;
		
		//Columnas
		$sql["_info_filtro_col"]['sql'] = "SELECT	
										col.*,
										con.clase as carga_consulta_php_clase,
										con.archivo as carga_consulta_php_archivo
								FROM	apex_objeto_ei_filtro_col col
											LEFT OUTER JOIN apex_consulta_php con ON
												(col.objeto_ei_filtro_proyecto = con.proyecto AND
													col.carga_consulta_php = con.consulta_php) 
								WHERE	col.objeto_ei_filtro_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_filtro_col']['sql'] .= "	AND		col.objeto_ei_filtro=$componente ";	
		}
		$sql['_info_filtro_col']['sql'] .= "ORDER	BY	orden;";
		$sql['_info_filtro_col']['registros']='n';
		$sql['_info_filtro_col']['obligatorio']=true;
		return $sql;
	}
}
?>