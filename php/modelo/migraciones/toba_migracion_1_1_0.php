<?php

class toba_migracion_1_1_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN usar_vinculo			smallint		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_carpeta		varchar(60)		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_item			varchar(60)		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_popup		smallint		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_popup_param	varchar(100)	";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_target		varchar(40)		";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_celda		varchar(40)		";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN carga_dt		int4	";		
		$sql[] = "ALTER TABLE apex_clase ADD COLUMN objeto_dr_proyecto				varchar(15)	";
		$sql[] = "ALTER TABLE apex_clase ADD COLUMN objeto_dr						int4		";
		$sql[] = "ALTER TABLE apex_clase ADD COLUMN utiliza_fuente_datos			int4		";
		$sql[] = "ALTER TABLE apex_objeto_db_registros ADD COLUMN fuente_datos_proyecto			varchar(15)";		
		$sql[] = "ALTER TABLE apex_objeto_db_registros ADD COLUMN fuente_datos					varchar(20)";				
		$sql[] = "CREATE UNIQUE INDEX apex_objeto_dbr_uq_tabla ON apex_objeto_db_registros (tabla,fuente_datos_proyecto,fuente_datos)";
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
		$archivo = toba_dir().'/php/modelo/ddl/pgsql_a50_asistentes.sql';
		if (file_exists($archivo)) {
			$this->elemento->get_db()->ejecutar_archivo($archivo);
		}
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
	 * Para poder utilizar el unique de (tabla,fuente) es necesario replicar la info de la fuente en la tabla del datos_tabla
	 */
	function proyecto__dt_duplicacion_fuente()
	{
		$sql[] = "
			UPDATE apex_objeto_db_registros 
				SET 
					fuente_datos = (SELECT fuente_datos FROM apex_objeto WHERE 
											apex_objeto.proyecto = apex_objeto_db_registros.objeto_proyecto 
										AND apex_objeto.objeto = apex_objeto_db_registros.objeto ),
					fuente_datos_proyecto = (SELECT fuente_datos_proyecto FROM apex_objeto WHERE 
											apex_objeto.proyecto = apex_objeto_db_registros.objeto_proyecto 
										AND apex_objeto.objeto = apex_objeto_db_registros.objeto )
				WHERE
					objeto_proyecto = '{$this->elemento->get_id()}'
		";
		return $this->elemento->get_db()->ejecutar($sql);
	}
}
	
?>