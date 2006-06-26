<?php
require_once('interfaces.php');

class componente_toba implements definicion_toba
{
	static function get_estructura()
	{
		$estructura[0]['tabla'] = 'apex_objeto';
		$estructura[0]['registros'] = '1';
		$estructura[0]['obligatorio'] = true;
		$estructura[1]['tabla'] = 'apex_objeto_info';
		$estructura[1]['registros'] = '1';
		$estructura[1]['obligatorio'] = false;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql['info']['sql'] = "	SELECT	o.proyecto          		as proyecto,                 
									o.objeto                    	as objeto,                   
									o.anterior                  	as anterior,                 
									o.reflexivo                 	as reflexivo,                
									o.clase_proyecto            	as clase_proyecto,           
									o.clase                     	as clase,                    
									o.subclase                  	as subclase,                 
									o.subclase_archivo          	as subclase_archivo,         
									o.objeto_categoria_proyecto 	as objeto_categoria_proyecto,
									o.objeto_categoria          	as objeto_categoria,         
									o.nombre                    	as nombre,                   
									o.titulo                    	as titulo,                   
									o.colapsable                	as colapsable,               
									o.descripcion               	as descripcion,              
									o.fuente_datos_proyecto     	as fuente_proyecto,    
									o.fuente_datos              	as fuente,             
									o.solicitud_registrar       	as solicitud_registrar,      
									o.solicitud_obj_obs_tipo    	as solicitud_obj_obs_tipo,   
									o.solicitud_obj_observacion 	as solicitud_obj_observacion,
									o.parametro_a               	as parametro_a,              
									o.parametro_b               	as parametro_b,              
									o.parametro_c                	as parametro_c,              
									o.parametro_d               	as parametro_d,              
									o.parametro_e               	as parametro_e,              
									o.parametro_f               	as parametro_f,              
									o.usuario                   	as usuario,                  
									o.creacion                  	as creacion,        
									c.editor_proyecto 				as clase_editor_proyecto,
									c.editor_item 					as clase_editor_item,
									c.archivo 						as clase_archivo,
									c.vinculos 	 					as clase_vinculos,
									c.editor_item 					as clase_editor,
									c.icono 						as clase_icono,
									c.descripcion_corta				as clase_descripcion_corta,
									c.instanciador_proyecto			as clase_instanciador_proyecto,
									c.instanciador_item 			as clase_instanciador_item,
									oi.objeto 						as objeto_existe_ayuda,
									(SELECT COUNT(*) 
										FROM apex_objeto_dependencias 
										WHERE objeto_consumidor = o.objeto
												AND proyecto = o.proyecto) as cant_dependencias
						FROM	apex_objeto o
									LEFT OUTER JOIN apex_objeto_info oi 
										ON (o.objeto = oi.objeto AND o.proyecto = oi.objeto_proyecto),
								apex_clase c
						WHERE	o.clase_proyecto = c.proyecto
						AND			o.clase = c.clase
						AND		o.proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info']['sql'] .= "	AND		o.objeto='$componente';";	
		}
		$sql['info']['registros']='1';	
		$sql['info']['obligatorio']=true;
		return $sql;
	}
		
	function get_vista_dependencias($proyecto, $componente=null)
	{
		$sql['sql'] = 	"	SELECT	d.identificador as		identificador,
							o.proyecto as					proyecto,
							o.objeto as						objeto,
							o.clase as						clase,
							c.archivo as 					clase_archivo,
							o.subclase as					subclase,
							o.subclase_archivo as			subclase_archivo,
							o.fuente_datos as 				fuente,
							d.parametros_a as				parametros_a,
							d.parametros_b as				parametros_b
					FROM	apex_objeto o,
							apex_objeto_dependencias d,
							apex_clase c
					WHERE	o.objeto = d.objeto_proveedor
					AND		o.proyecto = d.proyecto
					AND		o.clase = c.clase
					AND		o.clase_proyecto = c.proyecto
					AND		d.proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['sql'] .= "	AND		d.objeto_consumidor='$componente' ";	
		}
		$sql['sql'] .= "			ORDER BY identificador;";
		$sql['registros']='n';
		$sql['obligatorio']=false;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes';
	}

	static function get_nombre_clase_info()
	{
		return 'info_componente';
	}
}

//##########################################################################

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
												accion_imphtml_debug	as accion_imphtml_debug,
												accion_vinculo_carpeta		,
												accion_vinculo_item			,
												accion_vinculo_objeto		,
												accion_vinculo_popup		,
												accion_vinculo_popup_param	,
												accion_vinculo_celda,
												accion_vinculo_target
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
		return 'nucleo/componentes/interface';
	}
}

//##########################################################################

class componente_ci extends componente_ei
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[3]['tabla'] = 'apex_objeto_dependencias';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = false;
		$estructura[4]['tabla'] = 'apex_objeto_mt_me';
		$estructura[4]['registros'] = '1';
		$estructura[4]['obligatorio'] = true;
		$estructura[5]['tabla'] = 'apex_objeto_ci_pantalla';
		$estructura[5]['registros'] = 'n';
		$estructura[5]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//-- Info BASICA --------------
		$sql["info_ci"]['sql'] = "		SELECT		ev_procesar_etiq		as	ev_procesar_etiq,
													ev_cancelar_etiq		as	ev_cancelar_etiq,
													objetos					as	objetos,
													ancho					as	ancho,			
													alto					as	alto,
													posicion_botonera		as  posicion_botonera,
													tipo_navegacion			as	tipo_navegacion,
													con_toc					as  con_toc
											FROM	apex_objeto_mt_me
											WHERE	objeto_mt_me_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_ci']['sql'] .= "	AND		objeto_mt_me='$componente' ";	
		}
		$sql['info_ci']['sql'] .= ";";
		$sql['info_ci']['registros']='1';
		$sql['info_ci']['obligatorio']=true;
		//-- PANTALLAS --------------
		$sql["info_ci_me_pantalla"]['sql'] = "SELECT	pantalla			as pantalla,	
													identificador			as identificador,
													etiqueta			  	as etiqueta,
													descripcion			  	as descripcion,
													imagen_recurso_origen	as imagen_recurso_origen,
													imagen					as imagen,
													objetos				  	as objetos,
													eventos					as eventos,
													orden					as orden
									 	FROM	apex_objeto_ci_pantalla
										WHERE	objeto_ci_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_ci_me_pantalla']['sql'] .= "	AND		objeto_ci='$componente' ";	
		}
		$sql['info_ci_me_pantalla']['sql'] .= "ORDER	BY	orden;";
		$sql['info_ci_me_pantalla']['registros']='n';
		$sql['info_ci_me_pantalla']['obligatorio']=true;
		//-- DEPENDENCIAS ------------
		$sql['info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_ci';
	}

	static function get_tipo_abreviado()
	{
		return "CI";		
	}
}

//##########################################################################

class componente_ei_formulario extends componente_ei
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[3]['tabla'] = 'apex_objeto_ut_formulario';
		$estructura[3]['registros'] = '1';
		$estructura[3]['obligatorio'] = false;
		$estructura[4]['tabla'] = 'apex_objeto_ei_formulario_ef';
		$estructura[4]['registros'] = 'n';
		$estructura[4]['obligatorio'] = false;
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
		$sql["info_formulario_ef"]['sql'] = "SELECT	identificador as identificador,
										columnas					as		columnas,
										obligatorio					as		obligatorio,
										elemento_formulario 		as		elemento_formulario,
										inicializacion				as		inicializacion,
										etiqueta					as		etiqueta,
										etiqueta_estilo				as		etiqueta_estilo,
										descripcion					as		descripcion,
										orden						as		orden,
										colapsado					as 		colapsado
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

//##########################################################################

class componente_ei_formulario_ml extends componente_ei_formulario
{
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//Formulario
		$sql["info_formulario"]['sql'] = "SELECT	auto_reset as	auto_reset,
										scroll as 					scroll,					
										ancho as					ancho,
										alto as						alto,
										filas as					filas,
										filas_agregar as			filas_agregar,
										filas_agregar_online as 	filas_agregar_online,
										filas_ordenar as			filas_ordenar,
										filas_numerar as 			filas_numerar,
										columna_orden as 			columna_orden,
										analisis_cambios		as	analisis_cambios
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_formulario']['sql'] .= "	AND		objeto_ut_formulario='$componente' ";	
		}
		$sql['info_formulario']['sql'] .= ";";
		$sql['info_formulario']['registros']='1';
		$sql['info_formulario']['obligatorio']=true;
		//EF
		$sql["info_formulario_ef"]['sql'] = "SELECT	identificador as identificador,
										columnas	as				columnas,
										obligatorio	as				obligatorio,
										elemento_formulario as		elemento_formulario,
										inicializacion	as			inicializacion,
										etiqueta	as				etiqueta,
										etiqueta_estilo	as			etiqueta_estilo,
										descripcion	as				descripcion,
										orden	as					orden,
										total as 					total,
										estilo as					columna_estilo,
										colapsado as 				colapsado
								FROM	apex_objeto_ei_formulario_ef
								WHERE	objeto_ei_formulario_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info_formulario_ef']['sql'] .= "	AND		objeto_ei_formulario='$componente' ";	
		}
		$sql['info_formulario_ef']['sql'] .= " AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql['info_formulario_ef']['registros']='n';
		$sql['info_formulario_ef']['obligatorio']=false;
		return $sql;
	}

	static function get_nombre_clase_info()
	{
		return 'info_ei_formulario_ml';
	}
	
	static function get_tipo_abreviado()
	{
		return "Form. ML";		
	}
}


//##########################################################################

class componente_ei_filtro extends componente_ei_formulario
{
	static function get_tipo_abreviado()
	{
		return "Filtro";		
	}	
}


//##########################################################################

class componente_ei_cuadro extends componente_ei
{
 	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[3]['tabla'] = 'apex_objeto_cuadro';
		$estructura[3]['registros'] = '1';
		$estructura[3]['obligatorio'] = true;		
		$estructura[4]['tabla'] = 'apex_objeto_cuadro_cc';
		$estructura[4]['registros'] = 'n';
		$estructura[4]['obligatorio'] = false;		
		$estructura[5]['tabla'] = 'apex_objeto_ei_cuadro_columna';
		$estructura[5]['registros'] = 'n';
		$estructura[5]['obligatorio'] = false;		
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- Cuadro ----------------
		$sql['info_cuadro']['sql'] = "SELECT	titulo as titulo,		
										c.subtitulo						as	subtitulo,		
										c.sql							as	sql,			
										c.columnas_clave				as	columnas_clave,
										c.clave_dbr						as	clave_datos_tabla,
										c.archivos_callbacks			as	archivos_callbacks,		
										c.ancho							as	ancho,			
										c.ordenar						as	ordenar,			
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
										n.archivo 						as	dao_archivo,
										c.cc_modo						as	cc_modo,						
										c.cc_modo_anidado_colap			as	cc_modo_anidado_colap,		
										c.cc_modo_anidado_totcol		as	cc_modo_anidado_totcol,		
										c.cc_modo_anidado_totcua		as	cc_modo_anidado_totcua		
							 FROM		apex_objeto_cuadro c
							 			LEFT OUTER JOIN	apex_nucleo n
							 			ON c.dao_nucleo_proyecto = n.proyecto
							 			AND c.dao_nucleo = n.nucleo
							 WHERE	objeto_cuadro_proyecto='$proyecto' ";	
		if ( isset($componente) ) {
			$sql['info_cuadro']['sql'] .= "	AND		objeto_cuadro='$componente' ";	
		}
		$sql['info_cuadro']['sql'] .= ";";
		$sql['info_cuadro']['registros']='1';
		$sql['info_cuadro']['obligatorio']=true;
		//------------ Columnas ----------------
		$sql['info_cuadro_columna']['sql'] = "SELECT	c.orden	as orden,		
												c.titulo						as titulo,
												c.estilo_titulo					as estilo_titulo,		
												e.css							as estilo,	 
												c.ancho							as ancho,	 
												c.clave							as clave,		
												f.funcion						as formateo,	 
												c.vinculo_indice				as vinculo_indice,	
												c.no_ordenar					as no_ordenar,
												c.mostrar_xls					as mostrar_xls,
												c.mostrar_pdf					as mostrar_pdf,
												c.pdf_propiedades				as pdf_propiedades,
												c.total							as total,
												c.total_cc						as total_cc
									 FROM		apex_columna_estilo e,
												apex_objeto_ei_cuadro_columna	c
												LEFT OUTER JOIN apex_columna_formato f	
												ON	f.columna_formato	= c.formateo
									 WHERE	objeto_cuadro_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql['info_cuadro_columna']['sql'] .= "	AND		objeto_cuadro='$componente' ";
		}
		$sql['info_cuadro_columna']['sql'] .= "	AND		c.estilo = e.columna_estilo	
					 AND		( c.desabilitado != '1' OR c.desabilitado IS NULL )
					 ORDER BY orden;";
		$sql['info_cuadro_columna']['registros']='n';
		$sql['info_cuadro_columna']['obligatorio']=true;
		//------------ Cortes de Control ----------------
		$sql['info_cuadro_cortes']['sql'] = "SELECT	orden,		
											columnas_id,	    		
											columnas_descripcion,	
											identificador		,	
											pie_contar_filas	,	
											pie_mostrar_titular ,	
											pie_mostrar_titulos	,	
											imp_paginar,
											descripcion				
									 FROM		apex_objeto_cuadro_cc	
									 WHERE		objeto_cuadro_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql['info_cuadro_cortes']['sql'] .= "	AND		objeto_cuadro='$componente' ";
		}
		$sql['info_cuadro_cortes']['sql'] .= " ORDER BY orden;";
		$sql['info_cuadro_cortes']['registros']='n';
		$sql['info_cuadro_cortes']['obligatorio']=false;
		return $sql;
	}

	static function get_nombre_clase_info()
	{
		return 'info_ei_cuadro';
	}
	
	static function get_tipo_abreviado()
	{
		return "Cuadro";		
	}
}


//##########################################################################

class componente_ei_esquema extends componente_ei
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[3]['tabla'] = 'apex_objeto_esquema';
		$estructura[3]['registros'] = '1';
		$estructura[3]['obligatorio'] = false;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);

		$sql['info_esquema']['sql'] = "SELECT
												dirigido,
												formato,					
												modelo_ejecucion_cache,	
												ancho,					
												alto
									FROM	apex_objeto_esquema
									WHERE	objeto_esquema_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql['info_esquema']['sql'] .= "	AND     objeto_esquema='$componente' ";	
		}
		$sql['info_esquema']['sql'] .= ";";
		$sql['info_esquema']['registros']='1';
		$sql['info_esquema']['obligatorio']=true;
		return $sql;
	}
	
	static function get_tipo_abreviado()
	{
		return "Esquema";		
	}
}


//##########################################################################

class componente_ei_arbol extends componente_ei
{
	static function get_tipo_abreviado()
	{
		return "Árbol";		
	}
}

//##########################################################################

class componente_ei_archivos extends componente_ei
{
	static function get_tipo_abreviado()
	{
		return "Archivos";		
	}
}

//##########################################################################

class componente_ei_calendario extends componente_ei
{
	static function get_tipo_abreviado()
	{
		return "Calendario";		
	}
}

//##########################################################################

class componente_cn extends componente_toba
{
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/negocio';
	}

	static function get_tipo_abreviado()
	{
		return "CN";		
	}
}

//##########################################################################

class componente_datos_relacion extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_datos_rel';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_objeto_datos_rel_asoc';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = true;
		$estructura[4]['tabla'] = 'apex_objeto_dependencias';
		$estructura[4]['registros'] = 'n';
		$estructura[4]['obligatorio'] = false;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- Info base de la estructura ----------------
		$sql["info_estructura"]['sql'] = "SELECT	proyecto 	,	
													objeto      ,	
													debug		,	
													ap			,	
													ap_clase	,	
													ap_archivo		
										 FROM		apex_objeto_datos_rel
										 WHERE		proyecto='$proyecto' ";	
		if ( isset($componente) ) {
			$sql["info_estructura"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_estructura"]['sql'] .= ";";
		$sql["info_estructura"]['registros']='1';
		$sql["info_estructura"]['obligatorio']=true;
		//------------ relaciones ----------------
		$sql["info_relaciones"]['sql'] = "SELECT	proyecto 		,
												objeto 		    ,
												asoc_id			,
											--	identificador   ,
												padre_proyecto	,
												padre_objeto	,
												padre_id		,
												padre_clave		,
												hijo_proyecto	,
												hijo_objeto		,
												hijo_id			,
												hijo_clave		,
												cascada			,
												orden			
									 FROM		apex_objeto_datos_rel_asoc 
									 WHERE		proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_relaciones"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_relaciones"]['sql'] .= ";";
		$sql["info_relaciones"]['registros']='n';
		$sql["info_relaciones"]['obligatorio']=false;
		$sql['info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);		
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_datos_relacion';
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/persistencia';
	}

	static function get_tipo_abreviado()
	{
		return "Relación";		
	}
}


//##########################################################################

class componente_datos_tabla extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_db_registros';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_objeto_db_registros_col';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = true;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- Info base de la estructura ----------------
		$sql["info_estructura"]['sql'] = "SELECT	dt.tabla    as tabla,
											dt.alias          	as alias,
											dt.min_registros  	as min_registros,
											dt.max_registros  	as max_registros,
											dt.ap				as ap			,	
											dt.ap_clase			as ap_sub_clase	,	
											dt.ap_archivo	    as ap_sub_clase_archivo,
											dt.modificar_claves as ap_modificar_claves,
											ap.clase			as ap_clase,
											ap.archivo			as ap_clase_archivo
					 FROM		apex_objeto_db_registros as dt
				 				LEFT OUTER JOIN apex_admin_persistencia ap ON dt.ap = ap.ap
					 WHERE		objeto_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_estructura"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_estructura"]['sql'] .= ";";
		$sql["info_estructura"]['registros']='1';
		$sql["info_estructura"]['obligatorio']=true;
		//------------ Columnas ----------------
		$sql["info_columnas"]['sql'] = "SELECT	objeto_proyecto,
						objeto 			,	
						col_id			,	
						columna			,	
						tipo			,	
						pk				,	
						secuencia		,
						largo			,	
						no_nulo			,	
						no_nulo_db		,
						externa
					 FROM		apex_objeto_db_registros_col 
					 WHERE		objeto_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_columnas"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_columnas"]['sql'] .= ";";
		$sql["info_columnas"]['registros']='n';
		$sql["info_columnas"]['obligatorio']=true;
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_datos_tabla';
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/persistencia';
	}
	
	static function get_tipo_abreviado()
	{
		return "Tabla";		
	}
}

//##########################################################################

class componente_item implements definicion_toba
{
	static function get_estructura()
	{
		$estructura[0]['tabla'] = 'apex_item';
		$estructura[0]['registros'] = '1';
		$estructura[0]['obligatorio'] = true;
		$estructura[1]['tabla'] = 'apex_item_info';
		$estructura[1]['registros'] = '1';
		$estructura[1]['obligatorio'] = false;
		$estructura[2]['tabla'] = 'apex_item_objeto';
		$estructura[2]['registros'] = 'n';
		$estructura[2]['obligatorio'] = false;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql['basica']['sql'] = "SELECT	i.proyecto as			item_proyecto,	
						i.item as								item,	
						i.nombre	as							item_nombre,
						i.descripcion as						item_descripcion,	
						i.actividad_buffer_proyecto as			item_act_buffer_proyecto,
						i.actividad_buffer as					item_act_buffer,	
						i.actividad_patron_proyecto as			item_act_patron_proyecto,
						i.actividad_patron as					item_act_patron,	
						p.archivo as							item_act_patron_script,	
						i.actividad_accion as					item_act_accion_script,	
						i.solicitud_tipo as						item_solic_tipo,	
						i.solicitud_registrar as				item_solic_registrar,
						i.solicitud_obs_tipo_proyecto	as		item_solic_obs_tipo_proyecto,	
						i.solicitud_obs_tipo	as				item_solic_obs_tipo,	
						i.solicitud_observacion	as				item_solic_observacion,	
						i.solicitud_registrar_cron	as			item_solic_cronometrar,	
						i.parametro_a as						item_parametro_a,	
						i.parametro_b as						item_parametro_b,	
						i.parametro_c as						item_parametro_c,
						pt.clase_nombre	as						tipo_pagina_clase,
						pt.clase_archivo as						tipo_pagina_archivo,
						pt.include_arriba	as					item_include_arriba,	
						pt.include_abajo as						item_include_abajo,	
						i.zona_proyecto as						item_zona_proyecto,
						i.zona as								item_zona,
						z.archivo as							item_zona_archivo,
						i.publico as							item_publico,
						ii.item as								item_existe_ayuda,
						i.carpeta as							carpeta,
						i.menu as								menu,
						i.orden as								orden,
						i.publico as							publico,
						i.solicitud_registrar_cron as			crono,
						i.solicitud_tipo as						solicitud_tipo,
						i.padre	as 								item_padre,
						(SELECT COUNT(*) FROM apex_item_objeto 
							WHERE item = i.item AND proyecto = i.proyecto) as cant_dependencias,
						(SELECT COUNT(*) FROM apex_item 
							WHERE padre = i.item AND proyecto = i.proyecto AND solicitud_tipo <> 'fantasma' AND item != '') as cant_items_hijos						
				FROM	apex_item i	
							LEFT OUTER JOIN apex_item_zona z	ON	( i.zona_proyecto	= z.proyecto AND i.zona	= z.zona	)
							LEFT OUTER JOIN apex_item_info ii ON (i.proyecto = ii.item_proyecto AND i.item = ii.item),
						apex_patron	p,	
						apex_pagina_tipo pt	
				WHERE		pt.pagina_tipo	= i.pagina_tipo
				AND		pt.proyecto	= i.pagina_tipo_proyecto
				AND		i.actividad_patron =	p.patron	
				AND		i.actividad_patron_proyecto =	p.proyecto
				AND		i.proyecto = '$proyecto'";
		if ( isset($componente) ) {
			$sql['basica']['sql'] .= "	AND		i.item ='$componente' ";	
		}
		$sql['basica']['registros']='1';	
		$sql['basica']['obligatorio']=true;
		$sql['objetos']['sql'] =	"SELECT	o.proyecto as		objeto_proyecto,
						o.objeto	as						objeto,
						o.nombre	as						objeto_nombre,
						o.subclase as						objeto_subclase,
						o.subclase_archivo as				objeto_subclase_archivo,
						io.orden	as						orden,	
						c.proyecto as					  	clase_proyecto,	
						c.clase as						  	clase,	
						c.archivo as					  	clase_archivo,
						d.proyecto as						fuente_proyecto,
						d.fuente_datos	as				  	fuente,
						d.fuente_datos_motor	as			fuente_motor,
						d.host as						  	fuente_host,	
						d.usuario as					  	fuente_usuario,	
						d.clave as						  	fuente_clave,
						d.base as						  	fuente_base
				FROM	apex_item_objeto io,	
						apex_objeto	o,	
						apex_fuente_datos	d,	
						apex_clase c
				WHERE	io.objeto =	o.objeto	
					 AND		io.proyecto	= o.proyecto
				AND		o.clase = c.clase	
				AND		o.clase_proyecto = c.proyecto	
				AND		o.fuente_datos	= d.fuente_datos
				AND		o.fuente_datos_proyecto	= d.proyecto
				AND		io.proyecto	= '$proyecto'";
		if ( isset($componente) ) {
			$sql['objetos']['sql'] .= "	AND		io.item ='$componente' ";	
		}
		$sql['objetos']['sql'] .= "	ORDER	BY	io.orden;";	
		$sql['objetos']['registros']='n';
		$sql['objetos']['obligatorio']=false;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return '';
	}

	static function get_nombre_clase_info()
	{
		return 'info_item';
	}	
}
?>