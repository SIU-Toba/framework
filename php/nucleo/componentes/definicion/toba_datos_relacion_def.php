<?php

class toba_datos_relacion_def extends toba_componente_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_datos_rel',
								'registros' => '1',
								'obligatorio' => true );
		$estructura[] = array( 	'tabla' => 'apex_objeto_dependencias',
								'registros' => 'n',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_objeto_datos_rel_asoc',
								'registros' => 'n',
								'obligatorio' => true );		
		$estructura[] = array( 	'tabla' => 'apex_objeto_rel_columnas_asoc',
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
				
		//------------- Info base de la estructura ----------------
		$sql['_info_estructura']['sql'] = "SELECT	proyecto 	,	
													objeto      ,	
													debug		,	
													ap			,
													punto_montaje,
													ap_clase	,	
													ap_archivo	,
													sinc_susp_constraints,
													sinc_orden_automatico,
													sinc_lock_optimista
										 FROM		apex_objeto_datos_rel
										 WHERE		proyecto=$quote_proyecto ";	
		if ( isset($componente) ) {
			$sql['_info_estructura']['sql'] .= "	AND		objeto=$quote_componente ";	
		}
		$sql['_info_estructura']['sql'] .= " ORDER BY objeto;";
		$sql['_info_estructura']['registros']='1';
		$sql['_info_estructura']['obligatorio']=true;
		//------------ relaciones ----------------
		$sql['_info_relaciones']['sql'] = "SELECT	proyecto 		,
												objeto 		    ,
												asoc_id			,
											--	identificador   ,
												padre_proyecto	,
												padre_objeto	,
												padre_id		,
											---	padre_clave		,
												hijo_proyecto	,
												hijo_objeto		,
												hijo_id			,
										---		hijo_clave		,
												cascada			,
												orden			
									 FROM		apex_objeto_datos_rel_asoc 
									 WHERE		proyecto = $quote_proyecto ";
		if ( isset($componente) ) {
			$sql['_info_relaciones']['sql'] .= "	AND		objeto=$quote_componente ";	
		}
		$sql['_info_relaciones']['sql'] .= " ORDER BY objeto, asoc_id;";
		$sql['_info_relaciones']['registros']='n';
		$sql['_info_relaciones']['obligatorio']=false;
		$sql['_info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);

		//------------- Tabla que mantenie las columnas que forman parte de la relacion-----------------
		$sql['_info_columnas_asoc_rel']['sql'] = "SELECT  rca.asoc_id,
																									rca.proyecto,
																									rca.objeto,
																									rca.hijo_clave,
																									rca.hijo_objeto,
																									hijo.columna as col_hija,
																									rca.padre_objeto,
																									rca.padre_clave,
																									padre.columna as col_padre

																				FROM		apex_objeto_rel_columnas_asoc as rca,																									
																									apex_objeto_datos_rel_asoc as dra,
																									apex_objeto_db_registros_col as padre,
																									apex_objeto_db_registros_col as hijo
																				WHERE
																									rca.proyecto = $quote_proyecto
																				AND			  rca.proyecto = dra.proyecto
																				AND			  rca.objeto = dra.objeto
																				AND			  rca.asoc_id = dra.asoc_id

																				AND			  rca.proyecto = padre.objeto_proyecto
																				AND			  rca.padre_objeto = padre.objeto
																				AND			  rca.padre_clave = padre.col_id

																				AND			  rca.proyecto = hijo.objeto_proyecto
																				AND			  rca.hijo_objeto = hijo.objeto
																				AND			  rca.hijo_clave = hijo.col_id
																	";
		if ( isset($componente) ) {
			$sql['_info_columnas_asoc_rel']['sql'] .= "	AND		rca.objeto=$quote_componente ";
		}
		$sql['_info_columnas_asoc_rel']['sql'] .= " ORDER BY rca.asoc_id, rca.padre_objeto, rca.hijo_objeto, rca.padre_clave, rca.hijo_clave;";
		$sql['_info_columnas_asoc_rel']['registros']='n';
		$sql['_info_columnas_asoc_rel']['obligatorio']=false;
		return $sql;
	}

	static function get_vista_extendida_resumida($proyecto, $componente)
	{
		$estructura = self::get_vista_extendida($proyecto, $componente);
		unset($estructura['_info_relaciones']);
		return $estructura;
	}
}
?>