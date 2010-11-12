<?php

class toba_migracion_1_1_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql = array();
		if (! $this->elemento->get_db()->existe_columna('validacion_bloquear_usuario', 'apex_proyecto')) {
				$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN validacion_bloquear_usuario			smallint		";
		}
		if (! $this->elemento->get_db()->existe_columna('modo_inicio_colapsado', 'apex_objeto_cuadro_cc')) {
			$sql[] = 'ALTER TABLE apex_objeto_cuadro_cc ADD COLUMN modo_inicio_colapsado smallint';
		}
			if (! $this->elemento->get_db()->existe_columna('bloqueado', 'apex_usuario')) {
			$sql[] = 'ALTER TABLE apex_usuario ADD COLUMN bloqueado smallint';
		}		
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN usar_vinculo			smallint		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_carpeta		varchar(60)		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_item			varchar(60)		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_popup		smallint		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_popup_param	varchar(100)	";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_target		varchar(40)		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_celda		varchar(40)		";
		$sql[] = "ALTER TABLE apex_objeto_ut_formulario ADD COLUMN expandir_descripcion		smallint	";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN carga_dt				int4	";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN carga_consulta_php	int4	";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN carga_no_seteado_ocultar	smallint	";
		$sql[] = "ALTER TABLE apex_clase ADD COLUMN objeto_dr_proyecto						varchar(15)	";
		$sql[] = "ALTER TABLE apex_clase ADD COLUMN objeto_dr								int4		";
		$sql[] = "ALTER TABLE apex_clase ADD COLUMN utiliza_fuente_datos					int4		";
		$sql[] = "ALTER TABLE apex_objeto_db_registros ADD COLUMN fuente_datos_proyecto		varchar(15)";		
		$sql[] = "ALTER TABLE apex_objeto_db_registros ADD COLUMN fuente_datos				varchar(20)";				
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN pagina_tipo							varchar(20)";				
		//$sql[] = "CREATE UNIQUE INDEX apex_objeto_dbr_uq_tabla ON apex_objeto_db_registros (fuente_datos, tabla)";
		$sql[] = "	CREATE TABLE apex_objeto_db_registros_uniq
					(
						objeto_proyecto    			   	varchar(15)		NOT NULL,
						objeto 		                	int4       		NOT NULL,
						uniq_id							int4			NULL,
						columnas						varchar(255)	NULL
					);";
		$sql[] = "	CREATE TABLE apex_clase_relacion
					(
						proyecto							varchar(15)		NOT NULL,
						clase_relacion						int4			NOT NULL, 
						clase_contenedora					varchar(60)		NOT NULL,
						clase_contenida						varchar(60)		NOT NULL
					);";
		$sql[] = "	CREATE TABLE	apex_consulta_php
					(
					  	proyecto 					VARCHAR(15)  	NOT NULL,
						consulta_php				int4			NOT NULL, 
					  	clase                   	VARCHAR(60)  	NOT NULL,
					  	archivo                 	VARCHAR(255) 	NOT NULL,
					  	descripcion                	VARCHAR(255) 	NULL
					);";		

		$this->elemento->get_db()->ejecutar($sql);	

		
		//--- Asistentes
		$sql = <<<EOF
CREATE TABLE apex_molde_opciones_generacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	uso_autoload					smallint			NULL,		-- Hace que no se generen require_once
	origen_datos_cuadro				varchar(20)			NULL,		-- metodologia usada para proveer datos: consulta_php, datos_tabla
	carga_php_include				varchar(255)		NULL,		-- consulta_php por defecto
	carga_php_clase					varchar(255)		NULL,		-- consulta_php por defecto
	CONSTRAINT "apex_molde_opciones_generacion_pk" PRIMARY KEY("proyecto"),
	CONSTRAINT "apex_molde_opciones_generacion_fk_proy" 	FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_molde_operacion_tipo_seq	INCREMENT 1	MINVALUE	0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_molde_operacion_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: operacion_tipo
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	operacion_tipo					int4				DEFAULT nextval('"apex_molde_operacion_tipo_seq"'::text) NOT	NULL,	
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	clase							varchar(255)		NOT NULL,
	ci								varchar(255)		NOT NULL,
	icono							varchar(30)			NULL,
	vista_previa					varchar(100)		NULL,
	orden							float				NULL,
	CONSTRAINT	"apex_molde_operacion_tipo_pk"	 PRIMARY	KEY ("operacion_tipo")
);
--#################################################################################################

CREATE SEQUENCE apex_molde_operacion_tipo_dato_seq	INCREMENT 1	MINVALUE	0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_molde_operacion_tipo_dato
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: tipo_dato
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	tipo_dato						int4				DEFAULT nextval('"apex_molde_operacion_tipo_dato_seq"'::text) NOT	NULL,
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	dt_tipo_dato					varchar(1)			NULL,		
	elemento_formulario				varchar(30)			NULL,
	cuadro_estilo 					int4		    	NULL,	
	cuadro_formato 					int4		    	NULL,
	orden							float				NULL,
	filtro_operador					varchar(10)			NULL,
	CONSTRAINT	"apex_molde_operacion_tipo_dato_pk"	PRIMARY	KEY ("tipo_dato"),
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_ef" FOREIGN KEY ("elemento_formulario") REFERENCES "apex_elemento_formulario" ("elemento_formulario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_estilo" FOREIGN KEY ("cuadro_estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_formato" FOREIGN KEY ("cuadro_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_tipo_datos" FOREIGN KEY ("dt_tipo_dato") REFERENCES "apex_tipo_datos" ("tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_molde_operacion_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: molde
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  					varchar(255)	NOT NULL,
	molde						int4			DEFAULT nextval('"apex_molde_operacion_seq"'::text) 		NOT NULL, 
	operacion_tipo				int4			NOT NULL,
	nombre                  	varchar(255) 	NULL,
	item						varchar(60)		NOT NULL,
	carpeta_archivos           	varchar(255) 	NOT NULL,
	prefijo_clases				varchar(30)		NOT NULL,
	fuente						varchar(20)		NOT NULL,
	CONSTRAINT  "apex_molde_operacion_pk" PRIMARY KEY ("molde", "proyecto"),
	CONSTRAINT 	"apex_molde_operacion_item" UNIQUE ("proyecto","item"),
	CONSTRAINT	"apex_molde_operacion_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_operacion_fk_item" FOREIGN	KEY ("item", "proyecto") REFERENCES	"apex_item"	("item", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_fk_tipo"  FOREIGN KEY ("operacion_tipo") REFERENCES   "apex_molde_operacion_tipo" ("operacion_tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_operacion_abms_fk_fuente" FOREIGN KEY	("proyecto","fuente") REFERENCES "apex_fuente_datos"	("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_molde_operacion_log_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion_log
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: generacion
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  					varchar(255)	NOT NULL,
	molde						int4	 		NOT NULL, 
	generacion					int4			DEFAULT nextval('"apex_molde_operacion_log_seq"'::text) 		NOT NULL, 
	momento						timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	CONSTRAINT  "apex_molde_operacion_log_pk" PRIMARY KEY ("generacion"),
	CONSTRAINT  "apex_molde_operacion_log_fk" FOREIGN KEY ("molde", "proyecto") REFERENCES "apex_molde_operacion" ("molde", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_molde_operacion_log_elementos_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion_log_elementos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: generacion
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	generacion					int4			NOT NULL, 
	molde						int4	 		NOT NULL, 
	id							int4			DEFAULT nextval('"apex_molde_operacion_log_elementos_seq"'::text) 		NOT NULL, 
	tipo						varchar(255)	NOT NULL,
	proyecto					varchar(255)	NOT NULL,
	clave						varchar(255)	NOT NULL, 
	CONSTRAINT  "apex_molde_operacion_log_e_pk" PRIMARY KEY ("id"),
	CONSTRAINT  "apex_molde_operacion_log_e_fk" FOREIGN KEY ("generacion") REFERENCES "apex_molde_operacion_log" ("generacion") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--**************************************************************************************************
--**************************************************************************************************
--************************                 ABM SIMPLE                 ******************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_molde_operacion_abms
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: molde
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  							varchar(255)	NOT NULL,
	molde								int4			NOT NULL, 
	tabla								varchar(255)	NOT NULL,
	gen_usa_filtro						smallint		NULL,
	gen_separar_pantallas				smallint		NULL,
	filtro_comprobar_parametros			smallint		NULL,
	cuadro_eof							varchar(255)	NULL,
	cuadro_eliminar_filas				smallint		NULL,
	cuadro_id							varchar(255)	NULL,
	cuadro_forzar_filtro				smallint		NULL,
	cuadro_carga_origen					varchar(15)		NULL,
	cuadro_carga_sql					varchar			NULL,
	cuadro_carga_php_include			varchar(255)	NULL,
	cuadro_carga_php_clase				varchar(255)	NULL,
	cuadro_carga_php_metodo				varchar(255)	NULL,
	datos_tabla_validacion				smallint		NULL,
	apdb_pre							smallint		NULL,	-- Hay que poner uno por ventana.
	CONSTRAINT  "apex_molde_operacion_abms_pk" PRIMARY KEY ("proyecto","molde"),
	CONSTRAINT  "apex_molde_operacion_abms_fk_molde" FOREIGN KEY ("molde", "proyecto") REFERENCES "apex_molde_operacion" ("molde", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_molde_operacion_abms_fila_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion_abms_fila
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: molde, fila
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  							varchar(255)	NOT NULL,
	molde								int4			NOT NULL, 
	fila								int4			DEFAULT nextval('"apex_molde_operacion_abms_fila_seq"'::text) NOT NULL,
	orden								float			NOT NULL,
	columna        						varchar(255)   	NOT NULL,
	asistente_tipo_dato					int4		   	NULL,
	etiqueta       						varchar(255)   	NULL,
	en_cuadro							smallint		NULL,
	en_form								smallint		NULL,
	en_filtro							smallint		NULL,
	filtro_operador						varchar(10)		NULL, -- Que operador utilizar? (=, <>, >, <, LIKE, etc)
	cuadro_estilo 						int4		   	NULL,	
	cuadro_formato 						int4		  	NULL,	
	dt_tipo_dato						varchar(1)		NULL,
	dt_largo							smallint		NULL,
	dt_secuencia						varchar(255)	NULL,
	dt_pk								smallint		NULL,
	elemento_formulario					varchar(30)		NULL,
	ef_obligatorio						smallint		NULL,
	ef_desactivar_modificacion			smallint		NULL,
	ef_procesar_javascript				smallint		NULL,
	ef_carga_origen						varchar(15)		NULL,
	ef_carga_sql						varchar			NULL,
	ef_carga_php_include				varchar(255)	NULL,
	ef_carga_php_clase					varchar(255)	NULL,
	ef_carga_php_metodo					varchar(255)	NULL,
	ef_carga_tabla						varchar(255)	NULL,
	ef_carga_col_clave					varchar(255)	NULL,
	ef_carga_col_desc					varchar(255)	NULL,
	CONSTRAINT  "apex_molde_operacion_abms_fila_pk" PRIMARY KEY ("fila","molde","proyecto"),
	CONSTRAINT	"apex_molde_operacion_abms_fila_uq" UNIQUE 	("proyecto","molde","columna"),
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_molde" FOREIGN KEY ("molde","proyecto") REFERENCES "apex_molde_operacion" ("molde","proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila" FOREIGN KEY ("asistente_tipo_dato") REFERENCES "apex_molde_operacion_tipo_dato" ("tipo_dato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_ef" FOREIGN KEY ("elemento_formulario") REFERENCES "apex_elemento_formulario" ("elemento_formulario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_estilo" FOREIGN KEY ("cuadro_estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_formato" FOREIGN KEY ("cuadro_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_tipo_datos" FOREIGN KEY ("dt_tipo_dato") REFERENCES "apex_tipo_datos" ("tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
	
EOF;
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	function instancia__migracion_nombre_componentes()
	{
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ci', '8', 'nucleo/componentes/interface/toba_ci.php', 'Controlador de Interface', 'ci', 'objetos/multi_etapa.gif', NULL, 'toba', 'objeto_ci', NULL, 'toba', '1642', NULL, 'toba', '/admin/objetos_toba/editores/ci', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_cn', '5', 'nucleo/componentes/negocio/toba_cn.php', 'Objeto de Negocio', 'cn', 'objetos/negocio.gif', NULL, 'toba', 'objeto', NULL, NULL, NULL, NULL, 'toba', '2045', NULL, NULL, NULL, 'aa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_datos_relacion', '9', 'nucleo/componentes/persistencia/toba_datos_relacion.php', 'Objeto DATOS - RELACION', 'datos_relacion', 'objetos/datos_relacion.gif', NULL, 'toba', 'objeto', NULL, NULL, NULL, NULL, 'toba', '/admin/objetos_toba/editores/db_tablas', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_datos_tabla', '9', 'nucleo/componentes/persistencia/toba_datos_tabla.php', 'Objeto DATOS - TABLA', 'datos_tabla', 'objetos/datos_tabla.gif', NULL, 'toba', 'objeto', NULL, NULL, NULL, NULL, 'toba', '/admin/objetos_toba/editores/db_registros', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_arbol', '7', 'nucleo/componentes/interface/toba_ei_arbol.php', 'Muestra una estructura en forma de arbol permitiendo colapsar distintas ramas, anexar iconos, utilerias y  propiedades a cada uno de los distintos nodos.', 'ei_arbol', 'objetos/arbol.gif', NULL, 'toba', 'objeto_ei', NULL, NULL, NULL, NULL, 'toba', '1241', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_archivos', '7', 'nucleo/componentes/interface/toba_ei_archivos.php', 'Muestra archivos y directorios.', 'ei_archivos', 'objetos/archivos.gif', NULL, 'toba', 'objeto_ei', NULL, NULL, NULL, NULL, 'toba', '/admin/objetos_toba/editores/ei_archivos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_calendario', '7', 'nucleo/componentes/interface/toba_ei_calendario.php', 'Calendario que permite la selección de días y semanas', 'ei_calendario', 'objetos/calendario.gif', NULL, 'toba', 'objeto_ei', NULL, NULL, NULL, NULL, 'toba', '/admin/objetos_toba/editores/ci', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_cuadro', '7', 'nucleo/componentes/interface/toba_ei_cuadro.php', 'Objeto cuadro que carga su contenido a partir de un ARRAY', 'ei_cuadro', 'objetos/cuadro_array.gif', NULL, 'toba', 'objeto_ei', NULL, 'toba', '1843', NULL, 'toba', '/admin/objetos_toba/editores/ei_cuadro', NULL, NULL, NULL, 'd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_esquema', '7', 'nucleo/componentes/interface/toba_ei_esquema.php', 'Muestra grafos utilizando GraphViz', 'ei_esquema', 'objetos/esquema.gif', NULL, 'toba', 'objeto_ei', NULL, NULL, NULL, NULL, 'toba', '/admin/objetos_toba/editores/ci', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_filtro', '7', 'nucleo/componentes/interface/toba_ei_filtro.php', 'Formulario para filtro', 'ei_filtro', 'objetos/ut_formulario.gif', NULL, 'toba', 'objeto_ei_formulario', NULL, 'toba', '1842', NULL, 'toba', '/admin/objetos_toba/editores/ei_filtro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_formulario', '7', 'nucleo/componentes/interface/toba_ei_formulario.php', 'Representa un formulario de datos', 'ei_formulario', 'objetos/ut_formulario.gif', NULL, 'toba', 'objeto_ei', NULL, 'toba', '1842', NULL, 'toba', '/admin/objetos_toba/editores/ei_formulario', NULL, NULL, NULL, 'ff', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO apex_clase (proyecto, clase, clase_tipo, archivo, descripcion, descripcion_corta, icono, screenshot, ancestro_proyecto, ancestro, instanciador_id, instanciador_proyecto, instanciador_item, editor_id, editor_proyecto, editor_item, editor_ancestro_proyecto, editor_ancestro, plan_dump_objeto, sql_info, doc_clase, doc_db, doc_sql, vinculos, autodoc, parametro_a, parametro_b, parametro_c, exclusivo_toba) VALUES ('toba', 'toba_ei_formulario_ml', '7', 'nucleo/componentes/interface/toba_ei_formulario_ml.php', 'Elemento de formulario multilinea', 'ei_formulario_ml', 'objetos/ut_formulario_ml.gif', NULL, 'toba', 'objeto_ei_formulario', NULL, 'toba', '1842', NULL, 'toba', '/admin/objetos_toba/editores/ei_formulario_ml', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_cn'                    WHERE clase =  'objeto_cn';          	";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_datos_relacion'        WHERE clase =  'objeto_datos_relacion';";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_datos_tabla'           WHERE clase =  'objeto_datos_tabla';            ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei'                    WHERE clase =  'objeto_ei';           ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_arbol'              WHERE clase =  'objeto_ei_arbol';          ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_archivos'           WHERE clase =  'objeto_ei_archivos';         ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_calendario'         WHERE clase =  'objeto_ei_calendario';        ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ci'                	WHERE clase =  'objeto_ci';       ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_cuadro'             WHERE clase =  'objeto_ei_cuadro';      ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_esquema'            WHERE clase =  'objeto_ei_esquema';                     ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_formulario'         WHERE clase =  'objeto_ei_formulario';                      ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_filtro'  			WHERE clase =  'objeto_ei_filtro';                      ";
		$sql[] = "UPDATE apex_objeto SET clase = 'toba_ei_formulario_ml'      WHERE clase =  'objeto_ei_formulario_ml';                          ";

		$this->elemento->get_db()->ejecutar($sql);
	}
	


	/**
	 * En migraciones anteriores algunos datos_tabla quedaron sin fuente. se le pone la fuente por defecto del sistema
	 */
	function proyecto__dt_fuentes_faltantes()
	{
		$sql[] = "
			UPDATE apex_objeto
				SET
					fuente_datos = (SELECT fuente_datos FROM apex_proyecto WHERE proyecto = '{$this->elemento->get_id()}'),
					fuente_datos_proyecto = '{$this->elemento->get_id()}'
				WHERE
						proyecto = '{$this->elemento->get_id()}'
					AND objeto IN (
							SELECT dt.objeto FROM apex_objeto_db_registros as dt
							WHERE dt.objeto_proyecto = '{$this->elemento->get_id()}'
						)
					AND fuente_datos IS NULL
		";
		return $this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 * Para poder utilizar el unique de (tabla,fuente) es necesario replicar la info de la fuente en la tabla del datos_tabla
	 */
	function proyecto__dt_duplicacion_fuente()
	{
		$sql = "
			SELECT
				dt1.tabla,
				dt1.objeto
			FROM apex_objeto_db_registros dt1
			WHERE
					dt1.objeto_proyecto = '{$this->elemento->get_id()}'
				AND dt1.tabla IN
					(SELECT dt2.tabla
						FROM apex_objeto_db_registros as dt2
						WHERE
								dt2.objeto_proyecto = dt1.objeto_proyecto
							AND dt2.objeto != dt1.objeto
					)
			ORDER BY dt1.tabla
		";
		$tablas = $this->elemento->get_db()->consultar($sql);
		$sql = "
			UPDATE apex_objeto_db_registros
				SET
					fuente_datos = (SELECT fuente_datos FROM apex_objeto WHERE
											apex_objeto.proyecto = apex_objeto_db_registros.objeto_proyecto
										AND apex_objeto.objeto = apex_objeto_db_registros.objeto ),
					fuente_datos_proyecto = '{$this->elemento->get_id()}'
				WHERE
					objeto_proyecto = '{$this->elemento->get_id()}'
		";
		$sql = "UPDATE
					apex_objeto_db_registros
				SET
					fuente_datos = apex_objeto.fuente_datos,
					fuente_datos_proyecto = '{$this->elemento->get_id()}'
				FROM
					apex_objeto
				WHERE
					apex_objeto_db_registros.objeto_proyecto = apex_objeto.proyecto AND
					apex_objeto_db_registros.objeto = apex_objeto.objeto AND
					apex_objeto_db_registros.objeto_proyecto = '{$this->elemento->get_id()}'
		";
		try {
			return $this->elemento->get_db()->ejecutar($sql);
		} catch (toba_error $e) {
			$mensaje = $e->getMessage();
			if (! empty($tablas)) {
				$mensaje .= "\n[ERROR]: El proyecto tiene datos_tabla duplicados. Desde la versión 1.1.0 no es posible
				para una misma fuente tener dos componentes que representen a la misma tabla del sistema.
				Es necesario resolver el conflicto dejando solo un represetante de las siguientes tablas y reiniciando la migración:\n\n";
				foreach ($tablas as $tabla) {
					$mensaje .= "tabla: {$tabla['tabla']}, id del objeto:  {$tabla['objeto']}\n";
				}
			}
			throw new toba_error($mensaje);
		}
	}

	function proyecto__namespace_toba()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion('/separar_texto_lineas\(/', 			'toba_texto::separar_texto_lineas(');
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);
	}


}
	
?>