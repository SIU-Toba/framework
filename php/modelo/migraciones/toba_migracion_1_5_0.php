<?php
class toba_migracion_1_5_0 extends toba_migracion
{
		function instancia__cambios_estructura()
		{
			/**
			 * Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because it has pending trigger events' de postgres 8.3
			 */
			$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
			$this->elemento->get_db()->ejecutar($sql);

			$sql = array();
			//------------- Nueva tabla para guardar el checksum de los proyectos, relacionado a la sincro_svn ---------
			$sql[] = 'CREATE TABLE			apex_checksum_proyectos
						(
							checksum						varchar(200)	NOT NULL,
							proyecto							varchar(15)		 NOT NULL,
							CONSTRAINT "apex_checksum_proyectos_pk" PRIMARY KEY ("proyecto"),
							CONSTRAINT "apex_checksum_proyectos_fk"	FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
						);';

			//--------------- Cambios para que los 'Vinculos' del cuadro ahora se disparen como eventos -----------------
			$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN evento_asociado BIGINT;';
			$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna ADD CONSTRAINT apex_col_cuadro_evento_asoc_fk FOREIGN KEY (objeto_cuadro_proyecto, evento_asociado)
							REFERENCES apex_objeto_eventos (proyecto,  evento_id) ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';

			//--------------------------------- Ahora se marcan los autovinculos de manera explicita --------------------------------
			$sql[] = 'ALTER TABLE apex_objeto_eventos ADD COLUMN es_autovinculo SMALLINT NOT NULL DEFAULT 0;';

			//--------------------------------- Cambio el tamaño de la columna indice ---------------------------------------------------
			$sql[] = 'ALTER TABLE apex_msg ALTER indice TYPE character varying(40);';
			$sql[] = 'ALTER TABLE apex_item_msg ALTER indice TYPE character varying(40);';
			$sql[] = 'ALTER TABLE apex_objeto_msg ALTER indice TYPE character varying(40);';

			//------------------------------------ Define si se puede actualizar automaticamente mediante wizard ----------------------
			$sql[] = 'ALTER TABLE apex_objeto_db_registros ADD COLUMN permite_actualizacion_automatica smallint NOT NULL DEFAULT 1;';

			//----------------------------------- Define la columna descripcion que se usara para una respuesta_popup ------------
			$sql[] = 'ALTER TABLE apex_objeto_cuadro ADD COLUMN columna_descripcion TEXT NULL;';

			//---------------------------------- Define si se muestra o no la leyenda del paginado -------------------------------------------
			$sql[] = 'ALTER TABLE apex_objeto_cuadro ADD COLUMN mostrar_total_registros SMALLINT NOT NULL DEFAULT 0;';
			$sql[] = 'ALTER TABLE apex_objeto_cuadro ADD COLUMN siempre_con_titulo SMALLINT NOT NULL DEFAULT 0;';


			//---------------------------------- Asociación N a N entre perfiles -------------------------------------------
			$sql[] = '
				CREATE TABLE apex_usuario_grupo_acc_miembros
				(	
					proyecto							varchar(15)		NOT NULL,
					usuario_grupo_acc					varchar(30)		NOT NULL,
					usuario_grupo_acc_pertenece			varchar(30)		NOT NULL
				
				);';
			//---------------------------------- Nuevo comportamiento del solo lectura del ef --------------------------------------------------
			$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN solo_lectura_modificacion SMALLINT NOT NULL DEFAULT 0;';

			$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN cascada_mantiene_estado	SMALLINT NOT NULL DEFAULT 0;';

			//--------------------------------- Agrega la posibilidad de terner permisos  por tablas -------------------------------------------
			$sql[] = 'ALTER TABLE apex_clase ADD COLUMN solicitud_tipo VARCHAR(20) NULL;';	

			$sql[] = 'ALTER TABLE apex_fuente_datos ADD COLUMN permisos_por_tabla SMALLINT NOT NULL DEFAULT 0;';
			
			$sql[] = '
				CREATE TABLE apex_item_permisos_tablas
				(
					proyecto						varchar(15)		NOT NULL,
					item							varchar(60)		NOT NULL,
					fuente_datos					varchar(20)		NOT NULL,
					tablas_modifica					TEXT			NULL,		
					CONSTRAINT	"apex_item_permisos_tablas_pk"	 	PRIMARY	KEY ("proyecto","item", "fuente_datos"),
					CONSTRAINT	"apex_item_permisos_tablas_item" 	FOREIGN	KEY ("proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
					CONSTRAINT  "apex_item_permisos_tablas_fuente"  FOREIGN KEY ("proyecto","fuente_datos") REFERENCES   "apex_fuente_datos" ("proyecto","fuente_datos") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
				);';

			//--------------------------------- Se agrega la posibilidad de usar web-services ---------------------------------------------------
			$sql[] = 'CREATE TABLE apex_servicio_web
							(
							  proyecto 				 VARCHAR(15)  	NOT NULL,
							  servicio_web           VARCHAR(50)  	NOT NULL,
							  descripcion			 TEXT		  	NULL,
							  param_to				 TEXT		  	NOT NULL,				
							  param_wsa				 SMALLINT		NOT NULL DEFAULT 0,		
							  CONSTRAINT "apex_servicio_web_pk" PRIMARY KEY("proyecto", "servicio_web"),
							  CONSTRAINT "apex_servicio_web_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto"("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
							);';

			$sql[] = 'CREATE TABLE apex_servicio_web_param
							(
							  proyecto 				 VARCHAR(15)  NOT NULL,
							  servicio_web           VARCHAR(50)  NOT NULL,
							  parametro				 TEXT		  NOT NULL,
							  valor					 TEXT		  NOT NULL,
							  CONSTRAINT "apex_servicio_web_param_pk" PRIMARY KEY("proyecto", "servicio_web", "parametro"),
							  CONSTRAINT "apex_servicio_web_param_fk_serv_web" FOREIGN KEY ("proyecto", "servicio_web") REFERENCES "apex_servicio_web"("proyecto", "servicio_web") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
							);';

			//--------------------------------Se corrige la clave de la tabla apex_relacion_tablas sino 2 proyectos con perfil datos pueden chocar --------------------
			$sql[] = 'ALTER TABLE apex_relacion_tablas DROP CONSTRAINT apex_relacion_tablas_pk;
						  ALTER TABLE apex_relacion_tablas ADD PRIMARY KEY (proyecto, relacion_tablas);';

			$sql[] = 'ALTER TABLE apex_estilo DROP CONSTRAINT apex_estilo_pk;
						   ALTER TABLE apex_estilo ADD PRIMARY KEY (estilo, proyecto);';

			//-------------------------------- Permite exportar restricciones personalizadas para no tener choques en produccion -----------------------------------------
			$sql[] = 'ALTER TABLE apex_usuario_grupo_acc ADD COLUMN permite_edicion SMALLINT NOT NULL DEFAULT 1;';
			$sql[] = 'ALTER TABLE apex_restriccion_funcional ADD COLUMN permite_edicion SMALLINT NOT NULL DEFAULT 1;';

			$sql[] = 'ALTER TABLE apex_objeto_ci_pantalla ADD COLUMN template_impresion TEXT NULL;';
			$sql[] = 'ALTER TABLE apex_objeto_ut_formulario ADD COLUMN template_impresion TEXT NULL;';

			//------------------------------------ --------------------Tablas donde se alojaran los gadgets --------------------------------------------------------------------
			$sql[] = "CREATE SEQUENCE apex_gadgets_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;";
			$sql[] = 'CREATE TABLE apex_gadgets
							(
							  gadget					 SERIAL NOT NULL,
							  proyecto				    VARCHAR(15) NOT NULL,
							  gadget_url			  VARCHAR(250) NULL,
							  titulo						 VARCHAR(50) NULL,
							  descripcion			 VARCHAR(250) NULL,
							  tipo_gadget			 CHAR(1) NOT NULL,
							  subclase				   VARCHAR(80) NULL,
							  subclase_archivo	VARCHAR(255) NULL,
							  CONSTRAINT "apex_gadget_pk" PRIMARY KEY ("proyecto", "gadget"),
							  CONSTRAINT "apex_usuario_proyecto_gadgets_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
							);';

			$sql[] = "CREATE TABLE apex_usuario_proyecto_gadgets
							(
							  usuario				  VARCHAR(60) NOT NULL,
							  proyecto				  VARCHAR(15) NOT NULL,
							  gadget				   INTEGER		NOT NULL,
							  orden						INTEGER		NOT NULL DEFAULT 1,
							  eliminable			CHAR(1) NOT NULL DEFAULT 'S',
							  CONSTRAINT \"apex_usuario_proyecto_gadgets_pk\" PRIMARY KEY (\"usuario\", \"proyecto\", \"gadget\"),
							  CONSTRAINT \"apex_usuario_proyecto_gadgets_fk_usuario\" FOREIGN KEY (\"usuario\") REFERENCES \"apex_usuario\" (\"usuario\") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
							  CONSTRAINT \"apex_usuario_proyecto_gadgets_fk_proyecto\" FOREIGN KEY (\"proyecto\") REFERENCES \"apex_proyecto\" (\"proyecto\") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
							  CONSTRAINT \"apex_usuario_proyecto_gadgets_fk_gadget\" FOREIGN KEY (\"proyecto\", \"gadget\") REFERENCES \"apex_gadgets\" (\"proyecto\", \"gadget\") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
							);";
			$this->elemento->get_db()->ejecutar($sql);

			$sql = "SET CONSTRAINTS ALL DEFERRED;";
			$this->elemento->get_db()->ejecutar($sql);
		}

		/**
		 * Se pasa la info de las columnas 'vinculo' a un evento que luego se asocia
		 */
		function proyecto__asociar_evento_vinculo()
		{
			$sql = "SELECT *
						 FROM apex_objeto_ei_cuadro_columna col
						WHERE
						col.usar_vinculo = 1 AND
						col.objeto_cuadro_proyecto = '{$this->elemento->get_id()}';";
			$datos = $this->elemento->get_db()->consultar($sql);

			if (! empty($datos)) {
				foreach($datos as $col) {
					$cuadro_id = $col['objeto_cuadro'];
					$columna =  $col['objeto_cuadro_col'];
					$nombre_evento = 'evt_'. $col['clave'];
					
					$sql_ins = "INSERT INTO apex_objeto_eventos
					(proyecto, objeto, identificador, maneja_datos, accion,en_botonera, 
					accion_vinculo_carpeta, accion_vinculo_item, es_autovinculo, accion_vinculo_popup,
					accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio)
					VALUES ('{$col['objeto_cuadro_proyecto']}', '{$col['objeto_cuadro']}', '$nombre_evento', 0, 'V', 0,";

					$sql_ins .= (isset($col['vinculo_carpeta'])) ?  "'{$col['vinculo_carpeta']}'," : 'NULL,';
					if (isset($col['vinculo_item'])) { //Seteo item y autovinculo en false
						$sql_ins .= "'{$col['vinculo_item']}', 0," ;
					} else { //Si no hay item es autovinculo
						$sql_ins .= 'NULL, 1,';
					}
					$sql_ins .= (isset($col['vinculo_popup'])) ?  "'{$col['vinculo_popup']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_popup_param'])) ?  "'{$col['vinculo_popup_param']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_target'])) ?  "'{$col['vinculo_target']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_celda'])) ?  "'{$col['vinculo_celda']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_servicio'])) ?  "'{$col['vinculo_servicio']}'" : 'NULL';
					$sql_ins .= ");";
					$this->elemento->get_db()->ejecutar($sql_ins);


					//Recupero la secuencia del evento
					$evt = $this->elemento->get_db()->recuperar_secuencia("apex_objeto_eventos_seq");
					
					$sql_up = "UPDATE apex_objeto_eventos a
										SET orden = (SELECT COALESCE(MAX(b.orden) + 1, 1)
																 FROM	apex_objeto_eventos b
																WHERE b.proyecto = a.proyecto AND b.objeto = a.objeto)
					WHERE	a.proyecto = '{$this->elemento->get_id()}' AND a.objeto = '{$col['objeto_cuadro']}'
					AND a.evento_id = '$evt';";
					$this->elemento->get_db()->ejecutar($sql_up);

					//Asocio el nuevo evento al cuadro y blanqueo las columnas en el mismo paso.
					$sql_up = "UPDATE apex_objeto_ei_cuadro_columna SET evento_asociado = '$evt'
					
					WHERE	objeto_cuadro_proyecto = '{$this->elemento->get_id()}'  AND
					objeto_cuadro = '$cuadro_id' AND objeto_cuadro_col = '$columna'; ";
					$this->elemento->get_db()->ejecutar($sql_up);
				}
			}
		}

		/**
		 * Se explicitan las segundas columnas de los cuadros con respuesta_popup
		 * como columnas descripcion
		 */
		function proyecto__explicitar_descripcion_respuesta_popup()
		{
			//Tengo que recuperar los que tienen accion = 'P'
			$sql = "SELECT	aoc.objeto_cuadro,
										    aoc.columnas_clave
						  FROM apex_objeto_eventos aoe
						  JOIN apex_objeto_cuadro aoc
						  ON aoe.proyecto = aoc.objeto_cuadro_proyecto AND aoe.objeto = aoc.objeto_cuadro
						  WHERE  aoe.proyecto = '{$this->elemento->get_id()}'  AND
										   aoe.accion = 'P'
						  GROUP BY aoc.objeto_cuadro, aoc.columnas_clave ;";
			$datos = $this->elemento->get_db()->consultar($sql);

			foreach($datos as $evento) {
				$columnas = explode(',' , $evento['columnas_clave']);		//Separo las columnas
				$largo = count($columnas);
				$col_desc = null;
				if (isset($columnas[$largo-1]) && $columnas[$largo-1] != '') {	//Si no hay error
					$col_desc = trim($columnas[$largo-1]);
					unset($columnas[$largo-1]);
					$columnas = implode(',' , array_map('trim', $columnas));
				}				
				if (! is_null($col_desc) && $columnas != '') {
					$sql = 'UPDATE apex_objeto_cuadro SET columna_descripcion = ' . $this->elemento->get_db()->quote($col_desc) .
					 ', columnas_clave = ' . $this->elemento->get_db()->quote($columnas);
					$sql .= " WHERE objeto_cuadro_proyecto = '{$this->elemento->get_id()}'  AND objeto_cuadro = '{$evento['objeto_cuadro']}'; ";
					$this->elemento->get_db()->ejecutar($sql);
				}
			}
		}
}
?>
