<?
require_once("componente.php");

class componente_cuadro extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_cuadro';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_objeto_cuadro_columna';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = false;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- Cuadro ----------------
		$sql["info_cuadro"]["sql"] = "SELECT	titulo as titulo,		
								subtitulo						as	subtitulo,		
								sql								as	sql,			
								columnas_clave					as	columnas_clave,		 
								archivos_callbacks				as	archivos_callbacks,		
								ancho							as	ancho,			
								ordenar							as	ordenar,			
								exportar						as	exportar_xls,		 
								exportar_rtf					as	exportar_pdf,		 
								paginar							as	paginar,			
								tamano_pagina					as	tamano_pagina,
								eof_invisible					as	eof_invisible,		 
								eof_customizado					as	eof_customizado,
								pdf_respetar_paginacion			as	pdf_respetar_paginacion,	
								pdf_propiedades					as	pdf_propiedades,
								asociacion_columnas				as	asociacion_columnas
					 FROM		apex_objeto_cuadro
					 WHERE	objeto_cuadro_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_cuadro"]["sql"] .= " AND		objeto_cuadro='$componente' ";
		}
		$sql["info_cuadro"]["sql"] .= " ;";			
		$sql["info_cuadro"]["registros"]='1';
		$sql["info_cuadro"]['obligatorio']=true;
		//------------ Columnas ----------------
		$sql["info_cuadro_columna"]["sql"] = "SELECT	c.orden	as orden,		
								c.titulo						as titulo,		
								e.css							as estilo,	 
								c.columna_ancho					as ancho,	 
								c.valor_sql						as valor_sql,		
								f.funcion						as valor_sql_formato,	 
								c.valor_fijo					as valor_fijo,	 
								c.valor_proceso_esp				as valor_proceso,
								c.valor_proceso_parametros		as valor_proceso_parametros,								
								c.vinculo_indice				as vinculo_indice,	
								c.par_dimension_proyecto		as par_dimension_proyecto,	 
								c.par_dimension					as par_dimension,
								c.par_tabla						as par_tabla,		
								c.par_columna					as par_columna,
								c.no_ordenar					as no_ordenar,
								c.mostrar_xls					as	mostrar_xls,
								c.mostrar_pdf					as	mostrar_pdf,
								c.pdf_propiedades				as	pdf_propiedades,
								c.total							as total
					 FROM		apex_columna_estilo e,
								apex_objeto_cuadro_columna	c
								LEFT OUTER JOIN apex_columna_formato f	
								ON	f.columna_formato	= c.valor_sql_formato
					 WHERE	objeto_cuadro_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_cuadro_columna"]["sql"] .= " AND	objeto_cuadro = '$componente' ";
		}
		$sql["info_cuadro_columna"]["sql"] .= " AND	c.columna_estilo = e.columna_estilo	
					 AND		( c.desabilitado != '1' OR c.desabilitado IS NULL )
					 ORDER BY orden; ";
		$sql["info_cuadro_columna"]["registros"]='n';
		$sql["info_cuadro_columna"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/transversales';
	}
}
?>