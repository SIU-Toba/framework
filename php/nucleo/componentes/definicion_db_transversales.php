<?php
require_once('interfaces.php');

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

//##########################################################################

class componente_cuadro_reg extends componente_cuadro
{
}

//##########################################################################

class componente_filtro extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_filtro';
		$estructura[2]['registros'] = 'n';
		$estructura[2]['obligatorio'] = true;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$consumo_web = toba::get_solicitud()->get_tipo() == 'web';
		$sql = parent::get_vista_extendida($proyecto, $componente);	
		$sql["info_dimensiones"]["sql"] = "SELECT	g.dimension_grupo as	grupo,
						g.nombre as		 				grupo_nombre,
						g.descripcion as 				grupo_des,
						d.dimension as 				dimension,
						d.fuente_datos as 			fuente,
						d.nombre as 					nombre,
						d.descripcion as				descripcion,
						d.dimension_tipo as		 	tipo,
						d.inicializacion as			inicializacion,
						f.etiqueta as					etiqueta,
						f.tabla as 						tabla,
						f.columna as 					columna,
						f.requerido as 				obligatorio,
						f.no_interactivo as			no_interactivo,
						f.predeterminado as 			predeterminado ";
		if ( $consumo_web ) {
			$sql["info_dimensiones"]["sql"] .= ", u.usuario_perfil_datos as	perfil ";
		}
			$sql["info_dimensiones"]["sql"] .= " FROM 	apex_objeto_filtro f,
						apex_dimension d
						LEFT OUTER JOIN apex_dimension_grupo g ON d.dimension_grupo = g.dimension_grupo ";
		if ( $consumo_web ) {
			// Filtrar dimensiones por perfil
			$perfil = toba::get_hilo()->obtener_usuario_perfil_datos();
			$sql["info_dimensiones"]["sql"] .= " LEFT OUTER JOIN apex_dimension_perfil_datos u 
													ON (d.dimension = u.dimension)
														AND (u.usuario_perfil_datos = '$perfil') ";
		}
		$sql["info_dimensiones"]["sql"] .= " WHERE	f.dimension = d.dimension
				AND		f.dimension_proyecto = d.proyecto
				AND		objeto_filtro_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_dimensiones"]["sql"] .= " AND     objeto_filtro = '$componente' ";
		}
		$sql["info_dimensiones"]["sql"] .= " ORDER BY g.orden, f.orden; ";
		$sql["info_dimensiones"]["registros"]='n';
		$sql["info_dimensiones"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/transversales';
	}
}

//##########################################################################

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

//##########################################################################

class componente_html extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_html';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//---- Plan -----------------------
		$sql["info_html"]["sql"] = "SELECT	html      
									FROM	apex_objeto_html
									WHERE	objeto_html_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_html"]["sql"] .= " AND     objeto_html='$componente' ";
		}
		$sql["info_html"]["sql"] .= " ; ";
		$sql["info_html"]["registros"]='1';
		$sql["info_html"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/transversales';
	}
}

//##########################################################################

class componente_lista extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_lista';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		$sql["info_lista"]["sql"] = "SELECT titulo as titulo,
						subtitulo as				subtitulo,
						sql  as						sql,
						col_ver as					col_ver,
						col_formato as				col_formato,
						col_titulos as				col_titulos,
						ancho as					ancho,
						ordenar as					ordenar,
						exportar as					exportar,
						vinculo_clave as			vinculo_clave,
						vinculo_indice as			vinculo_indice
				FROM	apex_objeto_lista
				WHERE	objeto_lista_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_lista"]["sql"] .= " AND 	objeto_lista= '$componente' ";
		}
		$sql["info_lista"]["sql"] .= " ; ";
		$sql["info_lista"]["registros"]='1';
		$sql["info_lista"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/transversales';
	}
}

//##########################################################################

class componente_mt extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_dependencias';
		$estructura[2]['registros'] = 'n';
		$estructura[2]['obligatorio'] = false;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		$sql['info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/transversales';
	}
}
//##########################################################################

class componente_mt_s extends componente_mt
{
}

//##########################################################################

class componente_mt_abms extends componente_mt_s
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
}

//##########################################################################

class componente_mt_mds extends componente_mt_s
{
}

//##########################################################################

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
		return 'nucleo/componentes/transversales';
	}
}
?>