<?php
class toba_migracion_2_2_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();
		
		//Cambio el tipo de la columna estilo del ef y quito la FK
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef DROP CONSTRAINT apex_ei_f_ef_fk_estilo;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef DROP COLUMN estilo;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN estilo text;';
		//Agrego una columna estilo al ei_filtro
		$sql[] = 'ALTER TABLE apex_objeto_ei_filtro_col ADD COLUMN estilo text;';
		
		//Cambio el tipo de la columna estilo del ef y quito la FK
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna DROP CONSTRAINT apex_obj_ei_cuadro_fk_estilo;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna RENAME estilo TO estilo_temp;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN estilo text;';
		
		//Actualizo la columna basado en el valor CSS de la otra tabla
		$sql[] = 'UPDATE apex_objeto_ei_cuadro_columna SET estilo = (SELECT css FROM apex_columna_estilo WHERE columna_estilo = estilo_temp);';
		
		//Elimino la columna temporal
		$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna DROP COLUMN estilo_temp;';
		
		//Agrego al ei_filtro una columna de tipo hora y fecha_hora
		$sql[] = "INSERT INTO apex_objeto_ei_filtro_tipo_col (tipo_col, descripcion, proyecto) VALUES ('hora', 'Hora', 'toba');";
		$sql[] = "INSERT INTO apex_objeto_ei_filtro_tipo_col (tipo_col, descripcion, proyecto) VALUES ('fecha_hora', ' Fecha y Hora', 'toba');";		
		
		//Tabla para guardar los schemas que pertenecen a la fuente
		$sql[] = 'CREATE TABLE apex_fuente_datos_schemas
				(
					proyecto			VARCHAR(15)	NOT NULL,
					fuente_datos		VARCHAR(15)	NOT NULL, 
					nombre			TEXT		NOT NULL,
					principal			SMALLINT	NOT NULL DEFAULT 0,
					CONSTRAINT	"apex_fuente_datos_schemas_pk" PRIMARY KEY ("proyecto", "fuente_datos", "nombre"),
					CONSTRAINT	"apex_fuente_datos_schemas_fk_fuente" FOREIGN KEY ("proyecto", "fuente_datos") REFERENCES "apex_fuente_datos" ("proyecto", "fuente_datos") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
				);';
		
		$sql[] = 'ALTER TABLE apex_objeto_db_registros ADD COLUMN esquema TEXT NULL;';
		$sql[] = 'ALTER TABLE apex_objeto_db_registros ADD CONSTRAINT "apex_objeto_fk_fuente_schemas" FOREIGN KEY ("objeto_proyecto", "fuente_datos", "esquema") REFERENCES "apex_fuente_datos_schemas" ("proyecto", "fuente_datos", "nombre") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		//Tabla para guardar las claves utilizadas anteriormente
		$sql[] = 'CREATE SEQUENCE apex_usuario_pwd_usados_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;';
		$sql[] = 'CREATE TABLE apex_usuario_pwd_usados
				(
					cod_pwd_pasados		int8 DEFAULT nextval(\'"apex_usuario_pwd_usados_seq"\'::text) NOT NULL, 
					usuario		VARCHAR(60)		NOT NULL, 
					clave		VARCHAR(128)	NOT NULL, 
					algoritmo		VARCHAR(10)		NOT NULL,
					CONSTRAINT	apex_usuario_pwd_usados_pk PRIMARY KEY (cod_pwd_pasados), 
					CONSTRAINT	apex_usuario_pwd_usados_fk_usuario FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE,
					CONSTRAINT apex_usuario_pwd_usados_uk UNIQUE (usuario, clave)
				);';
		//SP + Trigger que se encarga de hacer la copia
		$sql[] = 'CREATE OR REPLACE FUNCTION sp_old_pwd_copy()
				  RETURNS trigger AS
				$BODY$
								DECLARE
								BEGIN
									IF (TG_OP = \'INSERT\') OR (TG_OP = \'DELETE\') THEN
										RAISE EXCEPTION \'Error en la programación del trigger\';
									END IF;

									IF (OLD.clave != NEW.clave) OR (OLD.autentificacion != NEW.autentificacion) THEN
										INSERT INTO apex_usuario_pwd_usados (usuario, clave, algoritmo) VALUES (OLD.usuario, OLD.clave, OLD.autentificacion);
									END IF;
									RETURN NULL;
								END;
							$BODY$
				  LANGUAGE plpgsql VOLATILE
				  COST 100;';
		
		$sql[] = 'CREATE TRIGGER tusuario_pwd_pasados
				  AFTER UPDATE
				  ON apex_usuario
				  FOR EACH ROW
				  EXECUTE PROCEDURE sp_old_pwd_copy();';

		//Tabla para incluir sets de prueba para los perfiles de datos.
		$sql[] = 'CREATE TABLE apex_perfil_datos_set_prueba
				(		
					proyecto					varchar(15)		NOT NULL,
					fuente_datos				varchar(20)		NOT NULL,
					lote						TEXT			NULL,
					seleccionados				TEXT			NULL, 
					parametros				TEXT			NULL,
					CONSTRAINT	"apex_perfil_datos_set_prueba_pk" PRIMARY KEY ("proyecto","fuente_datos"),
					CONSTRAINT	"apex_perfil_datos_set_prueba_fk_fuente" FOREIGN KEY ("proyecto", "fuente_datos") REFERENCES "apex_fuente_datos" ("proyecto", "fuente_datos") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
				);';
		
		$sql[] = 'ALTER TABLE apex_proyecto ALTER COLUMN validacion_intentos_min SET DEFAULT 5;';
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "SET CONSTRAINTS ALL DEFERRED;";
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	function instancia__migracion_tablas_log()
	{
		$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
		$this->elemento->get_db()->ejecutar($sql);

		$this->manejador_interface->mensaje('Migrando las tablas de logs.', false);
			
		//Hay que crear el schema para las tablas de log
		$sql[] = 'CREATE SCHEMA toba_logs;';
		
		//Recupero las tablas de log 
		$log_instancia = toba_db_tablas_instancia::get_lista_global_log();
		$log_proyecto = toba_db_tablas_instancia::get_lista_proyecto_log();
		$tablas = array_merge($log_instancia, $log_proyecto);
		
		$secuencias = $this->elemento->get_db()->get_secuencia_tablas($tablas);
		foreach($secuencias as $tabla => $secuencia) {									//Genero las SQL para mover las tablas de schema
			$sql[] = 'ALTER TABLE '. $tabla . ' SET SCHEMA toba_logs;' ;
			$sql[] = 'ALTER SEQUENCE '. $secuencia . ' SET SCHEMA toba_logs;' ;
			$sql[] = 'ALTER TABLE toba_logs.' . $tabla . " ALTER COLUMN log_objeto SET DEFAULT nextval(('toba_logs.\"$secuencia\"'::text)::regclass);";
			$this->manejador_interface->progreso_avanzar();
		}
		$this->elemento->get_db()->ejecutar($sql);		

		$sql = "SET CONSTRAINTS ALL DEFERRED;";
		$this->elemento->get_db()->ejecutar($sql);
		
		//Despues hay que borrar todos los archivos de log que existen actualmente
		$instancia = $this->elemento->eliminar_archivos_log();
	}
	
	function instancia__setear_ventana_tiempo_default ()
	{	//Pongo 5 minutos como tiempo minimo de ventana para errores de login
		$sql[] = 'UPDATE apex_proyecto SET validacion_intentos_min = 5 WHERE validacion_intentos_min IS NULL;';		
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	function proyecto__convertir_preguntas_secretas()
	{
		$clave = $this->elemento->get_instalacion()->get_claves_encriptacion();			//Obtengo las claves con las que voy a encriptar
		
		$sql = 'SELECT cod_pregunta_secreta, pregunta, respuesta FROM apex_usuario_pregunta_secreta;';
		$preguntas = $this->elemento->get_db()->consultar($sql);
		if (! empty($preguntas)) {													//Si se recuperaron preguntas/respuestas secretas
			$sqls = array();
			foreach($preguntas as $dato) {
				$id = $dato['cod_pregunta_secreta'];
				$preg = mcrypt_encrypt(MCRYPT_BLOWFISH, $clave['get'], $dato['pregunta'], MCRYPT_MODE_CBC, substr($clave['db'],0,8));	
				$resp = mcrypt_encrypt(MCRYPT_BLOWFISH, $clave['get'], $dato['respuesta'], MCRYPT_MODE_CBC, substr($clave['db'],0,8));	
			
				$sqls[] = "UPDATE apex_usuario_pregunta_secreta SET pregunta = '$preg', respuesta = '$resp' WHERE cod_pregunta_secreta = '$id';";	//Encripto y armo la SQL correspondiente
			}
			if (! empty($sqls)) {
				$this->elemento->get_db()->ejecutar($sqls);
			}
		}		
	}

	function proyecto__migrar_lote_pruebas_perfil_datos()
	{
		$db = $this->elemento->get_db();
		$proyecto = $this->elemento->get_id();
		$sql = "SELECT fuente_datos, usuario, clave, base FROM apex_fuente_datos WHERE usuario ILIKE '%select%' OR  usuario ILIKE '%FROM%' ;" ;
		$fuentes_a_migrar = $db->consultar($sql);
		if (! empty($fuentes_a_migrar)) {			
			foreach ($fuentes_a_migrar as $fuente) {
				//Inserto el registro correspondiente en la tabla nueva
				$sql_in = 'INSERT INTO apex_perfil_datos_set_prueba (proyecto, fuente_datos, lote, seleccionados, parametros) VALUES (';
				$sql_in .= $db->quote($proyecto) . ',' .  $db->quote($fuente['fuente_datos']) .',';
				$sql_in .= $db->quote($fuente['usuario']). ',' . $db->quote($fuente['clave']) . ',' . $db->quote($fuente['base']) . ');';
				$db->ejecutar($sql_in);
				
				//Elimino los valores de la otra tabla, no incide ya que se sacan de bases.ini los reales
				$sql_up = 'UPDATE apex_fuente_datos SET usuario = NULL, clave = NULL, base = NULL WHERE proyecto = '. $db->quote($proyecto) . ' AND fuente_datos = ' . $db->quote($fuente['fuente_datos']). ';' ;
				$db->ejecutar($sql_up);
			}
		}
	}
	
	function proyecto__alerta_fuente_datos()
	{
		//--- Esta alerta esta xq no nos podemos conectar a la fuente de datos de negocio para obtener los schemas existentes, asi que se debe configurar manualmente desde el editor.
		$msg1 = 'ATENCION!!!! : ';
		$this->manejador_interface->mensaje($msg1, true);
		$msg = 'Por favor edite las fuentes de datos del proyecto y agregue los schemas que considere necesarios para su trabajo.';
		$this->manejador_interface->mensaje($msg, true);		
	}	
}
?>
