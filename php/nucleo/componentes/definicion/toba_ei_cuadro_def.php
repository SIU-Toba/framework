<?php

class toba_ei_cuadro_def extends toba_ei_def
{
 	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_cuadro',
								'registros' => '1',
								'obligatorio' => true );
		$estructura[] = array( 	'tabla' => 'apex_objeto_cuadro_cc',
								'registros' => 'n',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_objeto_ei_cuadro_columna',
								'registros' => 'n',
								'obligatorio' => false );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}		
				
		//------------- Cuadro ----------------
		$sql['_info_cuadro']['sql'] = "SELECT	titulo as titulo,		
										c.subtitulo						as	subtitulo,		
										c.sql							as	sql,			
										c.columnas_clave				as	columnas_clave,
										c.clave_dbr						as	clave_datos_tabla,
										c.archivos_callbacks			as	archivos_callbacks,		
										c.ancho							as	ancho,			
										c.ordenar						as	ordenar,			
										c.exportar_paginado				as	exportar_paginado,
										c.exportar						as	exportar_xls,	 
										c.exportar_rtf					as	exportar_pdf,		 
										c.paginar						as	paginar,			
										c.tamano_pagina					as	tamano_pagina,
										c.tipo_paginado					as	tipo_paginado,
										c.scroll						as	scroll,
										c.scroll_alto					as	alto,
										c.eof_invisible					as	eof_invisible,		 
										c.eof_customizado				as	eof_customizado,
										c.pdf_respetar_paginacion		as	pdf_respetar_paginacion,	
										c.pdf_propiedades				as	pdf_propiedades,
										c.asociacion_columnas			as	asociacion_columnas,
										c.dao_nucleo_proyecto			as  dao_nucleo_proyecto,	
										c.dao_nucleo					as  dao_clase,			
										c.dao_metodo					as  dao_metodo,
										c.dao_parametros				as  dao_parametros,
										''		 						as	dao_archivo,
										c.cc_modo						as	cc_modo,						
										c.cc_modo_anidado_colap			as	cc_modo_anidado_colap,		
										c.cc_modo_anidado_totcol		as	cc_modo_anidado_totcol,		
										c.cc_modo_anidado_totcua		as	cc_modo_anidado_totcua		
							 FROM		apex_objeto_cuadro c
							 WHERE	objeto_cuadro_proyecto=$proyecto ";	
		if ( isset($componente) ) {
			$sql['_info_cuadro']['sql'] .= "	AND		objeto_cuadro=$componente ";	
		}
		$sql['_info_cuadro']['sql'] .= ";";
		$sql['_info_cuadro']['registros']='1';
		$sql['_info_cuadro']['obligatorio']=true;
		//------------ Columnas ----------------
		$sql['_info_cuadro_columna']['sql'] = "SELECT	c.orden	as orden,	
												c.objeto_cuadro_col				as objeto_cuadro_col,
												c.titulo						as titulo,
												c.estilo_titulo					as estilo_titulo,		
												e.css							as estilo,	 
												c.ancho							as ancho,	 
												c.clave							as clave,		
												f.funcion						as formateo,	 
												c.no_ordenar					as no_ordenar,
												c.mostrar_xls					as mostrar_xls,
												c.mostrar_pdf					as mostrar_pdf,
												c.pdf_propiedades				as pdf_propiedades,
												c.total							as total,
												c.vinculo_indice				as vinculo_indice,	
												c.usar_vinculo					as usar_vinculo		,
												c.vinculo_carpeta				as vinculo_carpeta		,
												c.vinculo_item					as vinculo_item		,
												c.total_cc						as total_cc,
												c.vinculo_target				as vinculo_target		,
												c.vinculo_celda					as vinculo_celda	,
												c.vinculo_popup					as vinculo_popup		,
												c.vinculo_popup_param			as vinculo_popup_param,
												c.permitir_html					as permitir_html
									 FROM		apex_columna_estilo e,
												apex_objeto_ei_cuadro_columna	c
												LEFT OUTER JOIN apex_columna_formato f	
												ON	f.columna_formato	= c.formateo
									 WHERE	objeto_cuadro_proyecto = $proyecto ";
		if ( isset($componente) ) {
			$sql['_info_cuadro_columna']['sql'] .= "	AND		objeto_cuadro=$componente ";
		}
		$sql['_info_cuadro_columna']['sql'] .= "	AND		c.estilo = e.columna_estilo	
					 AND		( c.desabilitado != '1' OR c.desabilitado IS NULL )
					 ORDER BY orden;";
		$sql['_info_cuadro_columna']['registros']='n';
		$sql['_info_cuadro_columna']['obligatorio']=true;
		//------------ Cortes de Control ----------------
		$sql['_info_cuadro_cortes']['sql'] = "SELECT	orden,		
											columnas_id,	    		
											columnas_descripcion,	
											identificador		,	
											pie_contar_filas	,	
											pie_mostrar_titular ,	
											pie_mostrar_titulos	,
											modo_inicio_colapsado, 	
											imp_paginar,
											descripcion				
									 FROM		apex_objeto_cuadro_cc	
									 WHERE		objeto_cuadro_proyecto = $proyecto ";
		if ( isset($componente) ) {
			$sql['_info_cuadro_cortes']['sql'] .= "	AND		objeto_cuadro=$componente ";
		}
		$sql['_info_cuadro_cortes']['sql'] .= " ORDER BY orden;";
		$sql['_info_cuadro_cortes']['registros']='n';
		$sql['_info_cuadro_cortes']['obligatorio']=false;
		return $sql;
	}
}
?>