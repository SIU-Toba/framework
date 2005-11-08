<?
require_once("conversion_toba.php");

/**
*	--------------------------------------------------------------
*	 MIGRACION HACIA NUEVO MODELO DE EVENTOS DEFINIDOS EN EL ADMIN
*	--------------------------------------------------------------
*/
class conversion_0_8_3 extends conversion_toba
{

	function get_version()
	{
		return "0.8.3";	
	}

	function pre_cambios()
	{
		$sql = "DELETE FROM apex_objeto_ci_pantalla WHERE objeto_ci_proyecto='{$this->proyecto}'";
		$this->ejecutar_sql($sql,"instancia");		
		$sql = "DELETE FROM apex_objeto_ei_cuadro_columna WHERE objeto_cuadro_proyecto='{$this->proyecto}'";
		$this->ejecutar_sql($sql,"instancia");		
		$sql = "DELETE FROM apex_objeto_ei_formulario_ef WHERE objeto_ei_formulario_proyecto='{$this->proyecto}'";
		$this->ejecutar_sql($sql,"instancia");
		$sql = "DELETE FROM apex_objeto_eventos WHERE proyecto='{$this->proyecto}'";
		$this->ejecutar_sql($sql,"instancia");
	}
	
	function post_cambios()
	{	

		$sql = "UPDATE apex_objeto_cuadro SET ev_seleccion = NULL, ev_eliminar = NULL
				WHERE objeto_cuadro_proyecto='{$this->proyecto}'";
		$this->ejecutar_sql($sql,"instancia");
		
		$sql = "UPDATE apex_objeto_ut_formulario SET ev_agregar = NULL, ev_agregar_etiq = NULL, ev_mod_modificar = NULL,
													ev_mod_modificar_etiq = NULL, ev_mod_eliminar_etiq = NULL, 
													ev_mod_limpiar_etiq = NULL
				WHERE objeto_ut_formulario_proyecto='{$this->proyecto}'";
		$this->ejecutar_sql($sql,"instancia");
		
		//Estos DROP no se pueden hacer sino fallan las meta-consultas posteriores
		//Para borrar estas columnas hay que esperar una version posterior y sacarlas de los pg_*.sql
/*
		$sql = "ALTER TABLE apex_objeto_cuadro DROP COLUMN ev_seleccion";
		$this->ejecutar_sql($sql,"instancia");
		$sql = "ALTER TABLE apex_objeto_cuadro DROP COLUMN ev_eliminar";
		$this->ejecutar_sql($sql,"instancia");
		
		//Columnas del formulario
		$columnas = array("ev_agregar", "ev_agregar_etiq", "ev_mod_modificar", "ev_mod_modificar_etiq",  
						  "ev_mod_eliminar_etiq", "ev_mod_limpiar_etiq");
		foreach ($columnas as $columna) {
			$sql = "ALTER TABLE apex_objeto_ut_formulario DROP COLUMN $columna";
			$this->ejecutar_sql($sql,"instancia");
		} 
*/
	}
	
	/**
		Existe una suposicion respecto de cuando se usan claves de db_registros
		(creo que cuando se marca el evento seleccion pero no se especifica una clave)
	*/
	function cambio_ei_cuadro_clave_dbr()
	{
	}
	
	/**
		Migracion de db_registros a objetos_toba.
		Pueden buscarse todos los db_registros que se usan en los patrones abms,
		para levantar los datos y crear objetos toba?
	*/		
	function cambio_db_registros()
	{

	}
	
	
	/**
		Crear los eventos que esten seleccionados 
	*/
	function cambio_migrar_definicion_eventos_ei_cuadro()
	{
		//Evento SELECCION
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					 ,
				orden
			)
			SELECT
				c.objeto_cuadro_proyecto,
				c.objeto_cuadro,
				'seleccion',
				NULL,
				0,
				1,
				'',
				NULL,
				'apex',
				'doc.gif',
				0,
				'Seleccionar la fila',
				1
			FROM
				apex_objeto_cuadro c
			WHERE
				c.ev_seleccion = 1 AND
				c.objeto_cuadro_proyecto='{$this->proyecto}'
		";
		$this->ejecutar_sql($sql,"instancia");
		
		//Evento ELIMINAR
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					 ,
				orden
			)
			SELECT
				c.objeto_cuadro_proyecto,
				c.objeto_cuadro,
				'baja',
				NULL,
				0,
				1,
				'¿Está seguro que desea ELIMINAR la fila?',
				NULL,
				'apex',
				'borrar.gif',
				0,
				'Borra el contenido de la fila actual',
				2
			FROM
				apex_objeto_cuadro c
			WHERE
				c.ev_eliminar = 1 AND
				c.objeto_cuadro_proyecto='{$this->proyecto}'
		";
		$this->ejecutar_sql($sql,"instancia");		
	}

	/**
		Crear los eventos que esten seleccionados 
	*/
	function cambio_migrar_definicion_eventos_ei_filtro()
	{
		//Evento FILTRAR
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					,
				orden
			)
			SELECT
				f.objeto_ut_formulario_proyecto,
				f.objeto_ut_formulario,
				'filtrar',
				COALESCE(f.ev_agregar_etiq,'&Filtrar'),
				1,
				0,
				'',
				'abm-input-eliminar',
				NULL,
				NULL,
				1,
				'',
				1
			FROM
				apex_objeto_ut_formulario f,
				apex_objeto o
			WHERE
				f.objeto_ut_formulario_proyecto='{$this->proyecto}' AND
				f.objeto_ut_formulario_proyecto = o.proyecto AND
				f.objeto_ut_formulario = o.objeto AND
				o.clase = 'objeto_ei_filtro' AND
				f.ev_agregar = 1
		";
		$this->ejecutar_sql($sql,"instancia");
		
		//Evento CANCELAR
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					 ,
				orden
			)
			SELECT
				f.objeto_ut_formulario_proyecto,
				f.objeto_ut_formulario,
				'cancelar',
				COALESCE(f.ev_mod_limpiar_etiq,'&Cancelar'),
				0,
				0,
				'',
				'abm-input',
				NULL,
				NULL,
				1,
				'',
				2
			FROM
				apex_objeto_ut_formulario f,
				apex_objeto o
			WHERE
				f.objeto_ut_formulario_proyecto='{$this->proyecto}' AND			
				f.objeto_ut_formulario_proyecto = o.proyecto AND
				f.objeto_ut_formulario = o.objeto AND
				o.clase = 'objeto_ei_filtro' AND
                f.ev_mod_limpiar = 1
		";
		$this->ejecutar_sql($sql,"instancia");		
	}

	/**
		Crear los eventos que esten seleccionados 
	*/
	function cambio_migrar_definicion_eventos_ei_formulario()
	{
		//Evento ALTA
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					
			)
			SELECT
				f.objeto_ut_formulario_proyecto,
				f.objeto_ut_formulario,
				'alta',
				COALESCE(f.ev_agregar_etiq,'&Agregar'),
				1,
				0,
				'',
				'abm-input',
				NULL,
				NULL,
				1,
				''
			FROM
				apex_objeto_ut_formulario f,
				apex_objeto o
			WHERE
				f.objeto_ut_formulario_proyecto='{$this->proyecto}' AND
				f.objeto_ut_formulario_proyecto = o.proyecto AND
				f.objeto_ut_formulario = o.objeto AND
				o.clase = 'objeto_ei_formulario' AND
				f.ev_agregar = 1
		";
		$this->ejecutar_sql($sql,"instancia");		
		
		
		//Evento MODIFICACION del ML y el común
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					,
				orden
			)
			SELECT
				f.objeto_ut_formulario_proyecto,
				f.objeto_ut_formulario,
				'modificacion',
				COALESCE(f.ev_mod_modificar_etiq,'&Modificar'),
				1,
				0,
				'',
				'abm-input',
				NULL,
				NULL,
				1,
				'',
				2
			FROM
				apex_objeto_ut_formulario f,
				apex_objeto o
			WHERE
				f.objeto_ut_formulario_proyecto='{$this->proyecto}' AND
				f.objeto_ut_formulario_proyecto = o.proyecto AND
				f.objeto_ut_formulario = o.objeto AND
				o.clase IN ('objeto_ei_formulario', 'objeto_ei_formulario_ml') AND
				f.ev_mod_modificar = 1
		";
		$this->ejecutar_sql($sql,"instancia");		
		
		//Evento BAJA
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					,
				orden
			)
			SELECT
				f.objeto_ut_formulario_proyecto,
				f.objeto_ut_formulario,
				'baja',
				COALESCE(f.ev_mod_eliminar_etiq,'&Eliminar'),
				0,
				0,
				'¿Desea ELIMINAR el registro?',
				'abm-input',
				NULL,
				NULL,
				1,
				'',
				1
			FROM
				apex_objeto_ut_formulario f,
				apex_objeto o
			WHERE
				f.objeto_ut_formulario_proyecto='{$this->proyecto}' AND			
				f.objeto_ut_formulario_proyecto = o.proyecto AND
				f.objeto_ut_formulario = o.objeto AND
				o.clase = 'objeto_ei_formulario' AND
				f.ev_mod_eliminar = 1
		";
		$this->ejecutar_sql($sql,"instancia");			
		
		//Evento CANCELAR
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda					,
				orden
			)
			SELECT
				f.objeto_ut_formulario_proyecto,
				f.objeto_ut_formulario,
				'cancelar',
				COALESCE(f.ev_mod_limpiar_etiq,'&Cancelar'),
				0,
				0,
				'',
				'abm-input',
				NULL,
				NULL,
				1,
				'',
				3
			FROM
				apex_objeto_ut_formulario f,
				apex_objeto o
			WHERE
				f.objeto_ut_formulario_proyecto='{$this->proyecto}' AND			
				f.objeto_ut_formulario_proyecto = o.proyecto AND
				f.objeto_ut_formulario = o.objeto AND
				o.clase = 'objeto_ei_formulario' AND
                f.ev_mod_limpiar = 1
		";
		$this->ejecutar_sql($sql,"instancia");		
		
		//Evento SELECCION del ML
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda
			)
			SELECT
				f.objeto_ut_formulario_proyecto,
				f.objeto_ut_formulario,
				'seleccion',
				NULL,
				1,
				1,
				'',
				NULL,
				'apex',
				'doc.gif',
				0,
				'Seleccionar la fila'
			FROM
				apex_objeto_ut_formulario f,
				apex_objeto o
			WHERE
				f.objeto_ut_formulario_proyecto='{$this->proyecto}' AND			
				f.objeto_ut_formulario_proyecto = o.proyecto AND
				f.objeto_ut_formulario = o.objeto AND
				o.clase = 'objeto_ei_formulario_ml' AND
                f.ev_seleccion = 1
		";
		$this->ejecutar_sql($sql,"instancia");
		
	}

	/**
	*	Migra las columnas desde objeto_cuadro a objeto_ei_cuadro
	*/
	function cambio_migrar_tabla_ei_cuadro()
	{
		$sql = "INSERT INTO  apex_objeto_ei_cuadro_columna 
				(
					objeto_cuadro_proyecto	,
					objeto_cuadro         	,
					clave          			,	
					orden				  	,
					titulo                	,
					estilo    				,	
					ancho					,	
					formateo   				,	
					vinculo_indice	      	,
					no_ordenar				,	
					mostrar_xls				,	
					mostrar_pdf				,	
					pdf_propiedades       	,
					desabilitado			,	
					total			
				)
				SELECT 	
					x.objeto_cuadro_proyecto,
					x.objeto_cuadro         ,
					x.valor_sql      		,	
					x.orden				  	,
					x.titulo                ,
					x.columna_estilo  		,	
					x.columna_ancho			,	
					x.valor_sql_formato		,	
					x.vinculo_indice	    ,
					x.no_ordenar			,	
					x.mostrar_xls			,	
					x.mostrar_pdf			,	
					x.pdf_propiedades       ,
					x.desabilitado			,	
					x.total			
				FROM apex_objeto o,
				apex_objeto_cuadro_columna x                       
				WHERE 
					x.objeto_cuadro_proyecto='{$this->proyecto}'
				AND o.objeto = x.objeto_cuadro                       
				AND o.proyecto = x.objeto_cuadro_proyecto               
				AND x.valor_sql IS NOT NULL
				AND o.clase = 'objeto_ei_cuadro';";	
		$this->ejecutar_sql($sql,"instancia");	
		
		//Se borran los registros viejos
		$sql = "
				DELETE FROM apex_objeto_cuadro_columna
				WHERE
					objeto_cuadro_proyecto='{$this->proyecto}' AND
					objeto_cuadro IN 
					(
						SELECT objeto FROM apex_objeto 
						WHERE 
							clase IN ('objeto_ei_cuadro') AND
							proyecto='{$this->proyecto}'
					)
		";
		$this->ejecutar_sql($sql,"instancia");		
	}
	
	/**
	*	Migra los efs desde objeto_ut_formulario a objeto_ei_formulario
	*/
	function cambio_migrar_tabla_ei_formulario_ef()
	{
		$sql = "INSERT INTO  apex_objeto_ei_formulario_ef
				(
					objeto_ei_formulario_proyecto   , 	
					objeto_ei_formulario            , 	
					identificador      				,	
					elemento_formulario     		,	
					columnas                		,	
					obligatorio             		,		
					inicializacion          		,	
					orden                   		,	
					etiqueta                		,	
					descripcion             		,	
					colapsado						,	
					desactivado             		,	
					estilo   				 		,	
					total								
				)
				SELECT 	
					x.objeto_ut_formulario_proyecto   , 	
					x.objeto_ut_formulario            , 	
					x.identificador      				,	
					x.elemento_formulario     		,	
					x.columnas                		,	
					x.obligatorio             		,		
					x.inicializacion          		,	
					x.orden                   		,	
					x.etiqueta                		,	
					x.descripcion             		,	
					x.colapsado						,	
					x.desactivado             		,	
					x.lista_columna_estilo	 		,	
					x.total								
				FROM apex_objeto o,
				apex_objeto_ut_formulario_ef x                       
				WHERE x.objeto_ut_formulario_proyecto='{$this->proyecto}'
				AND o.objeto = x.objeto_ut_formulario                       
				AND o.proyecto = x.objeto_ut_formulario_proyecto               
				AND o.clase IN ('objeto_ei_formulario','objeto_ei_formulario_ml','objeto_ei_filtro');";
		$this->ejecutar_sql($sql,"instancia");		
		
		
		//Se borran los registros viejos
		$sql = "
				DELETE FROM apex_objeto_ut_formulario_ef
				WHERE 
					objeto_ut_formulario_proyecto='{$this->proyecto}' AND
					objeto_ut_formulario IN 
						(
							SELECT objeto FROM apex_objeto 
							WHERE 
								clase IN ('objeto_ei_formulario','objeto_ei_formulario_ml','objeto_ei_filtro') AND
								proyecto = '{$this->proyecto}'
							
						) 
				";
		$this->ejecutar_sql($sql,"instancia");					
	}
	
	
	/**
		Crear los eventos que esten seleccionados
	*/
	function cambio_migrar_definicion_eventos_ci()
	{
		//Eventos PROCESAR
		$sql = "
			INSERT INTO apex_objeto_eventos
			(
				proyecto                 ,
				objeto                   ,
				identificador            ,
				etiqueta                 ,
				maneja_datos             ,
				sobre_fila               ,
				confirmacion             ,
				estilo                   ,
				imagen_recurso_origen    ,
				imagen                   ,
				en_botonera              ,
				ayuda
			)
			SELECT DISTINCT
			      ci.objeto_mt_me_proyecto,
			      ci.objeto_mt_me,
			      CASE
			          WHEN pan.ev_procesar = 1 THEN 'procesar'
			          ELSE 'cancelar'
                  END,
                  CASE WHEN pan.ev_procesar = 1
                       THEN COALESCE(ci.ev_procesar_etiq, 'Proce&sar')
                       ELSE COALESCE(ci.ev_cancelar_etiq, '&Cancelar')
                  END,
				  CASE WHEN pan.ev_procesar = 1
				  	   THEN 1
					   ELSE 0
                   END,
				  0,
				  NULL,
				  NULL,
				  NULL,
				  NULL,
				  1,
				  ''
			FROM
			      apex_objeto_mt_me ci
				  	LEFT OUTER JOIN	apex_objeto_mt_me_etapa pan
						ON (ci.objeto_mt_me = pan.objeto_mt_me AND
							ci.objeto_mt_me_proyecto = pan.objeto_mt_me_proyecto)
             WHERE
			 	  ci.objeto_mt_me_proyecto='{$this->proyecto}' AND
                  pan.ev_procesar = 1 OR
                  pan.ev_cancelar = 1
		";
		$this->ejecutar_sql($sql,"instancia");
	}	

	/**
	* 	Migra las etapas del viejo objeto_mt_me a la tabla objeto_ci_pantalla
	*/
	function cambio_migrar_tabla_ci_pantalla()
	{
		$sql = "INSERT INTO  apex_objeto_ci_pantalla
				(
					objeto_ci_proyecto			,
					objeto_ci					,
					identificador				,
					orden						,
					etiqueta					,
					descripcion					,
					tip							,
					imagen_recurso_origen		,
					imagen						,
					objetos						,
					eventos						
				)
				SELECT 	
					x.objeto_mt_me_proyecto		,
					x.objeto_mt_me				,
					x.posicion					,
					CAST(x.posicion	AS smallint),
					x.etiqueta					,
					x.descripcion					,
					x.tip							,
					x.imagen_recurso_origen		,
					x.imagen						,
					x.objetos,
					CASE
					WHEN (COALESCE(x.ev_procesar, 0) || COALESCE(x.ev_cancelar,0)) = '10'
					     THEN 'procesar'
					WHEN (COALESCE(x.ev_procesar, 0) || COALESCE(x.ev_cancelar,0)) = '01'
					     THEN 'cancelar'
					WHEN (COALESCE(x.ev_procesar, 0) || COALESCE(x.ev_cancelar,0)) = '11'
					     THEN 'procesar,cancelar'		
					ELSE ''			
					END
				FROM apex_objeto o,
				apex_objeto_mt_me_etapa x
				WHERE x.objeto_mt_me_proyecto='{$this->proyecto}'
				AND o.objeto = x.objeto_mt_me
				AND o.proyecto = x.objeto_mt_me_proyecto
				AND o.clase IN ('objeto_ci','ci_cn','ci_abm_dbr','objeto_ci_abm','ci_abm_dbt','ci_abm_nav');";
		$this->ejecutar_sql($sql,"instancia");
		$sql = "
			DELETE FROM apex_objeto_mt_me_etapa
			WHERE 
				objeto_mt_me IN 
					(
						SELECT objeto FROM apex_objeto 
						WHERE 
							clase IN ('objeto_ci','objeto_ci_abm','ci_cn','ci_abm_dbr','ci_abm_dbt','ci_abm_nav') AND
							proyecto='{$this->proyecto}'
					) AND
				objeto_mt_me_proyecto='{$this->proyecto}' ";
		$this->ejecutar_sql($sql,"instancia");		
		//Las etiquetas vacias de las pantallas pasan a llamarse igual que los identificadores
		$sql = "UPDATE apex_objeto_ci_pantalla
				SET etiqueta = identificador
				WHERE etiqueta IS NULL
				AND objeto_ci_proyecto = '{$this->proyecto}';";
		$this->ejecutar_sql($sql,"instancia");		
	}
}
?>